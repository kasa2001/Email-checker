<?php

namespace App\Service\Factory;

use App\Service\File\SaveInterface;

class FileFactory
{
    private $namespace = "App\\Service\\File\\";

    /**
     * Get object by file extension
     *
     * @param string $type
     * @return SaveInterface
     */
    public function getSaveContainer(string $type): SaveInterface
    {
        $class = $this->namespace . $type . "Service";

        return new $class;
    }
}