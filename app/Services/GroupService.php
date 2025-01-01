<?php

namespace App\Services;

use Wever\Laradot\App\Services\DotService;
use App\Models\Group;

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
        $query = request()->user()->groups()->with('files');
        return parent::dotAll($query);
    }
    public function dotShow($id)
    {
        if (!request()->user()->groups()->where('groups.id', $id)->exists()) {
            throwError("You don't have the permission to view this group.");
        }

        $model = parent::dotShow($id);
        $model->load('files');
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
}
