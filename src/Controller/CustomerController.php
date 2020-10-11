<?php

namespace App\Controller;

use App\Document\Customer;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends AbstractController
{
    protected const DEFAULT_PAGE = 1;
    protected const DEFAULT_PER_PAGE = 10;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * CustomerController constructor.
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        $page = $request->query->get('page');
        $perPage = $request->query->get('per_page');
        $page = is_numeric($page) && $page > 0 ? (int) $page : self::DEFAULT_PAGE;
        $perPage = is_numeric($perPage) && $perPage > 0 ? (int) $perPage : self::DEFAULT_PER_PAGE;

        $customers = $this->customerRepository->findPage($page, $perPage);
        $data = [];

        /** @var Customer $customer */
        foreach ($customers as $customer) {
            if (!filter_var($customer->getEmail(), FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $data[] = [
                'first_name' => $customer->getFirstName(),
                'last_name' => $customer->getLastName(),
                'email' => $customer->getEmail(),
                'address' => $customer->getAddress(),
                'city' => $customer->getCity(),
                'salutation' => $customer->getSalutation(),
                'social_security_num' => $customer->getSsn(),
                'account_balance' => $customer->getAccountIntBalance()
            ];
        }

        return $this->json([
            'data' => $data,
        ]);
    }
}
