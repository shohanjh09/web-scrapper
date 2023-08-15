<?php

namespace App\MessageHandler;

use App\Message\ScrapeMessage;
use App\Service\CompanyService;
use App\Service\ScrappingService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ScrapeMessageHandler
{
    private $scrappingService;
    private $companyService;

    public function __construct(ScrappingService $scrappingService, CompanyService $companyService)
    {
        $this->scrappingService = $scrappingService;
        $this->companyService = $companyService;
    }

    public function __invoke(ScrapeMessage $message)
    {
        $registrationCode = $message->getRegistrationCode();

        $url = $_ENV['SCRAPPING_BASE_URL'] . "/en/company-search/1/";

        $data = [
            'code'        => $registrationCode,
            'order'       => '1',
            'resetFilter' => '0',
        ];

        // Perform scraping for the registration code
        $scrapedData = $this->scrappingService->searchCompany($url, $data);

        if (!empty($scrapedData['company_details'])) {
            // Save the scraped data to the database
            $this->companyService->createOrUpdateCompanyAndTurnoverInformation($scrapedData);
        } else {
            // Remove the entry from database which was used to tracking the scrapping
            $this->companyService->removeCompanyByRegistrationCode($registrationCode);
        }

        return true;
    }
}
