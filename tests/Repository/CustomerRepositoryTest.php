<?php

namespace App\Tests\Repository;

use App\Document\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerRepositoryTest extends WebTestCase
{
    private const TEST_CUSTOMER = ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'johndoe@test.com',
        'address' => 'Maple street 4', 'city' => 'Denver', 'gender' => 'male', 'ssn' => '123-45-6789',
        'account_balance' => 25464.54];
    private const TEST_PER_PAGE = 3;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->customerRepository = $container->get(CustomerRepository::class);

        parent::setUp();
    }

    public function testFindPage()
    {
        $totalCount = count($this->customerRepository->findAll());

        for ($page = 1; $page < $totalCount / self::TEST_PER_PAGE + 1; $page++) {
            $customerPage = $this->customerRepository->findPage($page, self::TEST_PER_PAGE);

            if ($page === (int) ($totalCount / self::TEST_PER_PAGE) + 1) {
                $this->assertCount(
                    $totalCount % self::TEST_PER_PAGE,
                    $customerPage,
                    'Incorrect number of customers in last page'
                );
            } else {
                $this->assertCount(self::TEST_PER_PAGE, $customerPage, 'Incorrect number of customers in page');
            }

            foreach ($customerPage as $customer) {
                $this->assertInstanceOf(
                    Customer::class,
                    $customer,
                    'Page doesn\'t contain customer objects'
                );
            }
        }
    }

    /**
     * @throws MongoDBException
     */
    public function testSaveAndDelete()
    {
        $customer = new Customer();
        $customer->setFirstName(self::TEST_CUSTOMER['first_name']);
        $customer->setLastName(self::TEST_CUSTOMER['last_name']);
        $customer->setEmail(self::TEST_CUSTOMER['email']);
        $customer->setAddress(self::TEST_CUSTOMER['address']);
        $customer->setCity(self::TEST_CUSTOMER['city']);
        $customer->setGender(self::TEST_CUSTOMER['gender']);
        $customer->setSsn(self::TEST_CUSTOMER['ssn']);
        $customer->setAccountBalance(self::TEST_CUSTOMER['account_balance']);

        $this->customerRepository->save($customer);
        $this->customerRepository->getDocumentManager()->detach($customer);
        $savedCustomer = $this->customerRepository->findOneBy(['id' => $customer->getId()]);

        $this->assertInstanceOf(Customer::class, $savedCustomer);
        $this->assertEquals($customer, $savedCustomer, 'Not all field were saved');

        $this->customerRepository->delete($savedCustomer);
        $deletedCustomer = $this->customerRepository->findOneBy(['id' => $customer->getId()]);

        $this->assertNull($deletedCustomer, 'Created customer was not deleted');
    }

    /**
     * @throws MongoDBException
     */
    public function testSaveAndDeleteMany()
    {
        $newCustomers = [];

        for ($i = 0; $i < 5; $i++) {
            $customer = new Customer();
            $customer->setFirstName(self::TEST_CUSTOMER['first_name'] . str_repeat('I', $i));
            $customer->setLastName(self::TEST_CUSTOMER['last_name'] . str_repeat('I', $i));
            $customer->setEmail(self::TEST_CUSTOMER['email'] . str_repeat('I', $i));
            $customer->setAddress(self::TEST_CUSTOMER['address'] . str_repeat('I', $i));
            $customer->setCity(self::TEST_CUSTOMER['city'] . str_repeat('I', $i));
            $customer->setGender(self::TEST_CUSTOMER['gender'] . str_repeat('I', $i));
            $customer->setSsn(self::TEST_CUSTOMER['ssn'] . str_repeat('I', $i));
            $customer->setAccountBalance(self::TEST_CUSTOMER['account_balance'] * $i);

            $newCustomers[] = $customer;
        }

        $newCustomerIds = [];
        $this->customerRepository->saveMany($newCustomers);

        foreach ($newCustomers as $newCustomer) {
            $this->customerRepository->getDocumentManager()->detach($newCustomer);
            $newCustomerIds[] = $newCustomer->getId();
        }

        $savedCustomers = $this->customerRepository->getDocumentManager()
            ->createQueryBuilder(Customer::class)
            ->field('id')->in($newCustomerIds)
            ->getQuery()
            ->execute()
            ->toArray();

        foreach ($savedCustomers as $index => $savedCustomer) {
            $this->assertInstanceOf(Customer::class, $savedCustomer);
            $this->assertEquals($newCustomers[$index], $savedCustomer, 'Not all field were saved');
        }

        $this->customerRepository->deleteMany($savedCustomers);
        $deletedCustomers = $this->customerRepository->getDocumentManager()
            ->createQueryBuilder(Customer::class)
            ->field('id')->in($newCustomerIds)
            ->getQuery()
            ->execute()
            ->toArray();

        $this->assertEmpty($deletedCustomers, 'Crated customers were not deleted');
    }
}
