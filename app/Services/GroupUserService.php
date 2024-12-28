<?php

namespace App\Services;

use App\Models\Group;
use Wever\Laradot\App\Services\DotService;
use App\Models\GroupUser;
use App\Models\User;

class GroupUserService extends DotService
{
    public function __construct()
    {
        parent::__construct(GroupUser::class);
    }
    public function all()
    {
        $loadRelations = ['group', 'user', 'inviter'];
        $invitationsFromMe = GroupUser::invitations()->with($loadRelations)->where('inviter_id', request()->user()->id ?? 0)->get();
        $invitationsToMe = request()->user()->groupeInvitations()->with($loadRelations)->get();
        return [
            'invitationsFromMe' => $invitationsFromMe,
            'invitationsToMe' => $invitationsToMe
        ];
    }
    public function create($data)
    {
        $group = Group::findOrFail($data['group_id']);
        $inviter = request()->user();
        $invitee = User::findOrFail($data['user_id']);
        if ($inviter->id != $group->creator->id) {
            throwError("Only group owner can invite other users.");
        }
        if (
            $invitee->groups()->where('groups.id', $group->id)->exists() ||
            $invitee->groupeInvitations()->where('groups.id', $group->id)->exists()
        ) {
            throwError("The user is either already a member of this group or has a pending invitation.");
        }
        $data['inviter_id'] = $inviter->id;
        return $this->dotCreate($data);
    }
    public function acceptInvitation($id)
    {
        $groupUser = GroupUser::invitations()->findOrFail($id);
        $user = request()->user();
        if ($groupUser->user_id != $user->id) {
            throwError("Only the invitee can accept the invitation.");
        }
        $data = ['joined_at' => now()];
        return $this->dotUpdate($id, $data);
    }

    public function delete($id)
    {
        $user = request()->user();
        $groupUser = GroupUser::invitations()->findOrFail($id);
        if ($groupUser->inviter_id != $user->id) {
            throwError("Only the inviter can delete the invitation.");
        }
        $groupUser->delete();
    }
}
