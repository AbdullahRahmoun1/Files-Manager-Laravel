<?php

namespace App\Repositories;

use App\Models\SuperAdmin;
use App\Models\User;

class SuperAdminRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(SuperAdmin::class);
    }

}
