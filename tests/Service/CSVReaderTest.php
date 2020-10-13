<?php

namespace App\Tests\Service;

use App\Service\CSVReader;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CSVReaderTest extends WebTestCase
{
    /**
     * @var CSVReader|object|null
     */
    private $csvReader;

    public function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->csvReader = $container->get(CSVReader::class);

        parent::setUp();
    }

    /**
     * @dataProvider provideTestCSVFiles
     * @param $path
     * @param $rowCount
     * @param $columnCount
     * @param $throwsException
     * @throws Exception
     */
    public function testReadFile($path, $rowCount, $columnCount, $throwsException)
    {
        if ($throwsException) {
            $this->expectExceptionMessageMatches('/^Mismatched number of columns.*/s');
        }

        $rows = $this->csvReader->readFile($path);
        $this->assertCount($rowCount, $rows, 'Incorrect number of rows red');

        foreach ($rows as $row) {
            $this->assertCount($columnCount, $row, 'Row contains wrong amount of columns');
        }
    }

    public function provideTestCSVFiles()
    {
        return [
            ['path' => 'tests/files/one.csv', 'rows' => 2, 'columns' => 8, 'throwsException' => false],
            ['path' => 'tests/files/two.csv', 'rows' => 1, 'columns' => 8, 'throwsException' => true],
            ['path' => 'tests/files/three.csv', 'rows' => 1, 'columns' => 8, 'throwsException' => false],
        ];
    }
}
