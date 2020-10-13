<?php

namespace App\Tests\Document;

use App\Document\Customer;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public function testGetSalutation()
    {
        $customer = new Customer();

        $customer->setGender('male');
        $this->assertEquals('mr', $customer->getSalutation(), 'Incorrect salutation');

        $customer->setGender('female');
        $this->assertEquals('ms', $customer->getSalutation(), 'Incorrect salutation');

        $customer->setGender('other');
        $this->assertNull($customer->getSalutation(), 'Incorrect salutation');
    }

    public function testGetAccountIntBalance()
    {
        $customer = new Customer();
        $this->assertEquals(0, $customer->getAccountIntBalance(), 'Incorrect account integer value');

        $customer->setAccountBalance(0);
        $this->assertEquals(0, $customer->getAccountIntBalance(), 'Incorrect account integer value');

        $customer->setAccountBalance(100.36);
        $this->assertEquals(10036, $customer->getAccountIntBalance(), 'Incorrect account integer value');

        $customer->setAccountBalance(-123.45);
        $this->assertEquals(-12345, $customer->getAccountIntBalance(), 'Incorrect account integer value');
    }
}
