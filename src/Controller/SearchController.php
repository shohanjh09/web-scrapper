<?php

namespace App\Controller;

use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
//    #[Route('/search', name: 'app_search')]
//    public function index(): Response
//    {
//        return $this->render('search/index.html.twig', [
//            'controller_name' => 'SearchController',
//        ]);
//    }

    #[Route('/search', name: 'search')]
    public function search(ApiService $apiService)
    {
        $url = "https://rekvizitai.vz.lt/en/company-search/1/";
        $data = array(
            'code' => '305454344',
            'order' => '1',
            'resetFilter' => '0'
        );

        $response = $apiService->searchCompany($url, $data);

        return new JsonResponse($response);
    }
}
