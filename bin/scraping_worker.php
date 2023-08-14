<?php

require __DIR__.'/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$container = require __DIR__.'/../config/bootstrap.php';

const WEB_URL = "https://rekvizitai.vz.lt/en";

$connection = new AMQPStreamConnection($_ENV['RABBITMQ_HOST'], $_ENV['RABBITMQ_PORT'], $_ENV['RABBITMQ_USER'], $_ENV['RABBITMQ_PASSWORD']);
$channel = $connection->channel();

$channel->queue_declare('scraping_queue', false, true, false, false);

$callback = function (AMQPMessage $message) use ($container) {
    $data = json_decode($message->getBody(), true);

    // Fetch necessary services from the Symfony container
    $entityManager = $container->get('doctrine')->getManager();
    $companyService = $container->get('App\Service\CompanyService');
    $scrappingService = $container->get('App\Service\ScrappingService');

    // Perform the scraping operation using $data['registration_code']
    $url = WEB_URL . "/company-search/1/";
    $scrappingData = [
        'code' => $data['registration_code'],
        'order' => '1',
        'resetFilter' => '0',
    ];
    $scrapedData = $scrappingService->searchCompany($url, $scrappingData);

    // Save the scraped data to the database
    $company = $companyService->createCompanyWithTurnover($scrapedData);

    echo " [x] Scraped and saved: {$data['registration_code']}\n";
};

$channel->basic_consume('scraping_queue', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
