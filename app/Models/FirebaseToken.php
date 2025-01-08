<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirebaseToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'owner_type',
        'owner_id',
        'auth_token',
        'fcm_token'
    ];

    public function owner(){
        return $this->morphTo('owner');
    }
}
