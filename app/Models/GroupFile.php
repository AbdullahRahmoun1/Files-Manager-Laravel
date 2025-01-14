<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupFile extends Model
{
    use HasFactory;
    protected $fillable = [
        '',
    ];

    public function file(){
        return $this->belongsTo(File::class);
    }
}
