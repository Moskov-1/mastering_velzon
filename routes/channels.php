<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('presence.online', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'last_seen_at' => $user->last_seen_at,
    ];
});


Broadcast::channel('chat.{convo_id}', function ($user, $convo_id) {
    try {
        if (!$user) {
            return false;
        }

        $conv = Conversation::select('id', 'seller_id', 'buyer_id')->where('id', $convo_id)->first();
        
        if(!$conv){
            return false;
        }

        // Logic: Is the user the buyer OR the seller?
        $isAllowed = ((int)$user->id === (int)$conv->buyer_id || (int)$user->id === (int)$conv->seller_id);

        $allowedRoles = [
            'admin' => true,
            'manager' => true,
            'agent' => true,
        ];
        
        if(!$isAllowed && isset($user->static_role)){
            $isAllowed = isset($allowedRoles[$user->static_role]);
        }

        return $isAllowed ? true : false;
    } catch (\Exception $e) {
        Log::error("Broadcast Auth Error: " . $e->getMessage());
        return false;
    }
});
