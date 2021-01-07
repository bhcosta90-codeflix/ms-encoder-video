<?php

namespace Video;

use Model\Video;

class Fragment extends BaseVideo
{
    public function exec(){
        Video::getVideo($this->filename, $this->field)->saveLog('Fragmenting');
        $mp4 = $this->getMP4();
        $frag = $this->getFrag();
        exec("/opt/bento4/bin/mp4fragment {$mp4} {$frag}");
        Video::getVideo($this->filename, $this->field)->saveLog('Fragmented');
    }
}