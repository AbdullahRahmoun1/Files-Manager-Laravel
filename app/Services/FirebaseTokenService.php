<?php

namespace App\Services;
use App\Models\FirebaseToken;
use Illuminate\Foundation\Auth\User as Authenticatable;

class FirebaseTokenService
{
    public function attachToOwner(Authenticatable $owner,$fcmToken,$authToken=null){
        if($authToken==null){
            $authToken=$owner->currentAccessToken();
        }
        FirebaseToken::create([
            'owner_type' => $owner::class,
            'owner_id' => $owner->id,
            'auth_token' => $authToken->token,
            'fcm_token' => $fcmToken
        ]);
    }
}

