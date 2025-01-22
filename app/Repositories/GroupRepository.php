<?php

namespace App\Repositories;

use App\Models\Group;
use Wever\Laradot\App\Repositories\DotRepository;

class GroupRepository extends DotRepository
{
    public function __construct()
    {
        parent::__construct(app(Group::class));
    }

    public function getUserGroupsWithFiles($userId)
    {
        return $this->model
            ->whereHas('members', fn($query) => $query->where('user_id', $userId))
            ->with('files')
            ->get();
    }

    public function getUserGroupsWithFilesAndMembers($userId)
    {
        return $this->model
            ->whereHas('members', fn($query) => $query->where('user_id', $userId))
            ->with(['files', 'members'])
            ->get();
    }

    public function findByIdWithRelations($id, $relations = [])
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    public function isUserInGroup($groupId, $userId)
    {
        return $this->model
            ->where('id', $groupId)
            ->whereHas('members', fn($query) => $query->where('user_id', $userId))
            ->exists();
    }

}
