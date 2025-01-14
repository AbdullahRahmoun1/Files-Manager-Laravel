<?php

namespace App\Http\Controllers;
use Wever\Laradot\App\Http\Controllers\DotController;
use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Services\GroupService;


class GroupController extends DotController
{
    public function __construct() {
        parent::__construct(
            GroupService::class,
            CreateGroupRequest::class,
            UpdateGroupRequest::class,
            GroupResource::class
        );
    }

    public function getGroupReport(Group $group){
        return $this->success(
            $this->service->getGroupReport($group)
        );
    }
}
