<?php

namespace App\Repositories;

use App\Models\GroupFile;

class GroupFileRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(GroupFile::class);
    }

}
