<?php

namespace App\Repository;

use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Figure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Figure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Figure[]    findAll()
 * @method Figure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FigureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Figure::class);
    }

    /**
     * @returns all figure per page
     * @retrun void
     */
    public function pagination($page, $limit)
    {
        return $this->createQueryBuilder('f')
            ->setFirstResult(($page * $limit) - $limit)
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @returns all figure per page
     * @retrun void
     */
    public function getTotalFigure()
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    /*
    public function findOneBySomeField($value): ?Figure
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
