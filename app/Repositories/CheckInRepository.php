<?php

namespace App\Repositories;

use App\Models\CheckIn;
use Wever\Laradot\App\Repositories\DotRepository;

class CheckInRepository extends DotRepository
{
    public function __construct(CheckIn $model)
    {
        parent::__construct($model);
    }

    /**
     * Get the active check-in for a file by a specific user.
     */
    public function getActiveCheckIn($file, $userId)
    {
        return $file->activeCheckIns()->where('user_id', $userId)->first();
    }

    /**
     * Update the check-out time for a check-in record.
     */
    public function updateCheckOut($checkIn, $time)
    {
        return $this->update($checkIn, ['checked_out_at' => $time]);
    }
}
