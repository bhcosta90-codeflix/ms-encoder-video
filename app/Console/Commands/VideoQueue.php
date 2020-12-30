<?php

namespace App\Console\Commands;

use App\Jobs\VideoDownload;
use App\Models\Video;
use Bschmitt\Amqp\Facades\Amqp;
use Illuminate\Console\Command;

class VideoQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue Video';

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
        \Amqp::consume('ms-encoder/video/converter', function($message, $resolver){
            $data = json_decode($message->getBody());
            Video::getVideo($data->video);
            dispatch(new VideoDownload($data->video));
            $resolver->acknowledge($message);
        }, [
            'persistent' => true,
            'exchange' => 'amq.topic',
            'exchange_type' => 'topic',
            'routing' => 'models.video.converter'
        ]);

        return 0;
    }
}
