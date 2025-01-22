<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use App\Repositories\GroupUserRepository;
use App\Models\Group;
use App\Models\User;
use Wever\Laradot\App\Services\DotService;

class GroupService extends DotService
{
    protected $groupRepo;
    protected $groupUserRepo;

    public function __construct(GroupRepository $groupRepo, GroupUserRepository $groupUserRepo)
    {
        parent::__construct(Group::class);
        $this->groupRepo = $groupRepo;
        $this->groupUserRepo = $groupUserRepo;
    }
    public function dotIndex($query = null)
    {
        $query = request()->user()->groups()->with('files');
        return parent::dotIndex($query);
    }
    public function dotAll($query = null)
    {
        $query = request()->user()->groups()->with('files', 'members');
        return parent::dotAll($query);
    }
    public function dotShow($id)
    {
        if (!request()->user()->groups()->where('groups.id', $id)->exists()) {
            throwError("You don't have the permission to view this group.");
        }

        $model = parent::dotShow($id);
        $model->load(['files', 'members']);
        return $model;
    }
    public function dotCreate($data)
    {
        if (!request()->user()) {
            throwError("Couldn't find creator id");
        }
        $data['creator_id'] = request()->user()->id;
        $group = parent::dotCreate($data);
        request()->user()->groups()->attach($group->id, [
            'joined_at' => now(),
            'inviter_id' => request()->user()->id
        ]);
        return $group;
    }

    public function getUserGroupsWithFiles()
    {
        $userId = request()->user()->id;
        return $this->groupRepo->getUserGroupsWithFiles($userId);
    }

    public function getUserGroupsWithFilesAndMembers()
    {
        $userId = request()->user()->id;
        return $this->groupRepo->getUserGroupsWithFilesAndMembers($userId);
    }

    public function showGroup($id)
    {
        $userId = request()->user()->id;
        if (!$this->groupRepo->isUserInGroup($id, $userId)) {
            throwError("You don't have the permission to view this group.");
        }
        return $this->groupRepo->findByIdWithRelations($id, ['files', 'members']);
    }

    public function createGroup($data)
    {
        $user = request()->user();
        $data['creator_id'] = $user->id;
        $group = $this->groupRepo->create($data);
        $this->groupUserRepo->attachUserToGroup($group->id, $user->id, $user->id);
        return $group;
    }

    public function kickUser(Group $group, User $user)
    {
        $currentUser = request()->user();
        if ($group->creator_id != $currentUser->id) {
            throwError("You don't have the permission to do this.");
        }
        if ($group->creator_id == $user->id) {
            throwError("Group owner can't be kicked out.");
        }
        $membership = $this->groupUserRepo->findActiveMembership($group->id, $user->id);
        $this->groupUserRepo->saveKickedStatus($membership);

        app('firebase')->send(
            $user,
            __('notifications.group.invitation.kicked.title'),
            __('notifications.group.invitation.kicked.body', ['groupName' => $group->name])
        );
    }

    public function leaveGroup(Group $group, User $user)
    {
        if ($group->creator_id == $user->id) {
            throwError("Group owner can't leave the group, delete it instead.");
        }
        $membership = $this->groupUserRepo->findActiveMembership($group->id, $user->id);
        $this->groupUserRepo->saveLeftStatus($membership);

        app('firebase')->send(
            $user,
            __('notifications.group.invitation.left.title'),
            __('notifications.group.invitation.left.body', ['groupName' => $group->name])
        );
    }
}
