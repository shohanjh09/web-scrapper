<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ScrappingService
{
    use PuppeterApiRequestTrait;

    const MOBILE_KEYS = ['mobile_phone', 'phone'];

    const IMG_DIR = "public/images/";

    private $proxyToken;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    private $scrappingBaseUrl;

    private $scrappingPuppeterUrl;

    public function __construct($scrappingBaseUrl, $scrappingPuppeterUrl, $proxyToken, LoggerInterface $logger, HttpClientInterface $httpClient)
    {
        $this->scrappingBaseUrl = $scrappingBaseUrl;
        $this->scrappingPuppeterUrl = $scrappingPuppeterUrl;
        $this->proxyToken = $proxyToken;
        $this->logger = $logger;
        $this->httpClient = $httpClient;
    }

    /**
     * Search for a company using the provided URL and data.
     *
     * @param $url
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function searchCompany($url, $data){
        $response = $this->makeApiRequest($url, $data, 'POST');

        $companyUrls = $this->extractCompanyUrl($response);

        $scrapedData = [];

        foreach ($companyUrls as $companyUrl){
            $companyDetails = $this->getCompanyDetails($companyUrl);
            $companyTurnover = $this->getCompanyTurnover($companyUrl . "turnover");

            $scrapedData[] = [
                'company_details' => $companyDetails,
                'company_turnover' => $companyTurnover
            ];
        }

        return $scrapedData;
    }


    /**
     * Get company details from the provided URL.
     *
     * @param $url
     * @return array
     * @throws \Exception
     */
    public function getCompanyDetails($url)
    {
        $response = $this->makeApiRequest($url, [], 'GET');
        $crawler = new Crawler($response);

        $scrapedData = [];

        $this->extractCompanyName($crawler, $scrapedData);
        $this->extractDataFromTable($crawler, '.information', $scrapedData);
        $this->extractDataFromTable($crawler, '.details-block__2', $scrapedData, true);

        return $scrapedData;
    }

    /**
     * Get company turnover data from the provided URL.
     *
     * @param $url
     * @return array
     * @throws \Exception
     */
    public function getCompanyTurnover($url)
    {
        $response = $this->makeApiRequest($url, [], 'GET');

        $crawler = new Crawler($response);
        $financesBlock = $crawler->filter('.finances-block');

        $turnoverData = [];

        $tableRows = $financesBlock->filter('table.currency-table tr');

        // Loop through the rows and extract the key-value pairs
        $tableRows->each(function (Crawler $row) use (&$turnoverData) {
            $columns = $row->filter('th, td');
            $columnCount = $columns->count();

            if ($columnCount >= 2) {
                $key = $this->keyGenerator(trim($columns->eq(0)->text()));

                for ($i = 1; $i < $columnCount; $i++) {
                    $value = trim($columns->eq($i)->text());
                    $turnoverData[$key][] = $value;
                }
            }
        });

        return $turnoverData;
    }

    /**
     * Extract the company name using crawler
     *
     * @param Crawler $crawler
     * @param array $scrapedData
     */
    private function extractCompanyName(Crawler $crawler, array &$scrapedData)
    {
        $companyName = $crawler->filter('.top-title h2')->text();
        $scrapedData['company_name'] = trim(str_replace('Company', '', $companyName));
    }

    /**
     * Extract data for the company details
     *
     * @param Crawler $crawler
     * @param $tableSelector
     * @param array $scrapedData
     * @param false $isMobile
     */
    private function extractDataFromTable(Crawler $crawler, $tableSelector, array &$scrapedData, $isMobile = false)
    {
        $table = $crawler->filter($tableSelector)->filter('table');
        $rows = $table->filter('tr');

        $rows->each(function (Crawler $row) use (&$scrapedData, $isMobile) {
            $key = $this->keyGenerator($row->filter('.name')->text());

            if ($isMobile && in_array($key, self::MOBILE_KEYS)) {
                $scrapedData[self::MOBILE_KEYS[0]] = $this->extractMobileValue($row);
            } else {
                $scrapedData[$key] = $row->filter('.value')->text();
            }
        });
    }

    /**
     * Extract data for the mobile phone value
     *
     * @param Crawler $row
     * @return string
     * @throws \Exception
     */
    private function extractMobileValue(Crawler $row)
    {
        $imageUrl = $row->filter('.value img')->attr('src');
        $imageName = str_replace('%', '', basename($imageUrl));

        $this->saveImage($imageName, $this->scrappingBaseUrl . $imageUrl);

        return $imageName;
    }


    /**
     * Save image in provided directory
     *
     * @param $imageName
     * @param $imageUrl
     * @throws \Exception
     */
    private function saveImage($imageName, $imageUrl){
        $this->logger->info("IMAGE NAME: ".$imageName." and URL: ".$imageUrl);

        // Create the directory if it doesn't exist
        if (!is_dir(self::IMG_DIR)) {
            mkdir(self::IMG_DIR, 0755, true);
        }

        // Download the image content
        $imageContent = $this->makeApiRequest($imageUrl, [], 'GET');

        // Save the image in the given directory
        file_put_contents(self::IMG_DIR.$imageName, $imageContent);
    }

    /**
     * Extract company URL
     *
     * @param $html
     * @return array
     */
    private function extractCompanyUrl($html)
    {
        $crawler = new Crawler($html);
        $companyUrl = [];

        $crawler->filter('.company-title.d-block')->each(function (Crawler $node) use (&$companyUrl) {
            $companyUrl[] = $node->attr('href');
        });

        return $companyUrl;
    }

    /**
     * Key Generator
     *
     * @param $name
     * @return string
     */
    private function keyGenerator($name){
        return trim(strtolower(str_replace(['(', ')', ' ', '-'], ['', '', '_', '_'], $name)));
    }
}
