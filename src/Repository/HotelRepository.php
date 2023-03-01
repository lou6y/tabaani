<?php

namespace App\Repository;

use App\Entity\Hotel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hotel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hotel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hotel[]    findAll()
 * @method Hotel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hotel::class);
    }

    // /**
    //  * @return Hotel[] Returns an array of Hotel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    public function findEntitiesByString($str)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT p 
        FROM App:Hotel p
        WHERE p.nom LIKE :str'

            )->setParameter('str', '%'.$str.'%')->getResult();
    }
    /*
    public function findOneBySomeField($value): ?Hotel
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function findHotelService(){
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT c.idHotel,c.nom,c.emplacement,c.contact,c.tarif,c.imageName,c.tarif,u.categ,u.type
                            FROM App:Service u JOIN App:Hotel c WITH u.idHotel = c.idHotel
                     
                            ");
        if(count($query->getArrayResult()) > 0) return $query->getResult();
        return null;
    }





}
