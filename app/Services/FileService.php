<?php

namespace App\Services;
use Wever\Laradot\App\Services\DotService;
use App\Models\File;
use App\Models\Group;

class FileService extends DotService
{
    public function __construct(File $file)
    {
        parent::__construct(File::class);
    }

    public function dotCreate($data){
        $user = request()->user();
        $groupAllowed = $user->groups()->where('groups.id',$data['group_id'])->exists();
        if(!$groupAllowed){
            throwError("You don't have the permission to create files in this group.");
        }
        $data['creator_id'] = request()->user()->id;
        unset($data['path']);
        $fileModel = parent::dotCreate($data);
        $fileModel->groups()->attach($data['group_id']);
        if(!$data['is_folder']){
            $file = request()->file('path');
            $fileModel->storeFile('path',$file);
            $fileModel->update(['extension'=>$file->getClientOriginalExtension()]);
        }
        return $fileModel;
    }

    public function getChildren(File $file){
        if($file->path){
            throwError("Files don't have children!");
        }
        return $file->directChildren;
    }
}

