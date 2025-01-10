<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wever\Laradot\App\Traits\Filterable;
class FileHistory extends Model
{
    use HasFactory;
    use Filterable;


    protected $fillable = [
        'path',
        'version',
        'check_in_id',
        'file_id',
        'comparison_path'
    ];

    public $filterable= [
        'file_id',
        'version',
        'check_in_id'
    ];
    // public static function boot(){
    //     self::addGlobalScope(fn($q)=>$q->orderByDesc('version'));
    // }


    public function file(){
        return $this->belongsTo(File::class);
    }
    public function checkIn(){
        return $this->belongsTo(CheckIn::class);
    }

}
