<?php

namespace App\Http\Controllers\Web\Backend\Settings;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
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
}
