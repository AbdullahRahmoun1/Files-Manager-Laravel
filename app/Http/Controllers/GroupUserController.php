<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupUserRequest;
use App\Http\Resources\GroupUserResource;
use App\Services\GroupUserService;


class GroupUserController extends Controller
{
    protected GroupUserService $service;
    public function __construct(GroupUserService $service)
    {
        $this->service = $service;
    }
    public function viewInvitations()
    {
        $data = $this->service->all();
        return $this->success([
            'invitationsFromMe' => GroupUserResource::collection($data['invitationsFromMe']),
            'invitationsToMe' => GroupUserResource::collection($data['invitationsToMe']),
        ]);
    }
    public function createInvitation(CreateGroupUserRequest $createGroupUserRequest)
    {
        $data=$this->service->create($createGroupUserRequest->validated());
        return $this->success(
            GroupUserResource::make($data)
        );
    }
    public function deleteInvitation($id)
    {
        $this->service->delete($id);
        return $this->success();
    }

    public function acceptInvitation($id){
        $this->service->acceptInvitation($id);
        return $this->success();
    }
}
