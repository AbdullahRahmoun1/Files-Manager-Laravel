<?php

namespace App\Repositories;

use App\Models\GroupUser;

class GroupUserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(GroupUser::class);
    }

}
