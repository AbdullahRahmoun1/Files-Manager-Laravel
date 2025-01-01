<?php

namespace App\Services;
use Wever\Laradot\App\Services\DotService;
use App\Models\GroupFile;

class GroupFileService extends DotService
{
    public function __construct()
    {
        parent::__construct(GroupFile::class);
    }
}

