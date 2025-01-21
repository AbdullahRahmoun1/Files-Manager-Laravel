<?php

namespace App\Services;

use Wever\Laradot\App\Services\DotService;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;

class GroupService extends DotService
{
    public function __construct()
    {
        parent::__construct(Group::class);
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

    public function kickUser(Group $group, User $user)
    {
        if ($group->creator_id != request()->user()->id) {
            throwError("You don't have the permission to do this.");
        }
        if ($group->creator_id == $user->id) {
            throwError("Group owner can't be kicked out.");
        }
        $gUser = GroupUser::active()
            ->where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->firstOrFail();
        $gUser->kicked_at = now();
        $gUser->save();
        app(UserService::class)->checkOutAllGroupFiles($user,$group);
        app('firebase')->send(
            $gUser->user,
            __('notifications.group.invitation.kicked.title'),
            __(
                'notifications.group.invitation.kicked.body',
                [
                    'groupName' => $gUser->group->name,
                ]
            ),
        );
    }
    public function leaveGroup(Group $group, User $user)
    {
        if ($group->creator_id == $user->id) {
            throwError("Group owner can't leave the group, delete it instead.");
        }
        $gUser = GroupUser::active()
            ->where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->firstOrFail();
        $gUser->left_at = now();
        $gUser->save();
        app(UserService::class)->checkOutAllGroupFiles($user,$group);
        app('firebase')->send(
            $gUser->user,
            __('notifications.group.invitation.left.title'),
            __(
                'notifications.group.invitation.left.body',
                [
                    'groupName' => $gUser->group->name,
                    'userName' => $user->name
                ]
            ),
        );
    }
}
