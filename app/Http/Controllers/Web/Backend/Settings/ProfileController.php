<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Container\Attributes\Storage;

class ProfileController extends Controller
{
    public function index(){
        $data['user']  = auth()->user();
        $data['profile'] = $data['user']->profile;
        return view("backend.layout.settings.profile", $data);
    }

    public function avatar(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'avatar' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }
            if ($request->hasFile('avatar')) {

                $path = fileUpload($request->file('avatar'), 'avatars/');
                $profile = Profile::find($request->profile_id);
                
                if($profile->avatar){
                    fileDelete($profile->avatar);
                }

                if ($path !== null) {
                    $profile->avatar = $path;
                }
                $profile->save();
            }

            return response()->json([
                'success' => true,
                'message'=> 'Avatar Uploaded successfully',
                'url' => asset($profile->avatar)
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
            'success' => false,
            'message' => 'Something went wrong.',
            'error'   => $e->getMessage(), 
        ], 500);
        }


    }

    public function banner(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'banner' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }
            if ($request->hasFile('banner')) {

                $path = fileUpload($request->file('banner'), 'banners/');
                $profile = Profile::find($request->profile_id);
                
                if($profile->banner){
                    fileDelete($profile->banner);
                }

                if ($path !== null) {
                    $profile->banner = $path;
                }
                $profile->save();
            }

            return response()->json([
                'success' => true,
                'message'=> 'Banner Uploaded successfully',
                'url' => asset($profile->banner)
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(), 
            ], 500);
        }


    }

}
