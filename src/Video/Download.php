<?php

namespace Video;

use Model\Video;

class Download extends BaseVideo
{
    public function exec()
    {
        $path = explode('/', BASE_PATH . '/download/' . $this->filename);
        $filename = array_pop($path);
        $dir = implode('/', $path);

        if(file_exists($this->getMP4())){
            return;
        }

        Video::getVideo($this->filename, $this->field)->saveLog('Downloading');

        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }

        $objStorage = \Google::getStorage();
        $objStorage->registerStreamWrapper();
        $contents = file_get_contents('gs://'.\Google::BUCKET.'/' . $filename);
        $file = fopen((string) $this->getMP4(),'w');
        fwrite($file, $contents);
        fclose($file);

        Video::getVideo($this->filename, $this->field)->saveLog('Downloaded');
    }
}