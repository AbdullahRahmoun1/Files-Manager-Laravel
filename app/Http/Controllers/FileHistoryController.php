<?php

namespace App\Http\Controllers;
use Wever\Laradot\App\Http\Controllers\DotController;
use App\Http\Requests\CreateFileHistoryRequest;
use App\Http\Requests\UpdateFileHistoryRequest;
use App\Http\Resources\FileHistoryResource;
use App\Services\FileHistoryService;


class FileHistoryController extends DotController
{
    public function __construct() {
        parent::__construct(
            FileHistoryService::class,
            CreateFileHistoryRequest::class,
            UpdateFileHistoryRequest::class,
            FileHistoryResource::class
        );
    }
}
