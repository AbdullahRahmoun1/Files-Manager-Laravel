<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'invitation_expires_at',
        'joined_at',
        'inviter_id',
        'group_id',
        'user_id'
    ];
    public function scopeInvitations(){
        return $this->where('invitation_expires_at', '>', now())
        ->whereNull("refused_at")
        ->whereNull('joined_at');
    }
    public function scopeActive(){
        return $this
        ->whereNotNull('joined_at')
        ->whereNull('kicked_at')
        ->whereNull("left_at");
    }
    public function inviter(){
        return $this->belongsTo(User::class,'inviter_id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function group(){
        return $this->belongsTo(Group::class);
    }
}
