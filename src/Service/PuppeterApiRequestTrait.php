<?php

namespace App\Service;

trait PuppeterApiRequestTrait
{
    /**
     * Use to scrap data with handling human validation
     *
     * @param $url
     * @param $data
     * @param string $method
     * @return mixed
     * @throws \Exception
     */
    private function makeApiRequest($url, $data, $method = 'POST')
    {
        $params = [
            'url' => $url,
            'token' => $this->proxyToken,
        ];

        try {
            $response = $this->httpClient->request($method, $this->scrappingPuppeterUrl, [
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
}