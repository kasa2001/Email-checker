<?php

namespace App\Service\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Exception as DefaultException;

/**
 * Interface WorksheetInterface
 * @package App\Service\Worksheet
 */
interface WorksheetInterface extends \Iterator, \Countable
{
    /**
     * Method load data from csv file to memory
     * @throws \App\Service\Exception\CsvException
     */
    public function load(): bool;

    /**
     * Set path
     * @param $path string
     */
    public function setPath($path): void;

    /**
     * @return Cell
     * @throws DefaultException
     */
    public function current(): Cell;
}