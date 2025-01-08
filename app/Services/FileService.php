<?php

namespace App\Services;

use App\Enums\GroupFileStatusEnum;
use Wever\Laradot\App\Services\DotService;
use App\Models\File;
use App\Models\Group;
use App\Models\GroupFile;

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
                GroupFileStatusEnum::PENDING
        ]);
        if (!$data['is_folder']) {
            $file = request()->file('path');
            $fileModel->storeFile('path', $file);
            $fileModel->update(['extension' => $file->getClientOriginalExtension()]);
        }
        return [
            'message' => $isGroupOwner ? "File Added Successfully." : "Success!, waiting for group admin's approval.",
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

    public function getFilesToApprove(Group $group)
    {
        return $group->unacceptedFiles;
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
            // ->ddRawSql();
            ->firstOrFail();
        $fileGroup->status = $data['status'];
        $fileGroup->save();
    }
}
