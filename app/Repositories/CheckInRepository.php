<?php

namespace App\Repositories;

use App\Models\CheckIn;
use Wever\Laradot\App\Repositories\DotRepository;

class CheckInRepository extends DotRepository
{
    public function __construct()
    {
        parent::__construct(app(CheckIn::class));
    }

}
