<?php

namespace App\Services;
use Wever\Laradot\App\Services\DotService;
use App\Models\SuperAdmin;

class SuperAdminService extends DotService
{
    public function __construct()
    {
        parent::__construct(SuperAdmin::class);
    }
}

