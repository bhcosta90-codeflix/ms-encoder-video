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

class VideoConverter implements ShouldQueue
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
        Log::info('Start converter: ' . $this->obj->uri);

        $commands = [];
        $commands[] = "/opt/bento4/bin/mp4dash";
        $commands[] = Storage::path("download/" . $this->obj->filepath ."/". $this->obj->name . ".frag");
        $commands[] = "--use-segment-timeline";
        $commands[] = "--o";
        $commands[] = Storage::path("download/" . $this->obj->filepath ."/". $this->obj->name);
        $commands[] = "-f";
        exec(implode(" ", $commands));

        $this->obj->log('CONVERTED');
        Log::info('Finish converter: ' . $this->obj->uri);

        (new VideoUpload($this->obj->uri))->handle();
    }
}
