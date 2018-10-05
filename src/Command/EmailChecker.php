<?php

namespace App\Command;


use App\Service\Factory\FileFactory;
use App\Service\Factory\WorksheetFactory;
use App\Service\Exception\CsvException;
use App\Service\Validator\MailValidatorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Tests\Compiler\F;

class EmailChecker extends Command
{
    /**
     * Pattern for checking with DNS
     */
    const MAIL_DNS = '/^[a-z\d][a-z\d\._\-&!?#=]*@/i';

    /**
     * Pattern for checking without DNS
     */
    const MAIL_NO_DNS = '/^[a-z\d][a-z\d\._\-&!?#=]*@[a-z\d][a-z\d\-\.]*\.[a-z]*$/i';

    /**
     * @var string
     */
    private $dns;

    /**
     * @var string
     */
    private $path;

    /**
     * @var WorksheetFactory
     */
    private $worksheetFactory;

    /**
     * @var MailValidatorService
     */
    private $validatorService;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var array
     */
    private $valid;

    /**
     * @var array
     */
    private $invalid;

    public function __construct(
        WorksheetFactory $worksheetFactory,
        MailValidatorService $validatorService,
        FileFactory $fileFactory
    )
    {
        parent::__construct();
        $this->worksheetFactory = $worksheetFactory;
        $this->fileFactory = $fileFactory;
        $this->validatorService = $validatorService;
    }

    public function configure()
    {
        $this->setName('application:email');
        $this->setDescription("Email checker");
        $this->addOption(
            "path",
            "-p",
            InputOption::VALUE_OPTIONAL,
            "Path to file (optional)",
            __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "csv" . DIRECTORY_SEPARATOR . "data.csv"
        );

        $this->addOption(
            "dns",
            '-d',
            InputOption::VALUE_NONE,
            "Check DNS address (Require network connection!!! Longer but better checking)  "
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getOptions($input);
        $worksheet = $this->worksheetFactory->getWorksheetReader($this->path);
        $worksheet->setPath($this->path);
        try {
            $worksheet->load();
        } catch (CsvException $exception) {
            die(1);
        }

        echo "Start checking " . count($worksheet) . " positions\n";

        foreach ($worksheet as $key => $cell) {
            $address = $cell->getValue();
            echo ".";
            if (!$this->validatorService->checkSize($address)
                || !$this->validatorService->pregAddress($address, ($this->dns ? self::MAIL_DNS : self::MAIL_NO_DNS))
            ) {
                $this->invalid[] = $address;
                continue;
            }

            if ($this->dns && !$this->validatorService->checkDNS($address)) {
                $this->invalid[] = $address;
                continue;
            }

            $this->valid[] = $address;
        }
        $this->saveData();
        echo "\n\n\nDone";
    }

    protected function getOptions(InputInterface $input)
    {
        $this->dns = $input->getOption('dns');
        $this->path = $input->getOption('path');
        $this->valid = [];
        $this->invalid = [];
    }

    public function saveData()
    {
        $invalid = $this->fileFactory->getSaveContainer("Txt");
        $valid = $this->fileFactory->getSaveContainer("Txt");
        $info = $this->fileFactory->getSaveContainer("Txt");

        $invalid->setPath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "txt" . DIRECTORY_SEPARATOR . "invalid.txt");
        $valid->setPath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "txt" . DIRECTORY_SEPARATOR . "valid.txt");
        $info->setPath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "txt" . DIRECTORY_SEPARATOR . "info.txt");

        $invalid->save($this->invalid);
        $valid->save($this->valid);

        $string = [];
        $string[] = "Valid: " . count($this->valid);
        $string[] = "Invalid: " . count($this->invalid);
        $info->save(
            $string
        );
    }
}