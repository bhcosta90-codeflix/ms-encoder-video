<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoFragment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Video $obj;

    /**
     * VideoFragment constructor.
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
        Log::info('Start fragment: ' . $this->obj->uri);

        $mp4 = Storage::path("download/" . $this->obj->filepath ."/". $this->obj->name . ".mp4");
        $frag = Storage::path("download/" . $this->obj->filepath ."/". $this->obj->name . ".frag");
        exec("/opt/bento4/bin/mp4fragment {$mp4} {$frag}");
        Log::info('Finish converter: ' . $this->obj->uri);

        $this->obj->log('FRAGMENTED');

        (new VideoConverter($this->obj->uri))->handle();
    }
}
