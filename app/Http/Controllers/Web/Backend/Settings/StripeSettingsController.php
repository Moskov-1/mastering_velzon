<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;


class StripeSettingsController extends Controller
{
    public function index(){
        return view("backend.layout.settings.payments-settings");
    }

    public function update(Request $request) {
        $request->validate([
            'stripe_key'       => 'nullable|string',
            'stripe_secret'         => 'nullable|string',
            'stripe_websocket_secret'         => 'nullable|string',
        ]);
        // 'mail_username'     => 'nullable|string',

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/STRIPE_KEY=(.*)\s*/',
                '/STRIPE_SECRET=(.*)\s*/',
                '/STRIPE_WEBHOOK_SECRET=(.*)\s*/',
                
            ], [
                'STRIPE_KEY=' . $request->stripe_key . $lineBreak,
                'STRIPE_SECRET=' . $request->stripe_secret . $lineBreak,
                'STRIPE_WEBHOOK_SECRET=' . $request->stripe_websocket_secret . $lineBreak,
            ], $envContent);

            File::put(base_path('.env'), $envContent);

            return response()->json([
                'success'=> true,
                'message' => 'stripe data updated'
            ], 201);

        } 
        catch (\Exception $e) {

            return response()->json([
                'success'=> false,
                'message' => 'error', 'Failed to update ... '.$e->getMessage()
            ], 422);
        }
    }

    public function test(){
        return 'gayh';
    }
}
