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

class VideoUpload implements ShouldQueue
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
        Log::info('Start upload: ' . $this->obj->uri);

        $directoryFiles = Storage::allFiles("download/" . $this->obj->filepath ."/". $this->obj->name);
        foreach ($directoryFiles as $directoryFile) {
            $content = Storage::get($directoryFile);
            $nameDirectoryFile = substr($directoryFile, 9);
            $arrayDirectory = explode('/', $nameDirectoryFile);
            array_pop($arrayDirectory);
            $nameDirectoryFile = str_replace($this->obj->name . "/" . $this->obj->name, $this->obj->name, $nameDirectoryFile);
            Storage::cloud()->put("converter/" . $nameDirectoryFile, $content);
        }

        Storage::deleteDirectory("download/" . $this->obj->filepath);
        Log::info('Finish upload: ' . $this->obj->uri);
        Log::info("download/" . $this->obj->filepath);

        \Amqp::publish('model.video.exported', json_encode([
            'file' => $this->obj->uri,
            'exported' => "converter/" . $this->obj->filepath,
        ]));

        $this->obj->log('UPLOADED');
    }
}
