<?php

namespace App\Http\Controllers\API;

use App\Mail\WelcomeMail;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Profile;
use App\Rules\PasswordRule;
use Illuminate\Support\Str;

use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\API\BaseController as BaseController;

class AuthController extends BaseController
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function isVendor(){
        $user = auth()->user();
        if(!$user){
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if($user->role !== 'vendor'){
            return response()->json(['message' => 'Forbidden. Not a vendor.'], 403);
        }
        return response()->json(['user' => [
            'id' => $user->vendor->id,
            'name' => $user->vendor->business_name,
        ]]);
    }

    /** Register a User.
     * @return \Illuminate\Http\JsonResponse */
    public function register(Request $request, $slug='user') {

       $messages = [
            'name.required' => 'Por favor ingresa tu nombre completo.',
            'email.required' => 'El correo electrónico es requerido para crear tu cuenta.',
            'email.email' => 'Por favor ingresa un correo electrónico válido.',
            'password.required' => 'Debes crear una contraseña para tu cuenta.',
            'c_password.required' => 'Por favor confirma tu contraseña.',
            'c_password.same' => 'Las contraseñas no coinciden. Por favor verifícalas.',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ], $messages);
     
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación: todos los campos son obligatorios y las contraseñas deben coincidir.'
            ], 422);
            // English meaning: "Validation error: all fields are required and the passwords must match."
        }
        if (User::withTrashed()->where('email', $request->email)->exists()) {
            return response()->json([
                'success'=> (bool)true,
                'message' => 'recovery-needed',
                'duplicate' => User::onlyTrashed()->where('email', $request->email)->exists(),
            ], 200);
            // English: "The email address is already registered."
        }
        
        try {
        
            $role = User::getRole($slug);

            $input = $request->only(['name','email','password']);
            $input['password'] = bcrypt($input['password']);

            $user = User::create($input);
            $user->role = $role;
            $user->status = 0;
            $user->save();
            
            if (!$user->trashed()) {
                $otp = $this->otpService->generateOtp($request->email);
            }

            // Store the token and expiry time in the database
            $user->password_reset_otp = $otp;
            $user->password_reset_otp_is_verified = false;
            $user->password_reset_otp_expiry = now()->addMinutes( $this->otpService->getTtl_min_time());  
            $user->save();

            if (!$user->trashed()) {
                $this->otpService->sendOtpEmail($request->email, $otp);
            }

            $profile = Profile::where('user_id', $user->id)->first(); 
            
            $profile->phone = $request->input('phone');
            $profile->address = $request->input('address');
            $profile->is_customer = 1;
            
            if($request->hasFile('avatar')){
                $profile->avatar = fileUpload($request->file('avatar'), 'avatars');
                $user->avatar = $profile->avatar;
                $user->save();
            }

            $profile->save();

            if($request->timezone){
                $user->config->update([
                    'give_notify_email' => true,
                    'give_booking_reminder' => true,
                    'timezone' => in_array($request->timezone, ['Europe/Madrid','Atlantic/Canary']) ? $request->timezone :'Europe/Madrid',
                ]);
            }

            $token = auth('api')->login($user);

            $cookie = cookie(
                'jwt_token',
                $token,
                1440*30,           // 24 hours in minutes x 30
                '/',            // path
                null,           // domain (null = current domain)
                false,          // secure (false for http://localhost)
                false,          // httpOnly = FALSE so JS can read it
                false,          // raw
                'lax'           // sameSite
            );
            
            Mail::to($user->email)->send(new WelcomeMail($user));

            return response()->json([
                'message' => 'Envío de OTP a tu correo electrónico',
                'success'=> true,
                'user' => [
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                // 'token'=> $token,
            ])->withCookie($cookie);
        }
        catch (Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }
  
    
    public function vendor(Request $request) {
        
        $messages = [
            // Name validation
            'name.required' => 'El nombre es obligatorio.',
            
            // Email validation
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            
            // Password validation
            'password.required' => 'La contraseña es obligatoria.',
            'c_password.required' => 'La confirmación de contraseña es obligatoria.',
            'c_password.same' => 'La confirmación de contraseña no coincide con la contraseña.',
            
            // Phone validation
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.string' => 'El teléfono debe ser una cadena de texto válida.',
            
            // Address validation
            'street.required' => 'La calle es obligatoria.',
            'city.required' => 'La ciudad es obligatoria.',
            'state.required' => 'El estado es obligatorio.',
            'zipcode.required' => 'El código postal es obligatorio.',
            
            // Business validation
            'business_name.required' => 'El nombre del negocio es obligatorio.',
            'business_name.max' => 'El nombre del negocio no puede tener más de 255 caracteres.',
            'business_type.required' => 'El tipo de negocio es obligatorio.',
            'business_type.max' => 'El tipo de negocio no puede tener más de 255 caracteres.',
            'business_bio.required' => 'La biografía del negocio es obligatoria.',
            'business_bio.string' => 'La biografía del negocio debe ser una cadena de texto válida.',
            
            // Generic fallback messages
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
            'same' => 'El campo :attribute y :other deben coincidir.',
            
            // Custom attribute names for better readability
            'attributes' => [
                'name' => 'nombre',
                'email' => 'correo electrónico',
                'password' => 'contraseña',
                'c_password' => 'confirmación de contraseña',
                'phone' => 'teléfono',
                'street' => 'calle',
                'city' => 'ciudad',
                'state' => 'estado',
                'zipcode' => 'código postal',
                'business_name' => 'nombre del negocio',
                'business_type' => 'tipo de negocio',
                'business_bio' => 'biografía del negocio',
                'logo' => 'logo',
            ]
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'phone' => 'required|string',
            'street'=> 'required',
            'city'=> 'required',
            'state'=> 'required',
            'zipcode'=> 'required',
            'business_name' => 'required|max:255',
            'business_type' => 'required|max:255',
            'business_bio'=> 'required|string',
            'logo'=> 'nullable',
        ], $messages);

        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación: todos los campos son obligatorios y las contraseñas deben coincidir.'
            ], 422);
            // English meaning: "Validation error: all fields are required and the passwords must match."
        }

        if (User::withTrashed()->where('email', $request->email)->exists()) {
            return response()->json([
                'success'=> (bool)true,
                'message' => 'recovery-needed',
                'duplicate' => User::onlyTrashed()->where('email', $request->email)->exists(),
            ], 200);
            // English: "The email address is already registered."
        }
        
        try {
        

            $input = $request->only(['name','email','password']);
            $input['password'] = bcrypt($input['password']);

            $user = User::create($input);
            $user->role = User::roles()['VENDOR'];
            $user->save();
            
            if (!$user->trashed()) {
                $otp = $this->otpService->generateOtp($request->email);
            }

            // Store the token and expiry time in the database
            $user->password_reset_otp = $otp;
            $user->password_reset_otp_is_verified = false;
            $user->password_reset_otp_expiry = now()->addMinutes( $this->otpService->getTtl_min_time());  

            if($request->timezone){
                $user->config->update([
                    'give_notify_email' => true,
                    'give_booking_reminder' => true,
                    'timezone' => in_array($request->timezone, ['Europe/Madrid','Atlantic/Canary']) ? $request->timezone :'Europe/Madrid',
                ]);
            }
            $user->save();
            if (!$user->trashed()) {
                $this->otpService->sendOtpEmail($request->email, $otp);
            }
            

            $success['user'] =  $user;
            
            $profile = Profile::where('user_id', $user->id)->first(); 
            
            $profile->phone = $request->input('phone');
            $profile->is_vendor = 1;
            $profile->save();

            $vendor = new Vendor;
            $vendor->user_id = $user->id;
            // $vendor->business_email = $user->email;
            $vendor->business_phone = $user->profile->phone;
            
            $vendor->business_name = $request->input('business_name');
            $vendor->business_type = $request->input('business_type');
            $vendor->business_bio = $request->input('business_bio');

            $vendor->street = $request->input('street');
            $vendor->city = $request->input('city');
            $vendor->zipcode = $request->input('zipcode');
            $vendor->state = $request->input('state');
            $vendor->country = $request->input('country');
            $vendor->save();

            if($request->hasFile('logo')){
                // $vendor->logo = public_fileUpload($request->file('logo'), 'vendors/logo');
                $vendor->logo = uploadImage($request->file('logo'), 'vendors/logo/', Str::random(4));
                $vendor->save();
            }

            $token = auth('api')->login($user, false);
            Mail::to($user->email)->send(new WelcomeMail($user));  
            
            return response()->json([
                'success' => true,
                'message' => 'Envío de OTP a tu correo electrónico',
                'user' => [
                    'email'=> $user->email,
                    'role' => $user->role,
                    'vendor_id'=> $vendor->id,
                    'business_name' => $vendor->business_name,
                ],
                // 'token' => $token
            ]);
        }
        catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
  
    /** Get a JWT via given credentials.
     * @return \Illuminate\Http\JsonResponse */
    public function login()
    {
        $credentials = request(['email', 'password']);
  
        if (! $token = auth('api')->attempt($credentials)) {
            return $this->sendError('No autorizado.', ['error'=>'No autorizado']);
        }
        
        $user = User::whereEmail(request()->input('email'))->first();
        // $token = $this->respondWithToken($token);
        
        if(!$user->status){
            return response()->json([
                'success' => false,
                'status' => false,
                'message'=>'email activation needed'
            ]);
        }
                
        $cookie = cookie(
            'jwt_token',
            $token,
            1440*30,           // 24 hours in minutes x 30
            '/',            // path
            null,           // domain (null = current domain)
            false,          // secure (false for http://localhost)
            false,          // httpOnly = FALSE so JS can read it
            false,          // raw
            'lax'           // sameSite
        );

        return response()->json([
            'message' => 'Inicio de sesión correcto',
            'success' => true,
            'user' => [
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token // ← Include in response body too (optional but helpful)
        ])->withCookie($cookie);
    }
  
    /** Get the authenticated User.
     * @return \Illuminate\Http\JsonResponse */
    public function profile()
    {
  
        // when using cookies, the default web guarded is in use. 
        $user = auth()->user();
        
        $success['id'] = $user?->id;
        $success['name'] = $user?->name;
        $success['email'] = $user?->email;
        $success['phone'] = $user?->profile?->phone;
        $success['avatar'] = $user?->profile?->avatar;
        $success['role'] = $user?->role;

        if($user?->role == 'vendor'){
            $success['business_name'] = $user?->vendor?->business_name;
            $success['vendor_id'] = $user->vendor->id;
        }

        // $success['profile'] = auth('api')->user()->profile;    
        return $this->sendResponse($success, 'profile return successfully.');
    }
  
    /** Log the user out (Invalidate the token).
     * @return \Illuminate\Http\JsonResponse */
    public function logout_old()
    {
        auth('api')->logout();
        
        return $this->sendResponse([], 'Successfully logged out.');
    }
    public function logout(Request $request)
    {
        // If you want, invalidate the token with JWTAuth
        try {
            // JWTAuth::invalidate(JWTAuth::getToken()); // for cockie :3
            auth('api')->logout();
        } catch (\Exception $e) {
            // handle exception if needed
        }

        // Clear cookie by setting a past expiry
        $cookieName = 'jwt_token';
        $cookie = Cookie::forget($cookieName);

        return response()->json(['message'=>'Logged out'])
                         ->withCookie($cookie);
    }
    /** Refresh a token.
     * @return \Illuminate\Http\JsonResponse */
    public function refresh()
    {
        $success = $this->respondWithToken(auth('api')->refresh());
   
        return $this->sendResponse($success, 'Refresh token return successfully.');
    }
  
    /** Get the token array structure.
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }

    public function forgotPassword(Request $request)
    {
        $messages = [
            'email.required' => 'El campo :attribute es obligatorio.',
            'email.email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'attributes' => [
                'email' => 'correo electrónico',
            ]
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], $messages);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error al actualizar el perfil: el correo electrónico es obligatorio y debe ser válido.'
            ], 422);
            // English: "Profile update error: email is required and must be valid."
        } 

        $routeName = Route::currentRouteName();
        $allowedDomains = ['davized-kriku.vercel.app',];  

        $currentDomain = $request->getHost();  

        $query = User::where('email', $request->email);

        if (
            // in_array($currentDomain, $allowedDomains) && 
            $routeName === 'recovery.otp') {
            $query->withTrashed();  
        }

        $user = $query->first();

        if (!$user) {
            // return jsonErrorResponse('No se ha encontrado ningún usuario con esta dirección de correo electrónico.', 404);
            return response()->json([
                'success' => false,
                'status' => false,
                // 'message' => 'No user found with this email',
                'message' => 'No se ha encontrado ningún usuario con esta dirección de correo electrónico.',

            ],200);
            // return jsonErrorResponse('No user found with this email address.', 404);
        }

        // Generate a 6-digit reset token
        $otp = $this->otpService->generateOtp($request->email);

        // Store the token and expiry time in the database
        $user->password_reset_otp = $otp;
        $role = $user->role;
        $user->password_reset_otp_is_verified = false;
        $user->password_reset_otp_expiry = now()->addMinutes( $this->otpService->getTtl_min_time());  // Token expires after 5 minutes
        $user->role = $role ?? 'user';
        $user->save();

        // Send token to the user's email (using Queue)
        // Mail::to($user->email)->queue(new PasswordResetMail($token));
        $this->otpService->sendOtpEmail($request->email, $otp);


        return jsonResponse(true, 'Se ha enviado un código OTP para restablecer la contraseña a su correo electrónico.', 200, 
        ['OTP' => $user->password_reset_token]);
        // return jsonResponse(true, 'Password reset OTP has been sent to your email.', 200, ['OTP' => $user->password_reset_token]);
    }

    public function verifyOtp(Request $request)
    {

        $messages = [
            // Email validation
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            
            // OTP validation
            'otp.required' => 'El código OTP es obligatorio.',
            'otp.string' => 'El código OTP debe ser una cadena de texto válida.',
            
            // Generic fallback messages
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            
            // Attribute names for better readability
            'attributes' => [
                'email' => 'correo electrónico',
                'otp' => 'código OTP',
            ]
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string',
        ], $messages);

        // Check if validation fails
        if ($validator->fails()) {
            return jsonErrorResponse('Profile Update Validation failed', 422, $validator->errors()->toArray());
        }

        // Find the user by email
        $user = User::where('email', $request->email)->withTrashed()->first();

        if (!$user) {
            return jsonErrorResponse('No se ha encontrado ningún usuario con esta dirección de correo electrónico.', 404);
        }

        // Check if the OTP matches
        if ($user->password_reset_otp !== removeSpaces($request->otp)) {
            return jsonErrorResponse('OTP no válido.', 400);
        }

        if (!$user->password_reset_otp) {
            return jsonErrorResponse('OTP no autorizado.', 401);
        }

        // Check if the OTP has expired
        if ($user->password_reset_otp_expiry < now()) {
            return jsonErrorResponse('El OTP ha caducado.', 400);
        }

        if (request()->route()->getName() === 'verify.registration.otp') {
            $user->status = 1;
        }

        $user->password_reset_otp_is_verified = true;
        $user->password_reset_otp_expiry = now()->addMinutes(5);
        $user->save(); 
        
        if (request()->route()->getName() === 'verify.registration.otp') {
            $token = auth('api')->login($user);
            
            if($user->role == 'vendor'){
                 return response()->json([
                    'success' => true,
                    'message' => 'Registro realizado correctamente',
                    'user' => [
                        'email'=> $user->email,
                        'role' => $user->role,
                        'vendor_id'=> $user->vendor->id,
                        'business_name' => $user->vendor->business_name,
                    ],
                    'token' => $token
                ]);
            }
            return response()->json(
                [
                    'success'=> true,
                    'message' => 'Registro realizado correctamente',
                    'user' => [
                            'email' => $user->email,
                            'role' => $user->role,
                        ],
                    "token" => $token
                ], 200
            );
        }

        return response()->json(
            [
                'success'=> true,
                // "message" => "OTP verified successfully. You can now reset your password with in the next 5 mins.",
                "message" => "OTP verificado correctamente. Ahora puede restablecer su contraseña en los próximos 5 minutos.",
                "email" => $request->email
            ], 200
        );
        // return jsonResponse(true, 'OTP verified successfully. You can now reset your password with in the next 5 mins.', 200);
    }

    public function resetPassword(Request $request)
    {
        $messages = [
            // Email validation
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            
            // Password validation
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto válida.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            
            // Custom PasswordRule validation
            'password.custom' => 'La contraseña no cumple con los requisitos de seguridad. Debe contener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.',
            
            // Generic fallback messages
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'confirmed' => 'La confirmación del campo :attribute no coincide.',
            
            // Attribute names for better readability
            'attributes' => [
                'email' => 'correo electrónico',
                'password' => 'contraseña',
                'password_confirmation' => 'confirmación de contraseña',
            ]
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => ['required','string', 'confirmed', new PasswordRule],
        ], $messages);


        // Check if validation fails
        if ($validator->fails()) {
            // return jsonErrorResponse('Profile Update Validation failed', 422, $validator->errors()->toArray());
            return response()->json([
                'success'=> (bool)false,
                getErrorHeader() => getValidationType(),
                'message'=> "Por favor, introduzca la misma contraseña dos veces."
            ],422);
        }

        // Find the user by email
        // $user = User::where('email', $request->email)->first();
        $user = User::where('email', $request->email)->withTrashed()->first();


        if (!$user) {
            return response()->json([
                'success'=> (bool)false,
                'status' => false,
                getErrorHeader() => "regularError",
                'message'=> "No se ha encontrado ningún usuario con esta dirección de correo electrónico."
            ],200);
            
            // return jsonErrorResponse('No user found with this email address.', 404);
        }
        if (!$user->password_reset_otp_is_verified) {
            return response()->json([
                'success'=> (bool)false,
                'status' => false,
                getErrorHeader() => "regularError",
                'message'=> "Intento no autorizado."
            ],200);
            // return jsonErrorResponse('Unauthorized attempt.', 401);
        }
        // Check if OTP verification is done
        if ($user->password_reset_otp === null || $user->password_reset_otp_expiry < now()) {
            $user->password_reset_otp_is_verified = false;
            $user->save();

            return response()->json([
                'success'=> (bool)false,
                'status' => false,
                getErrorHeader() => "regularError",
                'message'=> "La verificación OTP ha fallado o ha caducado. Solicite una nueva OTP."
            ],200);

            // return jsonErrorResponse('OTP verification failed or expired. Please request a new OTP.', 400);
        }

        // If OTP is verified and not expired, proceed with password reset
        $user->password = Hash::make($request->password); // Hash the new password
        $user->password_reset_otp = null; // Clear the otp after password reset
        $user->password_reset_otp_expiry = null; // Clear the expiry
        $user->password_reset_otp_is_verified = false;
         $role = $user->role;
        $user->role = $role;
        $user->save();
        
        // Restore the user
        if ($user && $user->trashed()) {
            $user->restore();  
        }
        
        return response()->json([
                'success'=> (bool)true,
                "type" => "confirmation",
                'message'=> "La contraseña se ha restablecido correctamente."
        ],200);

        return jsonResponse(true, 'La contraseña se ha restablecido correctamente.', 200);
    }
    
    public function resendOtp(Request $request)
    {
        $messages = [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], $messages);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                getErrorHeader() => getValidationType(),
                "message" =>  "Error en la validación de la actualización del perfil",
                "errors" => $validator->errors(),
            ], 422);
            // return jsonErrorResponse('Profile Update Validation failed', 422, $validator->errors()->toArray());
        }

        // Find user by email
        // $user = User::where('email', $request->email)->first();
        $user = User::where('email', $request->email)->withTrashed()->first();


        if (!$user) {
            return response()->json([
                "success" => false,
                getErrorHeader() => getValidationType(),
                "message" =>  "No se ha encontrado ningún usuario con esta dirección de correo electrónico.",
                "errors" => $validator->errors(),
            ],  404);
            // return jsonErrorResponse('No user found with this email address.', 404);
        }

        // Generate a new 6-digit reset token
        $otp = $this->otpService->generateOtp($request->email);

        // Store the new token and set expiry time
        $user->password_reset_otp = $otp;
        $user->password_reset_otp_is_verified = false;
        $user->password_reset_otp_expiry = now()->addMinutes($this->otpService->getTtl_min_time());  // Token expires after 5 minutes
        $user->save();

        // Send the new token to the user's email
        // Mail::to($user->email)->queue(new PasswordResetMail($token));
        $this->otpService->sendOtpEmail($request->email, $otp);
        return response()->json([
                'status'=> (bool)true,
                'success'=> (bool)true,
                "type" => "confirmation",
                'message'=> "Se ha enviado una nueva contraseña de restablecimiento OTP a su correo electrónico."
        ],201);
        return jsonResponse(true, 'A new password reset OTP has been sent to your email.', 200, ['OTP' => $otp]);
    }

    
    public function profileRetrieval(Request $request)
    {

        try {
            $user = auth()->user();
            $data = [
                'id'=> $user->id,
                'name'=> $user->name,
                'email'=> $user->email,
                'avatar'=> $user->profile->avatar,
                'phone'=> $user->profile->phone,
                'role'=> $user->id,
            ];

            // Latest assessment with both score and created_at
           
            return jsonResponse(
                true,
                'User profile retrieved successfully.',
                200,
                $data
            );
        } catch (Exception $e) {
            return jsonErrorResponse('Failed to retrieve user profile.'.$e->getMessage(), 500);
        }
    }

     public function ProfileUpdate(Request $request)
    {
        $authenticatedUser = User::find(auth('api')->user()->id);

        $messages = [
            // Name validation
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            
            // Email validation
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está en uso por otro usuario.',
            
            // Avatar validation
            'avatar.image' => 'El archivo debe ser una imagen válida.',
            'avatar.mimes' => 'La imagen debe ser de tipo: jpg, jpeg, png, gif, svg, webp, ico, bmp o tiff.',
            'avatar.max' => 'El tamaño de la imagen no puede exceder los 5 MB.',
            
            // Address validation
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'address.max' => 'La dirección no puede tener más de 255 caracteres.',
            
            // Generic fallback messages
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
            'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'unique' => 'El :attribute ya ha sido registrado.',
            'image' => 'El campo :attribute debe ser una imagen.',
            'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
            
            // Custom attribute names for better readability
            'attributes' => [
                'name' => 'nombre',
                'email' => 'correo electrónico',
                'avatar' => 'avatar',
                'address' => 'dirección',
            ]
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email|max:255|unique:users,email,' . $authenticatedUser->id,
            'avatar' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif,svg,webp,ico,bmp,tiff|max:5120',
            'address' => 'sometimes|nullable|string|max:255'
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                getErrorHeader() => getValidationType(),
                "message" =>  "Error en la validación de la actualización del perfil",
                "errors" => $validator->errors(),
            ], 422);
        }

        // Update only the fields that exist in request
        if ($request->filled('name')) {
            $authenticatedUser->name = $request->name;
        }

        if ($request->filled('email')) {
            $authenticatedUser->email = $request->email;
        }

        if ($request->filled('address')) {
            $authenticatedUser->address = $request->address;
        }

        // Avatar handle
        if ($request->hasFile('avatar')) {
            if ($authenticatedUser->avatar) {
                fileDelete(public_path($authenticatedUser->avatar));
            }

            $avatar = $request->file('avatar');
            $avatarName = $authenticatedUser->id . '_avatar';
            $avatarPath = fileUpload($avatar, 'profile/avatar', $avatarName);

            $authenticatedUser->avatar = $avatarPath;
        }

        $authenticatedUser->save();

        return jsonResponse(
            true,
            'Perfil actualizado correctamente',
            200,
            $authenticatedUser->only(['name', 'email', 'avatar', 'address'])
        );
    }

    public function ChangePassword(Request $request)
    {

       $messages = [
            'old_password.required' => 'Por favor, ingrese su contraseña actual.',
            'old_password.string' => 'La contraseña actual debe ser texto válido.',
            'password.required' => 'Por favor, ingrese su nueva contraseña.',
            'password.string' => 'La nueva contraseña debe ser texto válido.',
            'password.confirmed' => 'Las contraseñas nuevas no coinciden. Por favor, verifique.',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres para mayor seguridad.',
            
            // Generic fallback messages
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe contener texto válido.',
            'confirmed' => 'El campo :attribute no coincide con la confirmación.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            
            // Attribute names
            'attributes' => [
                'old_password' => 'contraseña actual',
                'password' => 'nueva contraseña',
                'password_confirmation' => 'confirmar contraseña',
            ]
        ];

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ], $messages);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                getErrorHeader() => getValidationType(),
                "message" =>  "Error en la validación de la actualización del perfil",
                "errors" => $validator->errors(),
            ], 422);
        }

        // Authenticate the user using JWT
        // $user = JWTAuth::parseToken()->authenticate();
        $user = auth('api')->user();

        if (!$user) {
            return jsonErrorResponse('Usuario no encontrado o no autorizado', 401);
        }

        // Check if the old password matches the current password
        if (!Hash::check($request->old_password, $user->password)) {
            return jsonErrorResponse('La contraseña anterior es incorrecta.', 400);
        }

        // Hash the new password and save it to the database
        $user->password = Hash::make($request->password);
        $user->save();

        return jsonResponse(true, 'Contraseña cambiada correctamente', 200, $user->only(['name', 'email', 'avatar']));
    }
}
