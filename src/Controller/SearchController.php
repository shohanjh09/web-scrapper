<?php

namespace App\Controller;

use App\Entity\Company;
use App\Message\ScrapeMessage;
use App\Service\CompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'company_search')]
    public function search(Request $request, CompanyService $companyService, MessageBusInterface $messageBus, EntityManagerInterface $entityManager): Response
    {
        $registrationCodes = $request->get('registration_codes');
        $message = null;
        $pagination = [];

        if (!empty($registrationCodes)) {
            $registrationCodesArray = explode(',', $registrationCodes);
            $registrationCodesArray = array_map('trim', $registrationCodesArray);

            $status = $this->checkStatusInDatabase($companyService, $registrationCodes);

            if ($status === 'completed') {
                $page = $request->query->getInt('page', 1);
                $itemsPerPage = $_ENV['ITEMS_PER_PAGE'] ?? 10;
                $pagination = $companyService->getActiveCompanyByRegistrationCode($registrationCodesArray, $page, $itemsPerPage);
            } else {
                foreach ($registrationCodesArray as $registrationCode) {
                    $company = $companyService->findCompanyByCriteria(['registration_code' => $registrationCode]);

                    if (!$company) {
                        $company = new Company();
                        $company->setCompanyName("Company - ".$registrationCode);
                        $company->setRegistrationCode($registrationCode);
                        $company->setStatus('in_progress');
                        $entityManager->persist($company);

                        $scrapeMessage = new ScrapeMessage($registrationCode);
                        $messageBus->dispatch($scrapeMessage);

                        $message = 'Scraping task is being processed. Results will display automatically after processing.';
                    }
                }

                if (!empty($message)) {
                    $entityManager->flush();
                }
            }
        }

        return $this->render('company/search.html.twig', [
            'pagination' => $pagination,
            'message' => $message,
            'registrationCodes' => $registrationCodes,
        ]);
    }

    #[Route('/check_status', name: 'check_status')]
    public function checkStatus(Request $request, CompanyService $companyService): JsonResponse
    {
        $registrationCodes = $request->get('registration_codes');
        $status = $this->checkStatusInDatabase($companyService, $registrationCodes);
        return new JsonResponse($status);
    }

    /**
     * Check the number of in_progress and completed company in the database for list of registration codes
     * @param CompanyService $companyService
     * @param string $registrationCodes
     * @return string
     */
    private function checkStatusInDatabase(CompanyService $companyService, string $registrationCodes): string
    {
        $registrationCodesArray = explode(',', $registrationCodes);
        $registrationCodesArray = array_map('trim', $registrationCodesArray);

        $statusCounts = $companyService->countStatusByRegistrationCodes($registrationCodesArray);
        $inProgressCount = $statusCounts['in_progress'] ?? 0;
        $completedCount = $statusCounts['completed'] ?? 0;

        return ($completedCount > 0 && $inProgressCount == 0) ? 'completed' : 'in_progress';
    }
}
