<?php

namespace App\Service;

use Exception;
use Symfony\Component\Filesystem\Filesystem;
use function MongoDB\BSON\toJSON;

class CSVReader
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * CSVReader constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $path
     * @return array
     * @throws Exception
     */
    public function readFile(string $path): array
    {
        $rows = [];

        if (!$this->filesystem->exists($path)) {
            throw new Exception(sprintf('Could find file "%s"', $path));
        }

        if (($fp = fopen($path, 'r')) !== false) {
            $header = [];

            while (($row = fgetcsv($fp, 1000, ',')) !== false) {
                if (!$header) {
                    $header = $row;
                    continue;
                }

                if (count($header) !== count($row)) {
                    throw new Exception(
                        sprintf(
                            "Mismatched number of columns \n Header: %s \n Row: %s",
                            implode(' | ', $header),
                            implode(' | ', $row)
                        )
                    );
                }

                $newRow = [];
                foreach ($row as $index => $value) {
                    $newRow[$header[$index]] = $value;
                }
                $rows[] = $newRow;
            }

            fclose($fp);
        }

        return $rows;
    }
}
