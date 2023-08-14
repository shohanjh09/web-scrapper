<?php

namespace App\MessageHandler;

use App\Message\ScrapeMessage;
use App\Service\CompanyService;
use App\Service\ScrappingService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ScrapeMessageHandler
{
    const WEB_URL = "https://rekvizitai.vz.lt/en";

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

        $url = self::WEB_URL . "/company-search/1/";

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
            $this->companyService->removeCompanyByRegistrationCode($registrationCode);
        }

        return true;
    }
}
