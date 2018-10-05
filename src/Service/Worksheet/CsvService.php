<?php

namespace App\Service\Worksheet;


use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Exception as DefaultException;
use App\Service\Exception\CsvException;

/**
 * Class CsvService
 * @package App\Service
 */
class CsvService implements WorksheetInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var Csv
     */
    private $reader;

    /**
     * @var Worksheet
     */
    private $worksheet;

    /**
     * @var integer
     */
    private $rows;

    /**
     * @var string
     */
    private $cols;

    /**
     * @var integer
     */
    private $currentRow;

    /**
     * @var string
     */
    private $currentCol;

    /**
     * CsvService constructor.
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function load(): bool
    {
        try {
            $this->reader = IOFactory::createReader('Csv');

            if (!$this->reader->canRead($this->path)) {
                throw new CsvException('Cannot be open file ' . $this->path);
            }

            $spreadsheet = $this->reader->load($this->path);
            $this->worksheet = $spreadsheet->getActiveSheet();

            $this->cols = $this->worksheet->getHighestDataColumn();
            $this->rows = $this->worksheet->getHighestDataRow();

            return true;
        } catch (Exception $exception) {
            throw new CsvException('Cannot be open file ' . $this->path);
        } catch (DefaultException $exception) {
            throw new CsvException('Cannot be get worksheet');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function current(): Cell
    {
        return $this->worksheet->getCell($this->currentCol . $this->currentRow);
    }

    /**
     * Next value for cell
     */
    public function next(): void
    {
        if ($this->currentCol == $this->cols) {
            $this->currentCol = 'A';
            $this->currentRow++;
            return;
        }

        // @TODO It's works to Z column only!!!
        $temp = ord($this->currentCol);
        ++$temp;
        $this->currentCol = chr($temp);
    }

    /**
     * Get coordinate for current cell
     * @return array
     */
    public function key(): array
    {
        return [
            'rows' => $this->currentRow,
            'cols' => $this->currentCol
        ];
    }

    /**
     * Check if you can iterate next value
     * @return bool
     */
    public function valid(): bool
    {
        return $this->currentCol != $this->cols || $this->currentRow <= $this->rows;
    }

    /**
     * Start value for foreach loop
     */
    public function rewind(): void
    {
        $this->currentRow = 1;
        $this->currentCol = 'A';
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $letter = ord($this->cols) - 64;
        return $letter * $this->rows;
    }
}