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
        // Get the registration code from the message
        $registrationCode = $message->getRegistrationCode();

        $url = $_ENV['SCRAPPING_BASE_URL'] . "/en/company-search/1/";

        $data = [
            'code'        => $registrationCode,
            'order'       => '1',
            'resetFilter' => '0',
        ];

        $scrapedDatas = $this->scrappingService->searchCompany($url, $data);

        if (!empty($scrapedDatas)) {
            $totalScraped = count($scrapedDatas);

            // Save the scraped data to the database
            foreach ($scrapedDatas as $key => $scrapedData) {
                $isLastScraped = $key === $totalScraped - 1;
                $this->companyService->createOrUpdateCompanyAndTurnoverInformation($scrapedData, $isLastScraped);
            }
        } else {
            // If no data scraped, remove the tracking entry from the database
            $this->companyService->removeCompanyByRegistrationCode($registrationCode);
        }

        return true;
    }
}
