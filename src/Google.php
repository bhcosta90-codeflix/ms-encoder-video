<?php


use Google\Cloud\Storage\StorageClient;

class Google
{
    const BUCKET = 'codemicrovideos';
    /**
     * @return StorageClient
     */
    public static function getStorage(): StorageClient{
        return new StorageClient([
            'keyFile' => json_decode(file_get_contents(BASE_PATH . '/storage/google.cloud.json'), true),
        ]);
    }
}