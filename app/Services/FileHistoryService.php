<?php

namespace App\Services;

use App\Models\File;
use App\Models\CheckIn;
use App\Repositories\FileHistoryRepository;
use Wever\Laradot\App\Services\DotService;

class FileHistoryService extends DotService
{
    protected $fileHistoryRepository;

    public function __construct(FileHistoryRepository $fileHistoryRepository)
    {
        parent::__construct($fileHistoryRepository->getModel());
        $this->fileHistoryRepository = $fileHistoryRepository;
    }

    public function createVersion(File $file, ?CheckIn $checkIn, string $path)
    {
        $lastHistory = $this->fileHistoryRepository->getLastHistory($file);

        $history = $this->fileHistoryRepository->createHistory(
            $file,
            $checkIn,
            $path,
            $this->getNextVersion($file)
        );

        if ($lastHistory && $lastHistory->path) {
            dispatch(function () use ($history, $lastHistory) {
                $diffString = app(FileComparisonService::class)->compare(
                    $lastHistory->path,
                    $history->path
                );

                $this->fileHistoryRepository->updateComparison($history, $diffString);
            });
        }

        return $history;
    }

    public function getNextVersion(File $file)
    {
        $lastHistory = $this->fileHistoryRepository->getLastHistory($file);
        $oldVersion = $lastHistory->version ?? 0.9;
        return round($oldVersion + 0.1, 1);
    }

    public function dotShow($id, $query = null)
    {
        $model = parent::dotShow($id);
        $model->load(['file', 'checkIn']);
        return $model;
    }
}
