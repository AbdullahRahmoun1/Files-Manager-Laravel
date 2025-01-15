<?php

namespace App\Services;

use App\Enums\GroupFileStatusEnum;
use Wever\Laradot\App\Services\DotService;
use App\Models\Group;
use App\Models\GroupFile;
use App\Models\GroupUser;
use App\Models\User;
use Carbon\Carbon;

class GroupService extends DotService
{
    public function __construct()
    {
        parent::__construct(Group::class);
    }
    public function dotIndex($query = null)
    {
        $query = request()->user()->groups()->with('files');
        return parent::dotIndex($query);
    }
    public function dotAll($query = null)
    {
        $query = request()->user()->groups()->with('files');
        return parent::dotAll($query);
    }
    public function dotShow($id)
    {
        if (!request()->user()->groups()->where('groups.id', $id)->exists()) {
            throwError("You don't have the permission to view this group.");
        }

        $model = parent::dotShow($id);
        $model->load(['files','members']);
        return $model;
    }
    public function dotCreate($data)
    {
        if (!request()->user()) {
            throwError("Couldn't find creator id");
        }
        $data['creator_id'] = request()->user()->id;
        $group = parent::dotCreate($data);
        request()->user()->groups()->attach($group->id, [
            'joined_at' => now(),
            'inviter_id' => request()->user()->id
        ]);
        return $group;
    }

    public function kickUser(Group $group,User $user){
        if($group->creator_id != request()->user()->id){
            throwError("You don't have the permission to do this.");
        }
        if($group->creator_id == $user->id){
            throwError("Group owner can't be kicked out.");
        }
        $gUser = GroupUser::active()
            ->where('user_id',$user->id)
            ->where('group_id',$group->id)
            ->firstOrFail();
        $gUser->kicked_at = now();
        $gUser->save();
    }

    public function getGroupReport(Group $group)
    {
        $reports = [];
        $reports[] = $this->getReportLine("group created.", $group->created_at);
        //files management
        $groupFiles = GroupFile::where('group_id', $group->id)->with('file.creator')->get();
        foreach ($groupFiles as $gFile) {
            $file = $gFile->file;
            $user = $file->creator;
            $fileName = $file->path ?
                "file '$file->name.$file->extension'" :
                "folder $file->name";
            if($user->id == $group->creator_id){
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
                $message = $gFile->status == GroupFileStatusEnum::ACCEPTED->value ?
                    "Group owner approved adding the new $fileName uploaded by user $user->name." :
                    "Group owner denied adding the new $fileName uploaded by user $user->name.";
                $reports[] = $this->getReportLine(
                    $message,
                    $gFile->decided_at
                );
            }
        }
        //members management
        $groupUsers = GroupUser::where('group_id', $group->id)->with('inviter', 'user')->get();
        foreach ($groupUsers as $gUser) {
            if ($gUser->user_id == $group->creator_id)continue;
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
                    "User {$gUser->user->name} got kicked out from the group by group owner.",
                    $gUser->kicked_at
                );
            }
            if (
                $gUser->invitation_expires_at
                && !$gUser->joined_at
                && !$gUser->refused_at
                && now() > $gUser->invitation_expires_at
            ) {
                $reports[] = $this->getReportLine(
                    "User {$gUser->user->name} group invitation has expired.",
                    $gUser->invitation_expires_at
                );
            }
        }
        usort($reports, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        return $reports;
    }
    private function getReportLine($msg, $date, ...$metaData)
    {
        return [
            'message' => $msg,
            'date' => Carbon::parse($date)->toDateTimeString()
        ];
    }
}
