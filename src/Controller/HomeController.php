<?php

namespace App\Controller;

use App\Service\CompanyService;
use App\Service\ScrappingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

class HomeController extends AbstractController
{
    CONST WEB_URL = "https://rekvizitai.vz.lt/en";

    #[Route('/', name: 'home')]
    public function index(CompanyService $companyService)
    {
        $companies = $companyService->findCompanyAll();

        return $this->render('search/index.html.twig', [
            'companies' => $companies
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, CompanyService $companyService, ScrappingService $scrappingService)
    {
        // Assuming you have some criteria to search for a company
        $registrationCode = $request->get('registration_code');

        $criteria = [
            'registration_code' => $registrationCode, // Example registration code
        ];

        // Try to fetch company data from the database
        $company = $companyService->findCompanyByCriteria($criteria);

        if (!$company) {
            // If company data is not found in the database, perform scraping
            $url = self::WEB_URL . "/company-search/1/";

            $data = [
                'code' => $registrationCode,
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

    #[Route('/company/{id}/turnover', name: 'company_turnover', methods: ['GET'])]
    public function getCompanyTurnover(Request $request, SerializerInterface $serializer, CompanyService $companyService, int $id): JsonResponse
    {
        $company = $companyService->findCompanyById($id);

        if (!$company) {
            return new JsonResponse(['error' => 'Company not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $companyTurnover = $company->getSerializedTurnover();

        return new JsonResponse(['turnover' => $companyTurnover]);
    }

}
