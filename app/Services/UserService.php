<?php

namespace App\Services;

use App\Enums\GroupFileStatusEnum;
use App\Models\CheckIn;
use App\Models\Group;
use App\Models\GroupFile;
use App\Models\GroupUser;
use Wever\Laradot\App\Services\DotService;
use App\Models\User;
use Carbon\Carbon;

class UserService extends DotService
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function dotAll($query = null)
    {
        $query = User::filter();
        return parent::dotAll($query);
    }

    public function getUserReport(User $user)
    {
        $reports = [];
        $reports[] = $this->getReportLine(
            "User registered.",
            $user->created_at
        );
        //groups
        $groupUsers = GroupUser::where('user_id', $user->id)->orWhere('inviter_id', $user->id)
            ->with('inviter', 'user', 'group')->get();
        foreach ($groupUsers as $gUser) {
            $group = $gUser->group;
            if ($gUser->inviter_id == $user->id) {
                //he sent the invitation
                if ($gUser->user_id == $group->creator_id) continue;
                $reports[] = $this->getReportLine(
                    "invited '{$gUser->user->name}' to join '{$group->name}' group.",
                    $gUser->created_at
                );
            } else {
                //he got the invitation
                $reports[] = $this->getReportLine(
                    "User received an invitation to join '$group->name' group.",
                    $gUser->created_at
                );
                if ($gUser->joined_at) {
                    $reports[] = $this->getReportLine(
                        "User has accepted the invitation to join '$group->name' group.",
                        $gUser->joined_at
                    );
                }
                if ($gUser->refused_at) {
                    $reports[] = $this->getReportLine(
                        "User has refused the invitation to join '$group->name' group.",
                        $gUser->refused_at
                    );
                }
                if ($gUser->kicked_at) {
                    $reports[] = $this->getReportLine(
                        "User got kicked out from '$group->name' group.",
                        $gUser->kicked_at
                    );
                }
            }
        }
        //files
        //.addition to group
        $groupFiles = GroupFile::with(['group', 'file.creator'])
            ->whereHas(
                'file.creator',
                fn($q) => $q->where('creator_id', $user->id)
            )->get();
        foreach ($groupFiles as $gFile) {
            $file = $gFile->file;
            $group = $gFile->group;
            $fileName = $file->path ?
                "file '$file->name.$file->extension'" :
                "folder '$file->name'";
            if ($user->id == $group->creator_id) {
                $reports[] = $this->getReportLine(
                    "User added a new $fileName to '$group->name' group.",
                    $gFile->created_at
                );
                continue;
            }
            $reports[] = $this->getReportLine(
                "User requested to add a new $fileName to '$group->name' group.",
                $gFile->created_at
            );
        }
        //.check in\out
        $checkIns = CheckIn::with('file.groups')->where('user_id', $user->id)->get();
        foreach ($checkIns as $checkIn) {
            $user = $checkIn->user;
            $file = $checkIn->file;
            $group = $file->groups()->first();
            $reports[] = $this->getReportLine("User checked-in on '$file->name.$file->extension' file inside '$group->name' group.", $checkIn->checked_in_at);
            if ($checkIn->checked_out_at) {
                $message = "User checked-out from '$file->name.$file->extension' file inside '$group->name' group.";
                $reports[] = $this->getReportLine($message, $checkIn->checked_out_at);
            }
        }
        usort($reports, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        return $reports;
    }
    private function getReportLine($msg, $date)
    {
        return [
            'message' => $msg,
            'date' => Carbon::parse($date)->toDateTimeString()
        ];
    }
}
