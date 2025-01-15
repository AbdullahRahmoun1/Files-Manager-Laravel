<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CheckIn extends Model
{
    use HasFactory;
    protected $fillable = [
        'checked_in_at',
        'checked_out_at',
        'file_id',
        'user_id'
    ];

    public function fileVersion(){
        return $this->hasOne(FileHistory::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function file(){
        return $this->belongsTo(File::class);
    }
}
