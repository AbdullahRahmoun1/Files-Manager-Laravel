<?php

namespace App\Repositories;

use App\Models\GroupFile;
use Wever\Laradot\App\Repositories\DotRepository;

class GroupFileRepository extends DotRepository
{
    public function __construct()
    {
        parent::__construct(app(GroupFile::class));
    }

}
