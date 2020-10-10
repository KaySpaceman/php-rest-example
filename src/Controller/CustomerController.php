<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends AbstractController
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_PER_PAGE = 10;

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

        return $this->json([
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }
}
