<?php

namespace App\Tests\Command;

use App\Command\ImportCSVCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ImportCSVCommandTest extends WebTestCase
{
    public const TEST_ROWS = [
        ['name' => 'John', 'surname' => 'Doe', 'email' => 'johndoe@test.com', 'address' => 'Maple street 4',
            'city' => 'Denver', 'gender' => 'male', 'soc_security_num' => '123-45-6789', 'balance' => 25464.54],
        ['name' => 'Anna', 'surname' => 'Oak', 'address' => 'Oak street 5', 'city' => 'Seattle', 'gender' => 'female'],
        ['name' => 'Amy', 'balance' => 1000]
    ];

    /**
     * @var ImportCSVCommand|object|null
     */
    private $importCsvCommand;

    public function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->importCsvCommand = $container->get(ImportCSVCommand::class);

        parent::setUp();
    }

    public function testHydrateCustomers()
    {
        $customers = $this->importCsvCommand->hydrateCustomers(self::TEST_ROWS);
        $this->assertCount(count(self::TEST_ROWS), $customers, 'Not all customer object were created');

        foreach ($customers as $index => $customer) {
            foreach (self::TEST_ROWS[$index] as $key => $value) {
                $getter = 'get' . ucfirst(ImportCSVCommand::COLUMN_TO_PROPERTY_MAP[$key] ?? $key);

                $this->assertTrue(method_exists($customer, $getter), 'Customer model is missing getter method');
                $this->assertEquals($value, $customer->$getter(), 'Value from model doesn\'t match value in row');
            }
        }
    }
}
