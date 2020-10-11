<?php

namespace App\Repository;

use App\Document\Customer;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;

class CustomerRepository extends ServiceDocumentRepository
{
    /**
     * CustomerRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function findPage(int $page, int $perPage): array
    {
        return $this->findBy([], null, $perPage, $perPage * --$page);
    }

    /**
     * @param Customer $customer
     * @throws MongoDBException
     */
    public function save(Customer $customer): void
    {
        $this->dm->persist($customer);
        $this->dm->flush();
    }

    /**
     * @param array $customers
     * @throws MongoDBException
     */
    public function saveMany(array $customers)
    {
        foreach ($customers as $customer) {
            if ($customer instanceof Customer) {
                $this->dm->persist($customer);
            }
        }

        $this->dm->flush();
    }
}
