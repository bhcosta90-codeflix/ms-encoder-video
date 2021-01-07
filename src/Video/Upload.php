<?php


namespace Video;


use Model\Video;

class Upload extends BaseVideo
{
    private \Rabbitmq $rabbit;

    /**
     * Upload constructor.
     * @param string $filename
     * @param string $field
     */
    public function __construct(string $filename, string $field)
    {
        parent::__construct($filename, $field);
        $this->rabbit = new \Rabbitmq();
    }

    public function exec()
    {
        Video::getVideo($this->filename, $this->field)->saveLog('Uploading');

        $iterator = new \RecursiveDirectoryIterator($this->getPathConverter());
        $recursiveIterator = new \RecursiveIteratorIterator($iterator);

        $files = [];
        foreach ( $recursiveIterator as $entry ) {
            if($entry->getFilename() != '..' && $entry->getFilename() != '.')
                array_push($files, [
                    'file' => $entry->getPathname(),
                    'key' => $this->getPathFileName() . '/' . substr(str_replace($this->getPathConverter(), '', $entry->getPathname()), 1),
                ]);
        }

        $this->rabbit->publish(json_encode([
            "filename" => $this->filename,
            "field" => $this->field,
            "files" => $files,
        ]), "ms-encoder.video.upload");
        Video::getVideo($this->filename, $this->field)->saveLog('Uploaded');
    }

    public function upload($files){
        $objStorage = \Google::getStorage();
        $bucket = $objStorage->bucket(\Google::BUCKET);
        Video::getVideo($this->filename, $this->field)->saveLog('Sending in Google Storage');
        foreach ($files as $file){
            $bucket->upload(
                fopen($file->file, 'r'),
                [
                    'predefinedAcl' => 'publicRead',
                    'name' => $file->key,
                ]
            );

            unlink($file->file);
        }

        $this->rabbit->publish(json_encode([
            "filename" => $this->filename,
            "field" => $this->field,
        ]), "ms-encoder.video.success");

        Video::getVideo($this->filename, $this->field)->saveLog('Sent in Google Storage');

        unlink($this->getMP4());
        unlink($this->getFrag());
    }

}