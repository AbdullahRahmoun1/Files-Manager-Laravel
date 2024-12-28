<?php

namespace App\Http\Controllers;
use Wever\Laradot\App\Http\Controllers\DotController;
use App\Http\Requests\CreateSuperAdminRequest;
use App\Http\Requests\UpdateSuperAdminRequest;
use App\Http\Resources\SuperAdminResource;
use App\Services\SuperAdminService;


class SuperAdminController extends DotController
{
    public function __construct() {
        parent::__construct(
            SuperAdminService::class,
            CreateSuperAdminRequest::class,
            UpdateSuperAdminRequest::class,
            SuperAdminResource::class
        );
    }
}
