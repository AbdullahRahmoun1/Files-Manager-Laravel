<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\File;
use Wever\Laradot\App\Services\DotService;
use App\Models\FileHistory;

class FileHistoryService extends DotService
{
    public function __construct()
    {
        parent::__construct(FileHistory::class);
    }
    public function createVersion(File $file,CheckIn|null $checkIn,$path){
        $this->dotCreate([
            'path' => $path,
            'file_id' =>$file->id,
            'check_in_id' => $checkIn->id??null,
            'version' => $this->getNextVersion($file)
        ]);
    }
    public function getNextVersion(File $file){
        $oldVersion = $file->histories()->first()->version??0.9;
        return $oldVersion+0.1;
    }

    public function dotShow($id,$query=null){
        $model = parent::dotShow($id);
        $model->load(['file','checkIn']);
        return $model;
    }

}

