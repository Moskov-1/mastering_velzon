<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Rules\PasswordRule;

class DashboardApiController extends BaseController
{

    public function __construct()
    {

    }

    /** Get the authenticated User.
     * @return \Illuminate\Http\JsonResponse */
    public function profile()
    {
        $success = auth('api')->user();
   
        return $this->sendResponse($success, 'Refresh token return successfully.');
    }
  
    
    public function profileRetrieval(Request $request)
    {
        try {
            $user = auth('api')->user();

            // Latest assessment with both score and created_at
            $data = $user->profile;

            // $latestCreatedAt = $latestAssessment
            //     ? Carbon::parse($latestAssessment->created_at)->format('d F Y, g:i A')
            //     : null;

            return jsonResponse(
                true,
                'User profile retrieved successfully.',
                200,
                ['
                        user'=>$user,
                        'data'=>$data
                    ]
            );
        } catch (Exception $e) {
            return jsonErrorResponse('Failed to retrieve user profile.', 500);
        }
    }

     public function ProfileUpdate(Request $request)
    {
        $authenticatedUser = User::find(auth('api')->user()->id);

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email|max:255|unique:users,email,' . $authenticatedUser->id,
            'avatar' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif,svg,webp,ico,bmp,tiff|max:5120',
            'address' => 'sometimes|nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return jsonErrorResponse(
                'Profile Update Validation failed',
                422,
                $validator->errors()->toArray()
            );
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
            $avatarPath = fileUpload($avatar, 'user/avatar', $avatarName);

            $authenticatedUser->avatar = $avatarPath;
        }

        $authenticatedUser->save();

        return jsonResponse(
            true,
            'Profile updated successfully',
            200,
            $authenticatedUser->only(['name', 'email', 'avatar', 'address'])
        );
    }

}
