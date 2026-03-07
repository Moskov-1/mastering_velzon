<?php

namespace App\Http\Controllers\API;

use App\Traits\ApiResponseTrait;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Profile;
use App\Rules\PasswordRule;
use Illuminate\Support\Str;

use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\API\BaseController as BaseController;

class AuthController extends BaseController
{
    use ApiResponseTrait;
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
            'name.required' => 'Please enter your full name.',
            'email.required' => 'The email address is required to create your account.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'You must create a password for your account.',
            'c_password.required' => 'Please confirm your password.',
            'c_password.same' => 'The passwords do not match. Please verify them.',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ], $messages);
     
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error: all fields are required and passwords must match'
            ], 422);
            // English meaning: "Validation error: all fields are required and the passwords must match."
        }
        
        try {
        
            $input = $request->only(['name','email','password']);
            $input['password'] = bcrypt($input['password']);

            $user = User::create($input);
            $user->role = 'user';
            $user->status = 0;
            $user->save();
            
            $otp = $this->otpService->generateOtp($request->email);

            // Store the token and expiry time in the database
            $user->password_reset_otp = $otp;
            $user->password_reset_otp_is_verified = false;
            $user->password_reset_otp_expiry = now()->addMinutes( $this->otpService->getTtl_min_time());  
            $user->save();

            $this->otpService->sendOtpEmail($request->email, $otp);

            $profile = Profile::where('user_id', $user->id)->first(); 
            
            $profile->phone = $request->input('phone');
            $profile->address = $request->input('address');
            $profile->is_customer = 1;
            
            if($request->hasFile('avatar')){
                $user->avatar = fileUpload($request->file('avatar'), 'avatars');
                $user->save();
            }

            $profile->save();

            $token = auth('api')->login($user);

            

            return response()->json([
                'message' => 'OTP sent to your email address',
                'success'=> true,
                'user' => [
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token'=> $token,
            ]);
        }
        catch (Exception $e) {
            return $this->errorResponse( statusCode: 400, errors: $e->getMessage());
        }
    }
  
  
    public function login()
    {
        $credentials = request(['email', 'password']);
  
        if (! $token = auth('api')->attempt($credentials)) {
            // return $this->sendError('No autorizado.', ['error'=>'No autorizado']);
            return $this->errorResponse('Unauthorized', 401);
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


        return response()->json([
            'message' => 'Inicio de sesión correcto',
            'success' => true,
            'user' => [
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token 
        ]);
    }
   
    public function profile()
    {
  
        // when using cookies, the default web guarded is in use. 
        $user = auth()->user();
        
        $response['id'] = $user?->id;
        $response['name'] = $user?->name;
        $response['email'] = $user?->email;
        $response['phone'] = $user?->profile?->phone;
        $response['avatar'] = $user?->avatar;
        $response['role'] = $user?->role;

        $response['success'] = true;

        // $success['profile'] = auth('api')->user()->profile;    
        return response()->json($response);
    }
  

    public function logout(Request $request)
    {
        // If you want, invalidate the token with JWTAuth
        try {
            // JWTAuth::invalidate(JWTAuth::getToken()); // for cockie :3
            auth('api')->logout();
        } catch (Exception $e) {
            return $this->errorResponse('something went wrong', 0, $e->getMessage());
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
   
    protected function respondWithToken($token)
    {
        return [
            'token' => $token,
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
                "message" => "OTP verified successfully. You can now reset your password with in the next 5 mins.",
                "email" => $request->email
            ], 200
        );
        // return jsonResponse(true, 'OTP verified successfully. You can now reset your password with in the next 5 mins.', 200);
    }

    public function resetPassword(Request $request)
    {
        $messages = [
            // Email validation
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            
            // Password validation
            'password.required' => 'The password is required.',
            'password.string' => 'The password must be a valid string.',
            'password.confirmed' => 'The password confirmation does not match.',
            
            // Custom PasswordRule validation
            'password.custom' => 'The password does not meet the security requirements. It must contain at least 8 characters, one uppercase letter, one lowercase letter, one number and one special character.',
            
            // Generic fallback messages
            'required' => 'The :attribute field is required.',
            'email' => 'The :attribute must be a valid email address.',
            'string' => 'The :attribute must be a string.',
            'confirmed' => 'The :attribute confirmation does not match.',
            
            // Attribute names for better readability
            'attributes' => [
                'email' => 'email',
                'password' => 'password',
                'password_confirmation' => 'password confirmation',
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
                'message'=> "Please enter the same password twice."
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
                'message'=> "No user found with this email address."
            ],200);
            
            // return jsonErrorResponse('No user found with this email address.', 404);
        }
        if (!$user->password_reset_otp_is_verified) {
            return response()->json([
                'success'=> (bool)false,
                'status' => false,
                getErrorHeader() => "regularError",
                'message'=> "Unauthorized attempt."
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
                'message'=> "OTP verification failed or expired. Please request a new OTP."
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
                'message'=> "The password has been reset successfully."
        ],200);

        return jsonResponse(true, 'The password has been reset successfully.', 200);
    }
    
    public function resendOtp(Request $request)
    {
        $messages = [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
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
                'message'=> "A new password reset OTP has been sent to your email."
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
                'avatar'=> $user?->avatar,
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
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            
            // Email validation
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'email.unique' => 'The email is already in use by another user.',
            
            // Avatar validation
            'avatar.image' => 'The file must be a valid image.',
            'avatar.mimes' => 'The image must be of type: jpg, jpeg, png, gif, svg, webp, ico, bmp or tiff.',
            'avatar.max' => 'The image size cannot exceed 5 MB.',
            
            // Address validation
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address may not be greater than 255 characters.',
            
            // Generic fallback messages
            'string' => 'The field :attribute must be a string.',
            'max' => 'The field :attribute may not be greater than :max characters.',
            'email' => 'The field :attribute must be a valid email address.',
            'unique' => 'The :attribute is already in use.',
            'image' => 'The field :attribute must be a valid image.',
            'mimes' => 'The field :attribute must be a file of type: :values.',
            
           
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
                "message" =>  "Error in profile update validation",
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
            $authenticatedUser->profile->address = $request->address;
            $authenticatedUser->profile->save();
        }

        // Avatar handle
        if ($request->hasFile('avatar')) {
            if ($authenticatedUser->avatar) {
                fileDelete($authenticatedUser->avatar);
            }

            $avatar = $request->file('avatar');
            $avatarPath = fileUpload($avatar, 'avatars/');

            $authenticatedUser->avatar = $avatarPath;
        }

        $authenticatedUser->save();

        return jsonResponse(
            true,
            'Profile updated successfully',
            200,
            [
                'name' => $authenticatedUser->name,
                'email' => $authenticatedUser->email,
                'avatar' => $authenticatedUser->avatar,
                'address' => $authenticatedUser->profile->address,
            ]
        );
    }

    public function ChangePassword(Request $request)
    {

       $messages = [
            'old_password.required' => 'Please enter your current password.',
            'old_password.string' => 'The current password must be a valid string.',
            'password.required' => 'Please enter your new password.',
            'password.string' => 'The new password must be a valid string.',
            'password.confirmed' => 'The new passwords do not match. Please verify.',
            'password.min' => 'The password must have at least 8 characters for better security.',
            
            // Generic fallback messages
            'required' => 'The field :attribute is required.',
            'string' => 'The field :attribute must be a string.',
            'confirmed' => 'The field :attribute does not match the confirmation.',
            'min' => 'The field :attribute must have at least :min characters.',
            
            // Attribute names
            'attributes' => [
                'old_password' => 'current password',
                'password' => 'new password',
                'password_confirmation' => 'confirm new password',
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
                "message" =>  "Error in profile update validation",
                "errors" => $validator->errors(),
            ], 422);
        }

        // Authenticate the user using JWT
        // $user = JWTAuth::parseToken()->authenticate();
        $user = auth('api')->user();

        if (!$user) {
            return jsonErrorResponse('User not found or not authorized', 401);
        }

        // Check if the old password matches the current password
        if (!Hash::check($request->old_password, $user->password)) {
            return jsonErrorResponse('The current password is incorrect.', 400);
        }

        // Hash the new password and save it to the database
        $user->password = Hash::make($request->password);
        $user->save();

        return jsonResponse(true, 'Password changed successfully', 200, $user->only(['name', 'email', 'avatar']));
    }
}
