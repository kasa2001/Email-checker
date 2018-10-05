<?php

namespace App\Service\Factory;

use App\Service\Worksheet\WorksheetInterface;

class WorksheetFactory
{
    private $namespace = "App\\Service\\Worksheet\\";

    /**
     * Get object to work with worksheet by path
     *
     * @param $path
     * @return WorksheetInterface
     */
    public function getWorksheetReader($path): WorksheetInterface
    {
        $matches = [];
        preg_match('/\.([a-z0-9]*)$/i', $path, $matches);

        $data = strtoupper($matches[1]);
        $data = $data[0] . strtolower(substr($data, 1));
        $class = $this->namespace . $data . 'Service';
        return new $class();
    }
}