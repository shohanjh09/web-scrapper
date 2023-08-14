<?php

namespace App\Controller;

use App\Service\CompanyService;
use App\Service\ScrappingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    const WEB_URL = "https://rekvizitai.vz.lt/en";

    #[Route('/search', name: 'company_search')]
    public function search(Request $request, CompanyService $companyService, ScrappingService $scrappingService): Response
    {
        $registrationCode = $request->get('registration_code');

        if (!empty($registrationCode)) {
            $criteria = [
                'registration_code' => $registrationCode
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

                // Save the scraped data to the database
                $company = $companyService->createCompanyWithTurnover($scrapedData);
                $company = [$company];
            }
        } else {
            $company = [];
        }

        return $this->render('company/search.html.twig', [
            'pagination' => $company
        ]);
    }
}
