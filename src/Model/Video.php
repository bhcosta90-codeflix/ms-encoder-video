<?php


namespace Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    protected $fillable = ['file', 'field'];

    public function log(): HasMany
    {
        return $this->hasMany(VideoLog::class);
    }

    /**
     * @param string $file
     * @param string $field
     * @return mixed
     */
    public static function getVideo(string $file, string $field){
        if($obj = self::where('file', $file)->where('field', $field)->first()){
            return $obj;
        }

        return self::create(['file' => $file, 'field' => $field]);
    }

    /**
     * @param string $status
     * @return Model
     */
    public function saveLog(string $status): Model
    {
        return $this->log()->create(['status' => $status]);
    }
}