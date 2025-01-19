<?php

namespace App\Http\Controllers;

use Wever\Laradot\App\Http\Controllers\DotController;
use App\Http\Requests\CreateFileRequest;
use App\Http\Requests\DecideFileStatusRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Group;
use App\Services\FileReportService;
use App\Services\FileService;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function getFileReport(File $file){
        $reportService = app(FileReportService::class);
        $result = $reportService->getFileReport($file);
        if(request('pdf')??null){
            $pdfData = [
                'name' => "file",
                'status' => "status",
                'userName' => "username",
                'groupName' => "groupName",
                'fileLogs' => $result
            ];
            $pdf = Pdf::loadView('file_report_template', ['data' => $pdfData]);
            return $pdf->download();
        }
        return $this->success(
            $result
        );
    }

    public function removeFile(Group $group,File $file){
        return self::success($this->service->removeFile($group,$file));
    }
}
