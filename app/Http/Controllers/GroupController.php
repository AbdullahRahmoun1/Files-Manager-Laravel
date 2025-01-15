<?php

namespace App\Http\Controllers;
use Wever\Laradot\App\Http\Controllers\DotController;
use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Services\GroupReportService;
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
        $reportService = app(GroupReportService::class);
        return $this->success(
            $reportService->getReport($group,request('user_id')??null)
        );
    }

    public function kickUser(Group $group,User $user){
        $this->service->kickUser($group,$user);
        return self::success();
    }
}
