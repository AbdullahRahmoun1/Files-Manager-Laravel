<?php

namespace App\Models;

use App\Enums\GroupFileStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;
use Wever\Laradot\App\Traits\HasFiles;

class File extends Model
{
    use HasFactory;
    use HasFiles;
    protected $fillable = [
        'extension',
        'name',
        'path',
        'creator_id',
        'parent_id',
    ];
    protected $with = [
        'activeCheckin.user'
    ];
    public function getFilesConfigurations(): array
    {
        return  [
            'path' => [
                'storage' => 'files',
                'file_name_generator' => fn() => Str::random(30),
                'operations' => [
                    // 'compress',
                ],
            ]
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function parent()
    {
        return $this->belongsTo(File::class, 'parent_id');
    }

    public function directChildren()
    {
        return $this->hasMany(File::class, 'parent_id')->whereNull('deleted_at');
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_files')
            ->where('group_files.status', GroupFileStatusEnum::ACCEPTED)
            ->whereNull('group_files.removed_at');
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }
    public function activeCheckIns()
    {
        return $this->hasMany(CheckIn::class)->whereNull('checked_out_at');
    }

    public function histories()
    {
        return $this->hasMany(FileHistory::class)->orderByDesc('version');
    }

    public function activeCheckin(){
        return $this->hasOne(CheckIn::class)->whereNull('checked_out_at');
    }
}
