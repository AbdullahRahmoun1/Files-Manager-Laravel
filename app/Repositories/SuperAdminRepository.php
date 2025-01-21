<?php

namespace App\Repositories;

use App\Models\SuperAdmin;
use Wever\Laradot\App\Repositories\DotRepository;

class SuperAdminRepository extends DotRepository
{
    public function __construct()
    {
        parent::__construct(app(SuperAdmin::class));
    }

}
