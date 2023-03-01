<?php

namespace App\Repository;

use App\Entity\ReservationEvenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReservationEvenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationEvenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationEvenement[]    findAll()
 * @method ReservationEvenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationEvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationEvenement::class);
    }

    // /**
    //  * @return ReservationEvenement[] Returns an array of ReservationEvenement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReservationEvenement
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
