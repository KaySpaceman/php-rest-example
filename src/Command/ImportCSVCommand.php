<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCSVCommand extends Command
{
    protected static $defaultName = 'customer:import-csv';

    protected function configure()
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $input->getArgument('files');
        $success = false;

        if (count($files) === 0) {
            $output->writeln('No filenames given');

            return Command::FAILURE;
        }

        foreach ($files as $file) {
            $output->writeln($file);
        }

        return $success ? Command::SUCCESS : Command::FAILURE;
    }
}