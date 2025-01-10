<?php

namespace App\Http\Controllers;

use Wever\Laradot\App\Http\Controllers\DotController;
use App\Http\Requests\CreateFileRequest;
use App\Http\Requests\DecideFileStatusRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Group;
use App\Services\FileService;
use Illuminate\Http\Request;

class FileController extends DotController
{
    public function __construct()
    {
        parent::__construct(
            FileService::class,
            CreateFileRequest::class,
            UpdateFileRequest::class,
            FileResource::class
        );
    }

    public function dotCreate(Request $request)
    {
        $data = $this->createRequest()->validated();
        $result = $this->service->dotCreate($data);
        return self::success(
            FileResource::make($result['file']),
            $result['message']
        );
    }
    public function getChildren(File $file)
    {
        return $this->success(
            FileResource::collection($this->service->getChildren($file))
        );
    }

    public function getPendingFiles(Group $group)
    {
        return $this->success(
            FileResource::collection($this->service->getPendingFiles($group))
        );
    }
    public function decideFileStatus(DecideFileStatusRequest $request)
    {
        $this->service->decideFileStatus($request->validated());
        return $this->success();
    }
}
