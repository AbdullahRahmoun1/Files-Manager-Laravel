<?php

namespace App\Services;

use App\Repositories\GroupUserRepository;
use App\Models\Group;
use App\Models\User;

class GroupUserService
{
    protected $groupUserRepo;

    public function __construct(GroupUserRepository $groupUserRepo)
    {
        $this->groupUserRepo = $groupUserRepo;
    }

    public function all()
    {
        $userId = request()->user()->id ?? 0;
        $relations = ['group', 'user', 'inviter'];
        return [
            'invitationsFromMe' => $this->groupUserRepo->getInvitationsFromUser($userId, $relations),
            'invitationsToMe' => $this->groupUserRepo->getInvitationsToUser($userId, $relations),
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
            $invitee->groupInvitations()->where('groups.id', $group->id)->exists()
        ) {
            throwError("The user is either already a member of this group or has a pending invitation.");
        }

        $data['inviter_id'] = $inviter->id;
        $invitation = $this->groupUserRepo->createInvitation($data);

        app('firebase')->send(
            $invitee,
            __('notifications.group.invitation.received.title'),
            __('notifications.group.invitation.received.body', ['groupName' => $group->name])
        );

        return $invitation;
    }

    public function acceptInvitation($id)
    {
        $groupUser = $this->groupUserRepo->findInvitationById($id, ['group', 'inviter']);
        $user = request()->user();

        if ($groupUser->user_id != $user->id) {
            throwError("Only the invitee can accept the invitation.");
        }

        $updatedInvitation = $this->groupUserRepo->updateInvitation($id, ['joined_at' => now()]);

        app('firebase')->send(
            $groupUser->inviter,
            __('notifications.group.invitation.accepted.title'),
            __('notifications.group.invitation.accepted.body', [
                'groupName' => $groupUser->group->name,
                'userName' => $user->name,
            ])
        );

        return $updatedInvitation;
    }

    public function delete($id)
    {
        $connectedUser = request()->user();
        $groupUser = $this->groupUserRepo->findInvitationById($id, ['group', 'inviter', 'user']);

        if (
            $connectedUser->id != $groupUser->inviter_id &&
            $connectedUser->id != $groupUser->user_id
        ) {
            throwError("You don't have the permission to do this.");
        }

        if ($connectedUser->id == $groupUser->user_id) {
            $this->groupUserRepo->refuseInvitation($id);

            app('firebase')->send(
                $groupUser->inviter,
                __('notifications.group.invitation.refused.title'),
                __('notifications.group.invitation.refused.body', [
                    'groupName' => $groupUser->group->name,
                    'userName' => $groupUser->user->name,
                ])
            );
        } else {
            $this->groupUserRepo->deleteInvitation($id);
        }
    }
}
