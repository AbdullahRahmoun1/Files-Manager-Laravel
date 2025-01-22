<?php

namespace App\Services;
use App\Models\File;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function getPdfFileReport(File $file)
    {
        $result = $this->getFileReport($file);
        $pdfData = [
            'name' => $file->name . ($file->extension ? ".$file->extension" : ""),
            'current_version' => $file->histories()->first()->version,
            'created_by' => $file->creator->name,
            'groupName' => $file->groups()->first()->name??"N/A",
            'fileLogs' => $result
        ];
        $pdf = Pdf::loadView('file_report_template', ['data' => $pdfData]);
        return $pdf->download();
    }

    public function getCreationRelated(File $file)
    {
        $fileOrFolder = $file->path == null ? "Folder" : "File";
        return $this->getReportLine("$fileOrFolder created.", $file->created_at, 'create',$file->creator->name);
    }

    public function getCheckInOutRelated(File $file, $withInfo = false, $user_id = null)
    {
        $checkInOutReports = [];
        $file->load('checkIns.user');
        foreach ($file->checkIns as $checkIn) {
            $user = $checkIn->user;
            if ($user_id && $user_id != $user->id) {
                continue;
            }
            // Check-in report line
            $checkInMessage = "User $user->name checked-in.";
            if ($withInfo) {
                $checkInMessage .= " File: $file->name.";
            }
            $checkInOutReports[] = $this->getReportLine($checkInMessage, $checkIn->checked_in_at, 'check-in',$checkIn->user->name);

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
                $checkInOutReports[] = $this->getReportLine($checkOutMessage, $checkIn->checked_out_at, 'check-out',$checkIn->user->name);
            }
        }
        return $checkInOutReports;
    }

    private function getReportLine($msg, $date, $operation,string $user)
    {
        return [
            'message' => $msg,
            'date' => Carbon::parse($date)->format('Y-m-d h:i A'),
            'operation' => $operation,
            'user' => $user
        ];
    }
}
