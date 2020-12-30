<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Video;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Video $obj;

    /**
     * VideoDownload constructor.
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $this->obj = Video::getVideo($fileName);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Start download: ' . $this->obj->uri);
        $file = Storage::cloud()->get($this->obj->uri);
        Storage::put("download/" . $this->obj->filepath . "/" . $this->obj->name . "." . $this->obj->extension, $file);
        Log::info('Finish download: ' . $this->obj->uri);

        $this->obj->log('DOWNLOAD: ' . $this->obj->uri);

        (new VideoFragment($this->obj->uri))->handle();
    }
}
