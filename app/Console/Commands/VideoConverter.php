<?php

namespace App\Console\Commands;

use App\Models\Video;
use Bschmitt\Amqp\Facades\Amqp;
use Exception;
use Illuminate\Console\Command;
use Storage;

class VideoConverter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:converter {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converter Video';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->option('file');

        $obj = Video::where('name', $file)->first();
        if (empty($obj)) {
            $obj = new Video;
            $obj->name = $file;
            $obj->save();
        } else {
            $obj->log('PENDING');
        }

        try {
            $this->info("Iniciando a conversão do vídeo: {$file}");
            $arrayFile = explode('/', $file);
            $uuid = $arrayFile[0];
            $name = end($arrayFile);

            $file = Storage::cloud()->get($file);
            Storage::put($name, $file);
            $obj->log('DOWNLOAD');

            try {
                $mp4 = Storage::path($name);
                $frag = Storage::path($name) . ".frag";
                $this->info("Iniciando a fragmentação do vídeo: {$mp4}");
                exec("/opt/bento4/bin/mp4fragment {$mp4} {$frag}");
                $obj->log('FRAGMENTED');

                try {
                    $commands = [];
                    $commands[] = "/opt/bento4/bin/mp4dash";
                    $commands[] = $frag;
                    $commands[] = "--use-segment-timeline";
                    $commands[] = "--o";
                    $commands[] = Storage::path(sha1($name));
                    $commands[] = "-f";

                    $this->info("Iniciando a conversão do vídeo: {$mp4}");
                    exec(implode(" ", $commands));

                    $obj->log('CONVERTED');

                    try {
                        $directoryFiles = Storage::allFiles(sha1($name));
                        foreach ($directoryFiles as $directoryFile) {
                            $content = Storage::get($directoryFile);
                            Storage::cloud()->put("{$uuid}/{$directoryFile}", $content);
                        }
                        $obj->log('UPLOADED');

                        Amqp::publish('routing-success', json_encode([
                            'file' => $this->option('file'),
                            'exported' => $uuid . "/" .sha1($name),
                        ]));

                    } catch (Exception $e) {
                        $obj->log('ERROR-UPLOADED', $e->getMessage());

                        Amqp::publish('routing-failed', json_encode([
                            'file' => $this->option('file'),
                            'status' => "ERROR-UPLOADED",
                        ]));

                        $this->error($e->getMessage());
                    }
                } catch (Exception $e) {
                    $obj->log('ERROR CONVERTED', $e->getMessage());

                    Amqp::publish('routing-failed', json_encode([
                        'file' => $this->option('file'),
                        'status' => "ERROR-CONVERTED",
                    ]));

                    $this->error($e->getMessage());
                }
            } catch (Exception $e) {
                $obj->log('ERROR FRAGMENTED', $e->getMessage());

                Amqp::publish('routing-failed', json_encode([
                    'file' => $this->option('file'),
                    'status' => "ERROR-FRAGMENTED",
                ]));

                $this->error($e->getMessage());
            }

            Storage::delete($name);

        } catch (Exception $e) {
            $obj->log('ERROR DOWNLOAD', $e->getMessage());

            Amqp::publish('routing-failed', json_encode([
                'file' => $this->option('file'),
                'status' => "ERROR-DOWNLOAD",
            ]));

            $this->error($e->getMessage());
        }

        return 0;
    }
}
