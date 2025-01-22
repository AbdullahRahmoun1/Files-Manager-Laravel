<?php
namespace App\Repositories;

use App\Models\CheckIn;
use App\Models\File;
use App\Models\FileHistory;
use Wever\Laradot\App\Repositories\DotRepository;

class FileHistoryRepository extends DotRepository
{
    public function __construct(FileHistory $model)
    {
        parent::__construct($model);
    }

    /**
     * Get the latest history record for a file.
     */
    public function getLastHistory(File $file)
    {
        return $file->histories()->first();
    }

    /**
     * Create a new history record for a file.
     */
    public function createHistory(File $file, ?CheckIn $checkIn, string $path, float $version)
    {
        return $this->create([
            'file_id' => $file->id,
            'check_in_id' => $checkIn->id ?? null,
            'path' => $path,
            'version' => $version,
        ]);
    }

    /**
     * Update the comparison string in a file history.
     */
    public function updateComparison(FileHistory $history, string $comparison)
    {
        $history->comparison = $comparison;
        $history->save();
        return $history;
    }
}
