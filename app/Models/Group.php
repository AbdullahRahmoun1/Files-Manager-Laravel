<?php

namespace App\Models;

use App\Enums\GroupFileStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'color',
        'lang',
        'creator_id',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function files()
    {
        return $this->belongsToMany(File::class, 'group_files')
            ->withPivot(['status', 'removed_at']) // Ensure pivot fields are loaded
            ->wherePivot('status', GroupFileStatusEnum::ACCEPTED) // Filter by pivot field
            ->wherePivot('removed_at', null) // Filter by pivot field
            ->whereNull('files.parent_id'); // Filter by `files` table column
    }
    public function allFiles()
    {
        return $this->belongsToMany(File::class, 'group_files')
            ->withPivot(['status', 'removed_at']) // Ensure pivot fields are loaded
            ->wherePivot('status', GroupFileStatusEnum::ACCEPTED) // Filter by pivot field
            ->wherePivot('removed_at', null); // Filter by pivot field
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_users')
            ->whereNull('kicked_at')
            ->whereNull('left_at')
            ->whereNotNull('joined_at');
    }
    public function pendingFiles()
    {
        return $this->belongsToMany(File::class, 'group_files')
            ->where('group_files.status', GroupFileStatusEnum::PENDING);
    }
}
