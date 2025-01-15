<?php

namespace App\Http\Controllers;
use Wever\Laradot\App\Http\Controllers\DotController;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;


class UserController extends DotController
{
    public function __construct() {
        parent::__construct(
            UserService::class,
            CreateUserRequest::class,
            UpdateUserRequest::class,
            UserResource::class
        );
    }

    public function report(User $user){
        return $this->success(
            $this->service->getUserReport($user)
        );
    }
}
