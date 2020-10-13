<?php

namespace App\Command;

use App\Document\Customer;
use App\Repository\CustomerRepository;
use App\Service\CSVReader;
use Doctrine\ODM\MongoDB\MongoDBException;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCSVCommand extends Command
{
    /**
     * Maps CSV file columns to Customer object properties.
     * column => property.
     * Not the nicest solution, but this will do for now.
     */
    public const COLUMN_TO_PROPERTY_MAP = [
        'name' => 'firstName',
        'surname' => 'lastName',
        'email' => 'email',
        'address' => 'address',
        'city' => 'city',
        'gender' => 'gender',
        'soc_security_num' => 'ssn',
        'balance' => 'accountBalance',
    ];

    protected static $defaultName = 'customer:import-csv';

    /**
     * @var CSVReader
     */
    private $reader;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * ImportCSVCommand constructor.
     * @param CSVReader $reader
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CSVReader $reader, CustomerRepository $customerRepository)
    {
        $this->reader = $reader;
        $this->customerRepository = $customerRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Parses customer data CSV files and stores the dates in the database')
            ->setHelp(
                sprintf(
                    'Pass all filenames that should be imported as command arguments. "%s one.csv two.csv"',
                    self::$defaultName
                )
            )->addArgument(
                'files',
                InputArgument::IS_ARRAY,
                'Separate multiple filenames with a space'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = $input->getArgument('files');
        $readCount = 0;
        $newCustomers = [];

        if (count($files) === 0) {
            $output->writeln('No filenames given');

            return Command::FAILURE;
        }

        foreach ($files as $path) {
            try {
                $rows = $this->reader->readFile($path);
                $readCount += count($rows);

                $newCustomers = array_merge($newCustomers, $this->hydrateCustomers($rows));
            } catch (Exception $e) {
                $output->writeln(sprintf('Failed to parse file "%s" with error: %s', $path, $e->getMessage()));
            }
        }

        $createCount = count($newCustomers);

        try {
            $this->customerRepository->saveMany($newCustomers);
        } catch (MongoDBException $e) {
            $output->writeln(sprintf('Failed to save %s customers with error: %s', $createCount, $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('Red %d lines and created %d customers', $readCount, $createCount));

        return $createCount ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * @param array $rows
     * @return array
     */
    public function hydrateCustomers(array $rows): array
    {
        $newCustomers = [];

        foreach ($rows as $row) {
            $customer = new Customer();

            foreach ($row as $key => $value) {
                $setter = 'set' . ucfirst(self::COLUMN_TO_PROPERTY_MAP[$key] ?? $key);

                if ($value !== '' && method_exists($customer, $setter)) {
                    $customer->$setter($value);
                }
            }

            $newCustomers[] = $customer;
        }

        return $newCustomers;
    }
}