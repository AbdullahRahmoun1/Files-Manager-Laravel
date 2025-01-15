<?php

namespace App\Models;

use App\Enums\GroupFileStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupFile extends Model
{
    use HasFactory;
    protected $fillable = [
        '',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function scopeActive($q)
    {
        $q->where(
            'status', GroupFileStatusEnum::ACCEPTED,
        )->whereNull('removed_at');
    }
}
