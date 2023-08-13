<?php

namespace App\Controller;

use App\Service\ScrappingService;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    CONST WEB_URL = "https://rekvizitai.vz.lt/en";

    //TODO:: clean the code later
//    #[Route('/search', name: 'app_search')]
//    public function index(): Response
//    {
//        return $this->render('search/index.html.twig', [
//            'controller_name' => 'CompanyController',
//        ]);
//    }

    //https://localhost/search
    #[Route('/search', name: 'search')]
    public function search(ScrappingService $scrappingService)
    {
        $url = self::WEB_URL."/company-search/1/";

        $data = array(
            'code' => '305454344',
            'order' => '1',
            'resetFilter' => '0'
        );
        $response = $scrappingService->searchCompany($url, $data);

        return new JsonResponse($response);
    }
}
