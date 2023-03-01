<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
   public function trieradmin()
   {

    $conn = $this->getEntityManager()->getConnection();
       $role="admin";
        $sql = '
            SELECT * FROM user p
            WHERE p.role = :role
          
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['role' => $role]);

        // returns an array of arrays (i.e. a raw data set)
       return $stmt->fetchAll();

   }

    public function trierclient()
    {

        $conn = $this->getEntityManager()->getConnection();
        $role="client";
        $sql = ' SELECT * FROM user p  WHERE p.role = :role  ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['role' => $role]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();

    }



 
  /*  public function rechercheNomEmail($nom)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM user p
            WHERE p.nom LIKE :nom  OR  p.mail LIKE :nom 
          
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['nom' => '%'.$nom.'%' ]);
//Retourne un tableau contenant toutes les lignes du jeu d'enregistrements
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();

    }*/


    public function findByOne(  $email)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM user p
            WHERE p.email = :email 
          
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $email]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchColumn();

    }



    public function rechercheEmailrole($email,$role)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM user p
            WHERE (p.role = :role) AND (  p.email LIKE :email )
          
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['nom' => '%'.$nom.'%' , 'role'=> $role]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();

    }


    public function rechercheEmail($email)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM user p
            (  p.email LIKE :email )
          
            ';
        $stmt = $conn->prepare($sql);
        

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();

    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

   /* public function finduser():?User
    {
    
        $qb = $this->getEntityManager()->createQuery("C  ");
        return $query = $qb->getResult() ;
    }

*/
   
}
