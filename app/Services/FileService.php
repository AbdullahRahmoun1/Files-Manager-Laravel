<?php

namespace App\Services;
use Wever\Laradot\App\Services\DotService;
use App\Models\File;

class FileService extends DotService
{
    public function __construct(File $file)
    {
        parent::__construct(File::class);
    }

    public function dotCreate($data){
        $data['creator_id'] = request()->user()->id;
        unset($data['path']);
        $fileModel = parent::dotCreate($data);
        if(!$data['is_folder']){
            $file = request()->file('path');
            $data['extension'] = $file->getClientOriginalExtension();
            $fileModel->storeFile('path',$file);
        }
        return $fileModel;
    }
}

