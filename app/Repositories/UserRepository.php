<?php

namespace App\Repositories;

use App\Models\User;
use Wever\Laradot\App\Repositories\DotRepository;

class UserRepository extends DotRepository
{
    public function __construct()
    {
        parent::__construct(app(User::class));
    }

}
