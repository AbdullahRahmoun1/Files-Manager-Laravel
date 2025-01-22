<?php

namespace App\Services;

use App\Models\Group;
use App\Models\File;
use App\Repositories\FileRepository;
use App\Models\GroupFile;
use App\Enums\GroupFileStatusEnum;
use Illuminate\Support\Facades\Storage;
use Wever\Laradot\App\Services\DotService;

class FileService extends DotService
{
    protected $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        parent::__construct(File::class);
        $this->fileRepository = $fileRepository;
    }

    public function downloadFile(File $file)
    {
        $user = request()->user();
        // Validate user can access file
        return response()->download(Storage::disk('public')->path($file->path));
    }

    public function createFile($data)
    {
        $user = request()->user();
        $group = $user->groups()->where('groups.id', $data['group_id'])->first();

        if (!$group) {
            throwError("You don't have permission to create files in this group.");
        }

        $data['creator_id'] = $user->id;
        unset($data['path']);
        $file = $this->fileRepository->create($data);

        $isGroupOwner = $group->creator_id == $user->id;
        $file->groups()->attach($data['group_id'], [
            'status' => $isGroupOwner
                ? GroupFileStatusEnum::ACCEPTED
                : GroupFileStatusEnum::PENDING,
            'created_at' => now(),
        ]);

        if (!$data['is_folder']) {
            $uploadedFile = request()->file('path');
            $file->storeFile('path', $uploadedFile);
            $file->update(['extension' => $uploadedFile->getClientOriginalExtension()]);

            app(FileHistoryService::class)->createVersion($file, null, $file->path);
        }

        $this->notifyGroup($group, $file, $isGroupOwner);

        return [
            'message' => $isGroupOwner ? "Success." : "Waiting for group admin's approval.",
            'file' => $file,
        ];
    }

    public function removeFile(Group $group, File $file)
    {
        if (request()->user()->id != $group->creator_id) {
            throwError("Only group admins can remove files/folders.");
        }

        $groupFile = $this->fileRepository->getActiveGroupFile($file);
        $groupFile->removed_at = now();
        $groupFile->save();

        $this->fileRepository->softDelete($file);

        app('firebase')->sendMultipleUsers(
            $group->members,
            __('notifications.group.file.removed.title'),
            __(
                'notifications.group.file.removed.body',
                [
                    'groupName' => $group->name,
                    'fileName' => $file->name,
                ]
            )
        );
    }

    public function getPendingFiles(Group $group)
    {
        $user = request()->user();

        if ($group->creator_id != $user->id) {
            throwError("You don't have permission to do this.");
        }

        return $this->fileRepository->getGroupFiles($group, GroupFileStatusEnum::PENDING->value);
    }

    public function decideFileStatus($data)
    {
        $group = Group::findOrFail($data['group_id']);
        $user = request()->user();

        if ($group->creator_id != $user->id) {
            throwError("You don't have permission to do this.");
        }

        $groupFile = GroupFile::where('group_id', $data['group_id'])
            ->where('file_id', $data['file_id'])
            ->where('status', GroupFileStatusEnum::PENDING)
            ->firstOrFail();

        $groupFile->status = $data['status'];
        $groupFile->decided_at = now();
        $groupFile->save();

        $this->notifyDecision($group, $groupFile, $data['status']);
    }

    protected function notifyGroup(Group $group, File $file, bool $isGroupOwner)
    {
        $title = $isGroupOwner
            ? __('notifications.group.file.created.title')
            : __('notifications.group.file.add-request.title');

        $body = $isGroupOwner
            ? __('notifications.group.file.created.body', ['groupName' => $group->name, 'fileName' => $file->name])
            : __('notifications.group.file.add-request.body', ['groupName' => $group->name, 'fileName' => $file->name]);

        app('firebase')->sendMultipleUsers($group->members, $title, $body);
    }

    protected function notifyDecision(Group $group, GroupFile $groupFile, string $status)
    {
        $file = $groupFile->file;
        if ($status == GroupFileStatusEnum::ACCEPTED->value) {
            app('firebase')->sendMultipleUsers(
                $group->members,
                __('notifications.group.file.created.title'),
                __('notifications.group.file.created.body', ['groupName' => $group->name, 'fileName' => $file->name])
            );
            app('firebase')->send(
                $file->creator,
                __('notifications.group.file.add-request-approved.title'),
                __('notifications.group.file.add-request-approved.body', ['groupName' => $group->name, 'fileName' => $file->name])
            );
        } else {
            app('firebase')->send(
                $file->creator,
                __('notifications.group.file.add-request-rejected.title'),
                __('notifications.group.file.add-request-rejected.body', ['groupName' => $group->name, 'fileName' => $file->name])
            );
        }
    }


    public function downloadFile(File $file)
    {
        $user = request()->user();
        //validate user can access file
        return response()->download(Storage::disk('public')->path($file->path));
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
        if ($isGroupOwner) {
            app('firebase')->sendMultipleUsers(
                $group->members,
                __('notifications.group.file.created.title'),
                __(
                    'notifications.group.file.created.body',
                    [
                        'groupName' => $group->name,
                        'fileName' => $fileModel->name,
                    ]
                ),
            );
        } else {
            app('firebase')->sendMultipleUsers(
                $group->members,
                __('notifications.group.file.add-request.title'),
                __(
                    'notifications.group.file.add-request.body',
                    [
                        'groupName' => $group->name,
                        'fileName' => $fileModel->name,
                    ]
                ),
            );
        }
        return [
            'message' => $isGroupOwner ? "Success." : "Success!, waiting for group admin's approval.",
            'file' => $fileModel
        ];
    }

    public function removeFile(Group $group, File $file)
    {
        if (request()->user()->id != $group->creator_id) {
            throwError("Only group admin can remove files/folders.");
        }
        $gFile = GroupFile::active()->where('file_id', $file->id)->firstOrFail();
        $gFile->removed_at = now();
        $gFile->save();
        $file->deleted_at = now();
        $file->save();
        app('firebase')->sendMultipleUsers(
            $group->members,
            __('notifications.group.file.removed.title'),
            __(
                'notifications.group.file.removed.body',
                [
                    'groupName' => $group->name,
                    'fileName' => $file->name,
                ]
            ),
        );
    }

    public function getChildren(File $file)
    {
        if ($file->path) {
            throwError("Files don't have children!");
        }
        return $file->directChildren;
    }

    public function rename(File $file, $name)
    {
        if (!$name) {
            throwError("Name is required.");
        }
        $file->name = request('name');
        $file->save();
        return $file;
    }
}
