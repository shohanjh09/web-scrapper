<?php

namespace App\Repository;

use App\Entity\CompanyTurnover;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompanyTurnover>
 *
 * @method CompanyTurnover|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyTurnover|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyTurnover[]    findAll()
 * @method CompanyTurnover[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyTurnoverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyTurnover::class);
    }
}
