<?php

namespace App\Repositories;

use App\Models\GroupUser;
use Wever\Laradot\App\Repositories\DotRepository;

class GroupUserRepository extends DotRepository
{
    public function __construct()
    {
        parent::__construct(app(GroupUser::class));
    }

}
