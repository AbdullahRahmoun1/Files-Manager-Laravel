<?php

namespace App\Services;

use App\Enums\GroupFileStatusEnum;
use Wever\Laradot\App\Services\DotService;
use App\Models\File;
use App\Models\Group;
use App\Models\GroupFile;
use Carbon\Carbon;
use PHPUnit\Framework\MockObject\ReturnValueGenerator;

class FileService extends DotService
{
    public function __construct(File $file)
    {
        parent::__construct(File::class);
    }

    public function dotCreate($data)
    {
        $user = request()->user();
        $group = $user->groups()->where('groups.id', $data['group_id'])->first();
        if (!$group) {
            throwError("You don't have the permission to create files in this group.");
        }
        $data['creator_id'] = request()->user()->id;
        unset($data['path']);
        $fileModel = parent::dotCreate($data);
        $isGroupOwner = $group->creator_id == $user->id;
        $fileModel->groups()->attach($data['group_id'], [
            'status' => $isGroupOwner ?
                GroupFileStatusEnum::ACCEPTED :
                GroupFileStatusEnum::PENDING,
            'created_at' => now()
        ]);
        if (!$data['is_folder']) {
            $file = request()->file('path');
            $fileModel->storeFile('path', $file);
            $fileModel->update(['extension' => $file->getClientOriginalExtension()]);
            app(FileHistoryService::class)
                ->createVersion($fileModel, null, $fileModel->path);
        }
        return [
            'message' => $isGroupOwner ? "Success." : "Success!, waiting for group admin's approval.",
            'file' => $fileModel
        ];
    }

    public function getChildren(File $file)
    {
        if ($file->path) {
            throwError("Files don't have children!");
        }
        return $file->directChildren;
    }

    public function getPendingFiles(Group $group)
    {
        $user = request()->user();
        if ($group->creator_id != $user->id) {
            throwError("You don't have the permission to do this.");
        }
        return $group->pendingFiles;
    }

    public function decideFileStatus($data)
    {
        $group = Group::findOrFail($data['group_id']);
        $user = request()->user();
        if ($group->creator_id != $user->id) {
            throwError("You don't have the permission to do this.");
        }
        $fileGroup = GroupFile::where('group_id', $data['group_id'])
            ->where('file_id', $data['file_id'])
            ->where('status', GroupFileStatusEnum::PENDING)
            ->firstOrFail();
        $fileGroup->status = $data['status'];
        $fileGroup->decided_at = now();
        $fileGroup->save();
    }

    public function getFileReport(File $file){
        $reports = [] ;
        $fileOrFolder = $file->path==null?"Folder":"File";
        $reports[] = $this->getReportLine("$fileOrFolder created.",$file->created_at);
        foreach($file->checkIns as $checkIn){
            $user = $checkIn->user;
            $reports[] = $this->getReportLine("User $user->name checked-in.",$checkIn->checked_in_at);
            if($checkIn->checked_out_at){
                $message = "User $user->name checked-out";
                if($checkIn->fileVersion){
                    $version = $checkIn->fileVersion->version;
                    $message.= ", Version (V$version) created.";
                }else {
                    $message.= ".";
                }
                $reports[] = $this->getReportLine($message,$checkIn->checked_out_at);
            }
        }
        return array_reverse($reports);
    }
    private function getReportLine($msg,$date,...$metaData){
        return [
            'message' => $msg,
            'date' => Carbon::parse($date)->toDateTimeString()
        ];
    }
}
