<?php

namespace App\Services;
use App\Enums\RoleTypeEnum;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AuthService
{
    protected $firebaseTokenService;
    public function __construct(FirebaseTokenService $firebaseTokenService)
    {
        $this->firebaseTokenService = $firebaseTokenService;
    }
    public function register($data): array
    {
        $user = User::create($data);
        $tokenData=$this->validateCountAndCreateToken($user);
        if (isset($data['fcm_token'])) {
            $this->firebaseTokenService->attachToOwner($user, $data['fcm_token'],$user->tokens->first());
        }
        return [
            'user' => $user,
        ]+$tokenData;
    }
    public function login($data)
    {
        $fcm_token = $data['fcm_token']??null;
        unset($data['fcm_token']);
        if (!auth()->attempt($data)) {
            throwError(
                __('auth.wrong_credentials')
            );
        }
        $user = request()->user();
        $result = [
            'user' => $user,
        ];
        $result['token'] = $this->validateCountAndCreateToken($user);
        if ($fcm_token!=null) {
            $this->firebaseTokenService->attachToOwner($user, $fcm_token ,$user->tokens->last());
        }
        return $result;
    }

    public function validateCountAndCreateToken(User $authenticatable)
    {
        if ($authenticatable->tokens()->count() >= 6) {
            $token = $authenticatable->tokens()->orderBy('created_at')->first();
            if($authenticatable instanceof User){
                $authenticatable->fcmTokens()->where('auth_token', $token->token)->delete();
            }
            $token->delete();
        }
        $type =
            $authenticatable->isSuperAdmin()
            ?RoleTypeEnum::ADMIN->value:RoleTypeEnum::USER->value
        ;
        $token = $authenticatable
        ->createToken(request()->ip(), [$type]);
        $plainToken = $token->plainTextToken;
        return [
            'token' => $plainToken,
            'type' => $type
        ];
    }
}
