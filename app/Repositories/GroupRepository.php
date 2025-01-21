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

}
