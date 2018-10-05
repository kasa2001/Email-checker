<?php

namespace App\Service\File;


interface SaveInterface
{

    /**
     * Method save data to file selected
     * @param array $data
     * @return bool
     */
    public function save(array $data = []): bool;

    /**
     * Set path
     * @param string $path
     */
    public function setPath(string $path): void;
}