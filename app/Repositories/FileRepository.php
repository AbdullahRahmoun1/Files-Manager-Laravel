<?php
namespace App\Repositories;

use App\Models\File;
use App\Models\Group;
use App\Models\GroupFile;
use Illuminate\Support\Facades\Storage;
use Wever\Laradot\App\Repositories\DotRepository;

class FileRepository extends DotRepository
{
    public function __construct(File $model)
    {
        parent::__construct($model);
    }

    /**
     * Lock a file for update and find it by ID.
     */
    public function lockAndFind($fileId)
    {
        return $this->model->lockForUpdate()->findOrFail($fileId);
    }

    /**
     * Check if a file has groups attached.
     */
    public function hasGroups($file)
    {
        return $file->groups()->exists();
    }

    /**
     * Check if a file has active check-ins.
     */
    public function hasActiveCheckIns($file)
    {
        return $file->activeCheckIns()->exists();
    }

    /**
     * Get the first group of a file.
     */
    public function getFirstGroup($file)
    {
        return $file->groups()->first();
    }

    /**
     * Get files with their groups and members.
     */
    public function getFilesWithGroups(array $fileIds)
    {
        return $this->model->with(['groups.members'])->whereIn('id', $fileIds)->get();
    }

    /**
     * Validate if a new file has the same extension as the existing file.
     */
    public function validateFileExtension($newFile, $existingFile)
    {
        if ($newFile->getClientOriginalExtension() != $existingFile->extension) {
            throwError("The new file should have the same extension as the original one.");
        }
    }

    /**
     * Store a file and update its path.
     */
    public function storeFile($file, $pathColumn, $newFile, $deleteOld = false)
    {
        if ($deleteOld) {
            Storage::delete($file->$pathColumn);
        }
        $newPath = $newFile->store('files');
        $file->update([$pathColumn => $newPath]);
    }

    public function getGroupFiles(Group $group, string $status = null)
    {
        $query = $group->files();

        if ($status) {
            $query->wherePivot('status', $status);
        }

        return $query->get();
    }

    public function getActiveGroupFile(File $file)
    {
        return GroupFile::active()
            ->where('file_id', $file->id)
            ->firstOrFail();
    }

    public function softDelete(File $file)
    {
        $file->deleted_at = now();
        $file->save();
        return $file;
    }


}
