<?php


namespace Video;


use Model\Video;

class Converter extends BaseVideo
{
    public function exec()
    {
        Video::getVideo($this->filename, $this->field)->saveLog('Converting');
        $frag = $this->getFrag();
        $dir = $this->getPathConverter();

        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }

        $commands = [];
        $commands[] = "/opt/bento4/bin/mp4dash";
        $commands[] = $frag;
        $commands[] = "--use-segment-timeline";
        $commands[] = "--o";
        $commands[] = $dir;
        $commands[] = "-f";
        exec(implode(" ", $commands));

        Video::getVideo($this->filename, $this->field)->saveLog('Converted');
    }

}