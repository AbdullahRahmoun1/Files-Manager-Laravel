<?php

namespace App\Repositories;

use App\Models\FileHistory;
use App\Models\User;

class FileHistoryRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(FileHistory::class);
    }

}
