<?php

namespace App\Controller;

use App\Service\CompanyService;
use App\Service\ScrappingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
//    #[Route('/search', name: 'search')]
//    public function search(ScrappingService $scrappingService)
//    {
//        $url = self::WEB_URL."/company-search/1/";
//
//        $data = array(
//            'code' => '305454344',
//            'order' => '1',
//            'resetFilter' => '0'
//        );
//        $response = $scrappingService->searchCompany($url, $data);
//
//        return new JsonResponse($response);
//    }

    #[Route('/search', name: 'search')]
    public function search(CompanyService $companyService, ScrappingService $scrappingService)
    {
        // Assuming you have some criteria to search for a company
        $criteria = [
            'registration_code' => '305454344', // Example registration code
        ];

        // Try to fetch company data from the database
        $company = $companyService->findCompanyByCriteria($criteria);

        if (!$company) {
            // If company data is not found in the database, perform scraping
            $url = self::WEB_URL . "/company-search/1/";

            $data = [
                'code' => '305454344',
                'order' => '1',
                'resetFilter' => '0',
            ];

            $scrapedData = $scrappingService->searchCompany($url, $data);

            // Save the scraped data to the database using the CompanyService
            $company = $companyService->createCompanyWithTurnover($scrapedData);

            // Return the response with the scraped and saved data
            return new JsonResponse(['message' => 'Company data scraped and saved successfully.', 'data' => $company]);
        }

        // If company data is found in the database, return the response
        return new JsonResponse(['message' => 'Company data found in the database.', 'data' => $company]);
    }
}
