<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginAdminRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use App\Models\VerificationCode;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }
    public function register(CreateUserRequest $request){
        $result = $this->authService->register($request->validated());
        $result['user'] = UserResource::make($result['user']);
        return $this->success($result);
    }
    public function login(LoginUserRequest $request){
        $result = $this->authService->login($request->validated());
        $result['user'] = UserResource::make($result['user']);
        return $this->success($result);
    }
}
