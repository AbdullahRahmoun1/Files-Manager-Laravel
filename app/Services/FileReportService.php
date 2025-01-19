<?php

namespace App\Services;

use App\Models\File;
use Carbon\Carbon;

class FileReportService
{
    public function getFileReport(File $file)
    {
        $reports = [];
        $reports[] = $this->getCreationRelated($file);
        $reports = array_merge($reports, $this->getCheckInOutRelated($file));

        return array_reverse($reports);
    }

    public function getCreationRelated(File $file)
    {
        $fileOrFolder = $file->path == null ? "Folder" : "File";
        return $this->getReportLine("$fileOrFolder created.", $file->created_at,'create');
    }

    public function getCheckInOutRelated(File $file, $withInfo = false,$user_id=null)
    {
        $checkInOutReports = [];
        $file->load('checkIns.user');
        foreach ($file->checkIns as $checkIn) {
            $user = $checkIn->user;
            if($user_id && $user_id!=$user->id){
                continue;
            }
            // Check-in report line
            $checkInMessage = "User $user->name checked-in.";
            if ($withInfo) {
                $checkInMessage .= " File: $file->name.";
            }
            $checkInOutReports[] = $this->getReportLine($checkInMessage, $checkIn->checked_in_at,'check-in');

            // Check-out report line (if exists)
            if ($checkIn->checked_out_at) {
                $checkOutMessage = "User $user->name checked-out";
                if ($checkIn->fileVersion) {
                    $version = $checkIn->fileVersion->version;
                    $checkOutMessage .= ", Version (V$version) created.";
                } else {
                    $checkOutMessage .= ".";
                }
                if ($withInfo) {
                    $checkOutMessage .= " File: $file->name.";
                }
                $checkInOutReports[] = $this->getReportLine($checkOutMessage, $checkIn->checked_out_at,'check-out');
            }
        }
        return $checkInOutReports;
    }

    private function getReportLine($msg, $date,$operation)
    {
        return [
            'message' => $msg,
            'date' => Carbon::parse($date)->toDateTimeString(),
            'operation' => $operation
        ];
    }
}
