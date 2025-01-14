<?php

namespace App\Services;

use Wever\Laradot\App\Services\DotService;
use App\Models\CheckIn;
use App\Models\File;

class CheckInService extends DotService
{
    public function __construct()
    {
        parent::__construct(CheckIn::class);
    }
    public function checkIn($file_id)
    {
        $file = File::lockForUpdate()->findOrFail($file_id);
        $user = request()->user();

        if ($file->activeCheckIns()->exists()){
            throwError("This file is already checked-in by a user.");
        }
        return $this->dotCreate([
            'file_id'=>$file->id,
            'user_id' => $user->id,
            'checked_in_at' => now()
        ]);
    }

    public function bulkCheckIn(array $files_ids){
        $result = [];
        foreach($files_ids as $fId){
            $result[] = $this->checkIn($fId);
        }
        return $result;
    }
    public function checkOut($file_id) {
        $file = File::findOrFail($file_id);
        $user = request()->user();
        $checkIn = $file->activeCheckIns()->where('user_id',$user->id)->first();
        if(!$checkIn){
            throwError("You can't check-out when you didn't check-in.");
        }
        if(request()->hasFile('file')){
            $newFile = request()->file('file');
            $oldFilePath = $file->path;
            if($newFile->getClientOriginalExtension() != $file->extension){
                throwError("Thew new file should have the same extension as the original one.");
            }
            $file->storeFile('path',$newFile,false);
            //history stuff
            app(FileHistoryService::class)->createVersion(
                $file,$checkIn,$file->path
            );
        }
        return $this->dotUpdate($checkIn,[
            'checked_out_at' => now()
        ]);
    }
}
