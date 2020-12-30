<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    public static function booted(){
        static::creating(function($obj){
            $arrayPointObject = explode('.', $obj->name);
            $arrayNameObject = explode('/', $obj->name);
            $extension = array_pop($arrayPointObject);
            $nameFile = array_pop($arrayNameObject);
            list($absoluteNameFile) = explode('.', $nameFile);

            $obj->filepath = implode('.', $arrayPointObject);
            $obj->extension = $extension;
            $obj->name = $absoluteNameFile;
        });
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

    public static function getVideo(string $name){
        $obj = Video::where('name', $name)->first();
        if (empty($obj)) {
            $obj = new Video;
            $obj->name = $name;
            $obj->save();
        }

        return $obj;
    }

    public function getUriAttribute()
    {
        return "{$this->filepath}.{$this->extension}";
    }
}
