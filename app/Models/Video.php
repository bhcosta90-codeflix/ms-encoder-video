<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    public static function booted(){
        static::created(function($obj){
            $obj->log('PENDING');
        });
    }

    public function logs()
    {
        return $this->hasMany(VideoLog::class);
    }

    public function log($status, $message=null)
    {
        $this->logs()->create([
            'status' => $status,
            'message' => $message,
        ]);
    }
}
