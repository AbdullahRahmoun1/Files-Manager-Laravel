<?php

namespace App\Http\Controllers;
use Wever\Laradot\App\Http\Controllers\DotController;
use App\Http\Requests\CreateGroupFileRequest;
use App\Http\Requests\UpdateGroupFileRequest;
use App\Http\Resources\GroupFileResource;
use App\Services\GroupFileService;


class GroupFileController extends DotController
{
    public function __construct() {
        parent::__construct(
            GroupFileService::class,
            CreateGroupFileRequest::class,
            UpdateGroupFileRequest::class,
            GroupFileResource::class
        );
    }
}
