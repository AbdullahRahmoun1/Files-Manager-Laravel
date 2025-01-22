<?php

namespace App\Repositories;

use App\Models\GroupUser;
use Wever\Laradot\App\Repositories\DotRepository;

class GroupUserRepository extends DotRepository
{
    protected $model;

    public function __construct(GroupUser $groupUser)
    {
        $this->model = $groupUser;
    }

    public function findActiveMembership($groupId, $userId)
    {
        return $this->model
            ->active()
            ->where('group_id', $groupId)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function saveKickedStatus($membership)
    {
        $membership->kicked_at = now();
        return $membership->save();
    }

    public function saveLeftStatus($membership)
    {
        $membership->left_at = now();
        return $membership->save();
    }

    public function attachUserToGroup($groupId, $userId, $inviterId)
    {
        return $this->model->create([
            'group_id' => $groupId,
            'user_id' => $userId,
            'inviter_id' => $inviterId,
            'joined_at' => now(),
        ]);
    }

    public function getInvitationsFromUser($userId, $relations = [])
    {
        return $this->model->invitations()
            ->where('inviter_id', $userId)
            ->with($relations)
            ->get();
    }

    public function getInvitationsToUser($userId, $relations = [])
    {
        return $this->model->invitations()
            ->where('user_id', $userId)
            ->with($relations)
            ->get();
    }

    public function findInvitationById($id, $relations = [])
    {
        return $this->model->invitations()
            ->with($relations)
            ->findOrFail($id);
    }

    public function createInvitation($data)
    {
        return $this->model->create($data);
    }

    public function updateInvitation($id, $data)
    {
        $invitation = $this->model->findOrFail($id);
        $invitation->update($data);
        return $invitation;
    }

    public function deleteInvitation($id)
    {
        $this->model->where('id', $id)->delete();
    }

    public function refuseInvitation($id)
    {
        $invitation = $this->model->findOrFail($id);
        $invitation->refused_at = now();
        $invitation->save();
        return $invitation;
    }
}
