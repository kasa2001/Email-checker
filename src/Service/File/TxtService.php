<?php

namespace App\Service\File;


class TxtService implements SaveInterface
{
    private $path;

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data = []): bool
    {
        $file = fopen($this->path, 'w');

        if (!$file) {
            return false;
        }

        foreach ($data as $item) {
            fwrite($file, $item . "\n");
        }

        if (fclose($file)) {
            //For linux
            chmod($this->path, 0755);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }
}