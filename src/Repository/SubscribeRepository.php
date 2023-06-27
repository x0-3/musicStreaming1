<?php

namespace App\Repository;

use App\Entity\Subscribe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscribe>
 *
 * @method Subscribe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscribe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscribe[]    findAll()
 * @method Subscribe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscribeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscribe::class);
    }

    public function save(Subscribe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Subscribe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
    // find top 4 artists with most subscribers 
   /**
    * @return Subscribe[] Returns an array of Subscribe objects
    */
   public function find4ByMostSubscribers(): array
    {

        $query = $this->createQueryBuilder('s')
            ->select("u.id, u.username, u.avatar, COUNT(s.id) AS num_followers")
            ->innerJoin('s.user2', 'u')
            ->groupBy('u.id, u.username, u.avatar')
            ->orderBy('num_followers', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ;

        return $query;
    }

    
    // find artists with most subscribers 
   /**
    * @return Subscribe[] Returns an array of Subscribe objects
    */
   public function findByMostSubscribers(): array
    {

        $query = $this->createQueryBuilder('s')
            ->select("u.id, u.username, u.avatar, COUNT(s.id) AS num_followers")
            ->innerJoin('s.user2', 'u')
            ->groupBy('u.id, u.username, u.avatar')
            ->orderBy('num_followers', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
        ;

        return $query;
    }


    // get the artist that a user is subscribed to 
   /**
    * @return Subscribe[] Returns an array of Subscribe objects
    */
   public function findUserSubscriber($userId, $limit = null): array
    {

        $query = $this->createQueryBuilder('s')
            ->select('s.id, u.email, us.id, us.username, us.avatar')
            ->innerJoin('s.user1', 'u')
            ->innerJoin('s.user2', 'us')
            ->where('u.email = :email')
            ->setParameter('email', $userId)
            ->groupBy('s.id ,u.email, us.id, us.username, u.avatar')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        ;

        return $query;
    }




//    /**
//     * @return Subscribe[] Returns an array of Subscribe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Subscribe
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
