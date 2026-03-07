<?php

namespace App\Http\Controllers\Web\Backend\Settings;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Event\Telemetry\System;

class SystemController extends Controller
{
    public function index(){
        return view('backend.layout.settings.system');
    }

    public function update(SystemRequest $request){
        $settings = Setting::first();


        if($request->file('logo')){
            $settings->logo = fileUpdate($request->logo, 'settings/logo', $settings->logo);
        }
        if($request->file('mini_logo')){
            $settings->mini_logo = fileUpdate($request->mini_logo, 'settings/mini_logo', $settings->mini_logo);
        }
        if($request->file('icon')){
            $settings->icon = fileUpdate($request->icon, 'settings/icon', $settings->icon);
        }

        $settings->save();

        $data = $request->safe()->except(['logo', 'mini_logo', 'icon']);
        $settings->update($data);

        return redirect()->route('backend.settings.system.index')->with('success','Updated System Settings');

    }

    /**
     * Remove a file from storage and clear the corresponding setting.
     *
     * Expects JSON payload: { field: 'logo' | 'mini_logo' | 'icon' }
     */
    public function deleteFile(Request $request)
    {
        $field = $request->input('field');
        $allowed = ['logo', 'mini_logo', 'icon'];

        if (!in_array($field, $allowed)) {
            return response()->json(['error' => 'Invalid field provided'], 422);
        }

        $settings = Setting::first();
        if (!$settings) {
            return response()->json(['error' => 'Settings not found'], 404);
        }

        $path = $settings->{$field};

        if ($path) {
            // remove from disk - path is stored relative to public
            if (file_exists(public_path($path))) {
                @unlink(public_path($path));
            }
            $settings->{$field} = null;
            $settings->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'No file to delete'], 404);
    }
}
