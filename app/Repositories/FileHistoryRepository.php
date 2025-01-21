<?php

namespace App\Repositories;

use App\Models\FileHistory;
use Wever\Laradot\App\Repositories\DotRepository;

class FileHistoryRepository extends DotRepository
{
    public function __construct()
    {
        parent::__construct(app(FileHistory::class));
    }

}
