<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function creator(){
        return $this->belongsTo(User::class,'creator_id');
    }

    public function parent(){
        return $this->belongsTo(File::class,'parent_id');
    }

    public function directChildren(){
        return $this->hasMany(File::class,'parent_id');
    }
    public function groups(){
        return $this->belongsToMany(Group::class,'group_files');
    }

    public function checkIns(){
        return $this->hasMany(CheckIn::class);
    }
    public function activeCheckIns(){
        return $this->hasMany(CheckIn::class)->whereNull('checked_out_at');
    }
}
