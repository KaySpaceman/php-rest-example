<?php

namespace App\Controller;

use App\Document\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends AbstractController
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_PER_PAGE = 10;

    /**
     * @param Request $request
     * @param DocumentManager $dm
     * @return JsonResponse
     */
    public function list(Request $request, DocumentManager $dm)
    {
        $page = $request->query->get('page');
        $perPage = $request->query->get('per_page');
        $page = is_numeric($page) && $page > 0 ? (int) $page : self::DEFAULT_PAGE;
        $perPage = is_numeric($perPage) && $perPage > 0 ? (int) $perPage : self::DEFAULT_PER_PAGE;

        $customer = new Customer();
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setAccountBalance(123);

        $dm->persist($customer);
        $dm->flush();

        return $this->json([
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }
}
