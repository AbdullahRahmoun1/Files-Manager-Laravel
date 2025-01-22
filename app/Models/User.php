<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Wever\Laradot\App\FilterTypes\LikeFilter;
use Wever\Laradot\App\Traits\Filterable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public $filterable = [
        'email' => LikeFilter::class,
        'name' => LikeFilter::class
    ];

    public function superAdmin()
    {
        return $this->hasOne(SuperAdmin::class);
    }
    public function isSuperAdmin()
    {
        return $this->superAdmin()->exists();
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class, GroupUser::class)
            ->whereNull('kicked_at')
            ->whereNull('left_at')
            ->whereNotNull('joined_at');
    }
    public function groupInvitations()
    {
        return $this->belongsToMany(Group::class, GroupUser::class)
        ->where('invitation_expires_at', '>', now())
        ->whereNull("refused_at")
        ->whereNull('joined_at');
    }
    public function fcmTokens(){
        return $this->morphMany(FirebaseToken::class,'owner');
    }
}
