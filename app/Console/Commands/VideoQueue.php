<?php

namespace App\Console\Commands;

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
        Amqp::consume('videos', function($message, $resolver){
            $data = json_decode($message->getBody());
            $this->call('video:converter', [
                '--file' => $data->video
            ]);
            $resolver->acknowledge($message);
        }, [
            'persistent' => true
        ]);

        return 0;
    }
}
