<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private $proxyToken;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct($proxyToken, LoggerInterface $logger, HttpClientInterface $httpClient)
    {
        $this->proxyToken = $proxyToken;
        $this->logger = $logger;
        $this->httpClient = $httpClient;
    }

    public function makeApiRequest($url, $data, $method = 'POST')
    {
        $params = [
            'url' => $url,
            'token' => $this->proxyToken,
        ];

        try {
            $response = $this->httpClient->request($method, 'https://api.scrape.do', [
                'query' => $params,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $data,
            ]);

            return $response->getContent();
        } catch (\Exception $ex) {
            // Log the exception
            $this->logger->error($ex->getMessage());

            throw $ex;
        }
    }

    public function searchCompany($url, $data){
        $response = $this->makeApiRequest($url, $data, 'POST');

        return $this->getCompanyLink($response);
    }

    public function getCompanyLink($html)
    {
        $crawler = new Crawler($html);
        $link = "";

        $crawler->filter('.company-title.d-block')->each(function (Crawler $node) use (&$link) {
            $link = $node->attr('href');
        });

        return $link;
    }

    public function getCompanyDetails($url)
    {
        $response = $this->makeApiRequest($url, [], 'GET');
        return $response;
    }
}
