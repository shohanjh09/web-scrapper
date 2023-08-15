<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Company::class);

        $this->paginator = $paginator;
    }

    /**
     * Count the number of in_progress and completed company for a list of registration codes
     *
     * @param array $registrationCodes
     * @return array
     */
    public function countStatusByRegistrationCodes(array $registrationCodes): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('c.status, COUNT(c.id) as count')
            ->andWhere('c.registration_code IN (:registrationCodes)')
            ->setParameter('registrationCodes', $registrationCodes)
            ->groupBy('c.status');

        $results = $queryBuilder->getQuery()->getResult();

        $statusCounts = [];
        foreach ($results as $result) {
            $statusCounts[$result['status']] = $result['count'];
        }

        return $statusCounts;
    }

    /**
     * Get a list of completed company for a list of registration codes
     *
     * @param array $registrationCodes
     * @param $page
     * @param $limit
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getCompletedCompanyByRegistrationCode(array $registrationCodes, $page, $limit)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->andWhere("c.registration_code IN (:registrationCodes) AND c.status = 'completed'")
            ->setParameter('registrationCodes', $registrationCodes)
            ->orderBy('c.id', 'DESC');

        return $this->paginator->paginate($queryBuilder->getQuery(), $page, $limit);
    }
}
