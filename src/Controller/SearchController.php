<?php

namespace App\Controller;

use App\Service\ScrappingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    //TODO:: clean the code later
//    #[Route('/search', name: 'app_search')]
//    public function index(): Response
//    {
//        return $this->render('search/index.html.twig', [
//            'controller_name' => 'SearchController',
//        ]);
//    }

    //https://localhost/search
    #[Route('/search', name: 'search')]
    public function search(ScrappingService $scrappingService)
    {
        $url = "https://rekvizitai.vz.lt/en/company-search/1/";
        $data = array(
            'code' => '305454344',
            'order' => '1',
            'resetFilter' => '0'
        );

        //$turnoverUrl = "https://rekvizitai.vz.lt/en/company/leto_projektai/turnover/";
        //$url = "https://rekvizitai.vz.lt/en/company/leto_projektai/";
        $response = $scrappingService->searchCompany($url, $data);
        //$response = $apiService->searchCompany($url, $data);

        //return $response;

        return new JsonResponse($response);
    }
}
