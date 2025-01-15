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
        $loadRelations = ['group','user', 'inviter'];
        $userId = request()->user()->id ?? 0;
        $invitationsFromMe = GroupUser::invitations()->with($loadRelations)->where('inviter_id', $userId)->get();
        $invitationsToMe = GroupUser::invitations()->with($loadRelations)->where('user_id',$userId)->get();
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
        $connectedUser = request()->user();
        $groupUser = GroupUser::invitations()->findOrFail($id);
        $inviter_id = $groupUser->inviter_id;
        $invitee_id = $groupUser->user_id;
        if (
            $connectedUser->id != $inviter_id
            && $connectedUser->id != $invitee_id
        ) {
            throwError("You don't have the permission to do this");
        }
        if($connectedUser->id == $invitee_id){
            $groupUser->refused_at = true;
            $groupUser->save();
        }else{
            $groupUser->delete();
        }
    }
}
