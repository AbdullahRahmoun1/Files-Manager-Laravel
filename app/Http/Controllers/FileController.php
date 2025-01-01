<?php

namespace App\Http\Controllers;
use Wever\Laradot\App\Http\Controllers\DotController;
use App\Http\Requests\CreateFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Services\FileService;


class FileController extends DotController
{
    public function __construct() {
        parent::__construct(
            FileService::class,
            CreateFileRequest::class,
            UpdateFileRequest::class,
            FileResource::class
        );
    }

    public function getChildren(File $file){
        return $this->success(
            FileResource::collection($this->service->getChildren($file))
        );
    }
}
