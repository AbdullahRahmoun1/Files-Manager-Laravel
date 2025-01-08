<?php

namespace App\Services;
use Wever\Laradot\App\Services\DotService;
use App\Models\User;

class UserService extends DotService
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function dotAll($query=null){
        $query = User::filter();
        return parent::dotAll($query);
    }
}

