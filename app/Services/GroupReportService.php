<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupFile;
use App\Models\GroupUser;
use App\Models\File;
use App\Enums\GroupFileStatusEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GroupReportService
{
    private $fileReportService;

    public function __construct(FileReportService $fileReportService)
    {
        $this->fileReportService = $fileReportService;
    }

    public function canFilter($group)
    {
        $user = request()->user();
        return $user->id == $group->creator_id;
    }
    public function getReport(Group $group, $user_id = null)
    {
        if ($user_id && !$this->canFilter($group)) {
            throwError("Only group admin can filter the report based on user.");
        }
        $include = $this->extractIncludeParams() ?? [
            'creation_related' => true,
            'file_management_related' => true,
            'check_inNout_related' => true,
            'member_management_related' => true,
        ];
        $reports = [];
        if ($include['creation_related'] ?? false) {
            $reports[] = $this->getCreationRelated($group);
        }
        if ($include['file_management_related'] ?? false) {
            $reports = array_merge($reports, $this->getFilesManagementRelated($group));
        }
        if ($include['check_inNout_related'] ?? false) {
            $reports = array_merge($reports, $this->getCheckInOutRelated($group, $user_id));
        }
        if ($include['member_management_related'] ?? false) {
            $reports = array_merge($reports, $this->getMembersManagementRelated($group));
        }
        usort($reports, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        return $reports;
    }

    public function getPdfReport(Group $group,$user_id =null)
    {
        $result = $this->getReport($group,$user_id);
        $pdfData = [
            'name' => $group->name,
            'group_admin' => $group->creator->name,
            'created_at' => Carbon::parse($group->created_at)->format('Y-m-d h:i A'),
            'logs' => $result
        ];
        $pdf = Pdf::loadView('group_report_template', ['data' => $pdfData]);
        return $pdf->download();
    }

    public function extractIncludeParams()
    {
        $includeParams = collect(request()->all())
            ->filter(function ($value, $key) {
                return str_starts_with($key, 'include_');
            })
            ->mapWithKeys(function ($value, $key) {
                $newKey = str_replace('include_', '', $key);
                return [$newKey => $value];
            })
            ->toArray();
        return empty($includeParams) ? null : $includeParams;
    }

    private function getCreationRelated(Group $group)
    {
        return $this->getReportLine("Group created.", $group->created_at);
    }

    private function getFilesManagementRelated(Group $group)
    {
        $reports = [];
        $groupFiles = GroupFile::where('group_id', $group->id)->with('file.creator')->get();

        foreach ($groupFiles as $gFile) {
            $file = $gFile->file;
            $user = $file->creator;
            $fileName = $file->path ? "file '$file->name.$file->extension'" : "folder $file->name";

            if ($user->id == $group->creator_id) {
                $reports[] = $this->getReportLine(
                    "Group owner '$user->name' added a new $fileName.",
                    $gFile->created_at
                );
                continue;
            }

            $reports[] = $this->getReportLine(
                "User '$user->name' requested to add a new $fileName.",
                $gFile->created_at
            );

            if ($gFile->decided_at) {
                $message = $gFile->status == GroupFileStatusEnum::ACCEPTED->value
                    ? "Group owner approved adding the new $fileName uploaded by user $user->name."
                    : "Group owner denied adding the new $fileName uploaded by user $user->name.";
                $reports[] = $this->getReportLine($message, $gFile->decided_at);
            }
        }

        return $reports;
    }

    private function getCheckInOutRelated(Group $group, $user_id = null)
    {
        $reports = [];
        $groupFiles = GroupFile::where('group_id', $group->id)->pluck('file_id');
        $files = File::whereIn('id', $groupFiles)->get();
        foreach ($files as $file) {
            $reports = array_merge($reports, $this->fileReportService->getCheckInOutRelated($file, true, $user_id));
        }

        return $reports;
    }

    private function getMembersManagementRelated(Group $group)
    {
        $reports = [];
        $groupUsers = GroupUser::where('group_id', $group->id)->with('inviter', 'user')->get();

        foreach ($groupUsers as $gUser) {
            if ($gUser->user_id == $group->creator_id) continue;

            $reports[] = $this->getReportLine(
                "Group admin '{$gUser->inviter->name}' invited '{$gUser->user->name}' to join the group.",
                $gUser->created_at
            );

            if ($gUser->joined_at) {
                $reports[] = $this->getReportLine(
                    "User '{$gUser->user->name}' has accepted the invitation to join the group.",
                    $gUser->joined_at
                );
            }

            if ($gUser->refused_at) {
                $reports[] = $this->getReportLine(
                    "User '{$gUser->user->name}' has refused the invitation to join the group.",
                    $gUser->refused_at
                );
            }

            if ($gUser->kicked_at) {
                $reports[] = $this->getReportLine(
                    "User '{$gUser->user->name}' got kicked out from the group by group owner.",
                    $gUser->kicked_at
                );
            }

            if ($gUser->left_at) {
                $reports[] = $this->getReportLine(
                    "User '{$gUser->user->name}' left the group.",
                    $gUser->left_at
                );
            }

            if (
                $gUser->invitation_expires_at &&
                !$gUser->joined_at &&
                !$gUser->refused_at &&
                now() > $gUser->invitation_expires_at
            ) {
                $reports[] = $this->getReportLine(
                    "User '{$gUser->user->name}' group invitation has expired.",
                    $gUser->invitation_expires_at
                );
            }
        }

        return $reports;
    }

    private function getReportLine($msg, $date)
    {
        return [
            'message' => $msg,
            'date' => Carbon::parse($date)->format('Y-m-d h:i A')
        ];
    }
}
