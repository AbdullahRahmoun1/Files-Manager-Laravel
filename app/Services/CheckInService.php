<?php
namespace App\Services;

use Wever\Laradot\App\Services\DotService;
use App\Repositories\CheckInRepository;
use App\Repositories\FileRepository;

class CheckInService extends DotService
{
    protected $checkInRepository;
    protected $fileRepository;

    public function __construct(CheckInRepository $checkInRepository, FileRepository $fileRepository)
    {
        parent::__construct($checkInRepository->getModel());
        $this->checkInRepository = $checkInRepository;
        $this->fileRepository = $fileRepository;
    }

    public function checkIn($file_id, $notify = true)
    {
        $file = $this->fileRepository->lockAndFind($file_id);
        $user = request()->user();

        if (!$this->fileRepository->hasGroups($file)) {
            throwError("This file isn't attached to any group yet.");
        }

        if ($this->fileRepository->hasActiveCheckIns($file)) {
            throwError("This file is already checked-in by a user.");
        }

        $model = $this->checkInRepository->create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'checked_in_at' => now()
        ]);

        if ($notify) {
            $group = $this->fileRepository->getFirstGroup($file);
            app('firebase')->sendMultipleUsers(
                $group->members,
                __('notifications.group.file.check-in.title'),
                __('notifications.group.file.check-in.body', [
                    'groupName' => $group->name,
                    'fileName' => $file->name,
                ])
            );
        }

        return $model;
    }

    public function bulkCheckIn(array $file_ids)
    {
        $result = [];
        foreach ($file_ids as $file_id) {
            $result[] = $this->checkIn($file_id, false);
        }

        $files = $this->fileRepository->getFilesWithGroups($file_ids);
        foreach ($files as $file) {
            $group = $this->fileRepository->getFirstGroup($file);
            app('firebase')->sendMultipleUsers(
                $group->members,
                __('notifications.group.file.check-in.title'),
                __('notifications.group.file.check-in.body', [
                    'groupName' => $group->name,
                    'fileName' => $file->name,
                ])
            );
        }

        return $result;
    }

    public function checkOut($file_id, $user = null)
    {
        $file = $this->fileRepository->findOrFail($file_id);
        $user ??= request()->user();

        $checkIn = $this->checkInRepository->getActiveCheckIn($file, $user->id);

        if (!$checkIn) {
            throwError("You can't check-out when you didn't check-in.");
        }

        if (request()->hasFile('file')) {
            $newFile = request()->file('file');
            $this->fileRepository->validateFileExtension($newFile, $file);

            $oldFilePath = $file->path;
            $this->fileRepository->storeFile($file, 'path', $newFile);

            app(FileHistoryService::class)->createVersion($file, $checkIn, $oldFilePath);
        }

        $model = $this->checkInRepository->updateCheckOut($checkIn, now());

        $group = $this->fileRepository->getFirstGroup($file);
        app('firebase')->sendMultipleUsers(
            $group->members,
            __('notifications.group.file.check-out.title'),
            __('notifications.group.file.check-out.body', [
                'groupName' => $group->name,
                'fileName' => $file->name,
            ])
        );
        return $model;
    }
}
