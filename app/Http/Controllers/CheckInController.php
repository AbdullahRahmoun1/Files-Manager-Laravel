<?php

namespace App\Http\Controllers;
use Wever\Laradot\App\Http\Controllers\DotController;
use App\Http\Requests\CreateCheckInRequest;
use App\Http\Requests\UpdateCheckInRequest;
use App\Http\Resources\CheckInResource;
use App\Models\File;
use App\Services\CheckInService;


class CheckInController extends DotController
{
    public function __construct() {
        parent::__construct(
            CheckInService::class,
            CreateCheckInRequest::class,
            UpdateCheckInRequest::class,
            CheckInResource::class
        );
    }

    public function checkIn($file_id){
        $data= $this->service->checkIn($file_id);
        return self::success(CheckInResource::make($data));
    }
    public function checkOut($file_id){
        $model=$this->service->checkOut($file_id);
        return self::success(CheckInResource::make($model));
    }

    public function bulkCheckIn($files_ids){
        $data= $this->service->bulkCheckIn(explode(',',$files_ids));
        return self::success(CheckInResource::collection($data));
    }
}
