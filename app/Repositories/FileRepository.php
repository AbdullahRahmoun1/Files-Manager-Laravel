<?php

namespace App\Repositories;

use App\Models\File;
use Wever\Laradot\App\Repositories\DotRepository;

class FileRepository extends DotRepository
{
    public function __construct()
    {
        parent::__construct(app(File::class));
    }

}
