<?php


namespace Video;


abstract class BaseVideo
{
    protected string $filename;
    protected string $field;

    /**
     * Download constructor.
     * @param string $filename
     * @param string $field
     */
    public function __construct(string $filename, string $field)
    {
        $this->filename = $filename;
        $this->field = $field;
        $this->extractFile();
    }

    /**
     * @return string
     */
    protected function getPathConverter(): string
    {
        $array = explode('.', BASE_PATH . '/converter/' . $this->filename);
        array_pop($array);
        $dir = implode('.', $array);

        return $dir;
    }

    /**
     * @return string[]
     */
    private function extractFile(): array
    {
        $frag = BASE_PATH . '/download/' . $this->filename . ".frag";

        return [
            'mp4' => BASE_PATH . '/download/' . $this->filename,
            'frag' => $frag,
            'converter' => $this->getPathConverter()
        ];
    }

    protected function getFile(): string
    {
        return $this->extractFile()['file'];
    }

    protected function getMP4(): string
    {
        return $this->extractFile()['mp4'];
    }

    protected function getFrag(): string
    {
        return $this->extractFile()['mp4'] . ".frag";
    }

    /**
     * @return string
     */
    protected function getPathFileName()
    {
        $array = explode('.', $this->filename);

        array_pop($array);
        return implode('.', $array);
    }

    public abstract function exec();
}