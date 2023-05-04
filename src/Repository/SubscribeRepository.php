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

   /**
    * @return Subscribe[] Returns an array of Subscribe objects
    */
   public function findByMostSubscribers(): array
   {
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQueryBuilder()
        ->select("u.id, u.username, u.avatar, COUNT(s.id) AS num_followers")
        ->from('App\Entity\Subscribe', 's')
        // TODO: might need to change to subscribers
        ->innerJoin('s.userSubscribes', 'u')
        ->groupBy('u.id, u.username, u.avatar')
        ->orderBy('num_followers', 'DESC')
        ->setMaxResults(4)
        ->getQuery()
        ->getResult()
       ;

       return $query;
   }

// SELECT u.id, u.username, u.avatar, COUNT(s.id) AS num_followers
// FROM user u
// INNER JOIN subscribe s
// ON u.id=s.subscribers_id
// GROUP BY u.id, u.username, u.avatar
// ORDER BY num_followers DESC 



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
