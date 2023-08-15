<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use App\Service\CompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\CompanyFilterType;
use Symfony\Component\Serializer\SerializerInterface;

class CompanyController extends AbstractController
{
    #[Route('/', name: 'company_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CompanyRepository $companyRepository, PaginatorInterface $paginator): Response
    {
        $itemsPerPage = $_ENV['ITEMS_PER_PAGE'] ?? 10;
        $page = $request->query->getInt('page', 1);

        $filterForm = $this->createForm(CompanyFilterType::class);
        $filterForm->handleRequest($request);

        $queryBuilder = $companyRepository->createQueryBuilder('c')->andWhere("c.status = 'completed'")->orderBy('c.id', 'DESC');

        // Apply filters if the form is submitted
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $page = 1;
            $data = $filterForm->getData();
            if ($data['search']) {
                $queryBuilder
                    ->andWhere('c.company_name LIKE :search OR c.registration_code LIKE :search OR c.vat LIKE :search')
                    ->setParameter('search', '%' . $data['search'] . '%');
            }
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $page,
            $itemsPerPage
        );

        return $this->render('company/index.html.twig', [
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    #[Route('/new', name: 'company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/new.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/edit.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'company_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('company_index', [], Response::HTTP_SEE_OTHER);
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
