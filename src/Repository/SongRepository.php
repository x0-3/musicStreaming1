<?php

namespace App\Repository;

use App\Entity\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Song>
 *
 * @method Song|null find($id, $lockMode = null, $lockVersion = null)
 * @method Song|null findOneBy(array $criteria, array $orderBy = null)
 * @method Song[]    findAll()
 * @method Song[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

    public function save(Song $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Song $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    // find the most liked songs
    /**
    * @return Song[] Returns an array of Song objects
    */
    public function findByMostLikes(): array
    {

        $query = $this->createQueryBuilder('s')
            ->select('s.id, s.nameSong, a.cover, COUNT(ul.id) AS num_like')
            ->leftJoin('s.album', 'a')
            ->leftJoin('s.likes', 'ul')
            ->groupBy('s.id, s.nameSong,  a.cover')
            ->orderBy('num_like', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
            
        return $query;
    }

    /**
    * @return Song[] Returns an array of Song objects
    */
    public function findTenMostLikes(): array
    {

        $query = $this->createQueryBuilder('s')
            ->select('s.id,s.nameSong, a.cover, COUNT(ul.id) AS num_like')
            ->leftJoin('s.album', 'a')
            ->leftJoin('s.likes', 'ul')
            ->groupBy('s.id, s.nameSong,  a.cover')
            ->orderBy('num_like', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
            
        return $query;
    }


    // find artists most liked song
    /**
    * @return Song[] Returns an array of Song objects
    */
    public function findByArtistMostLike($artistId): array
    {

        $query = $this->createQueryBuilder('s')
            ->leftJoin('s.album', 'a')
            ->leftJoin('s.likes', 'ul')
            ->leftJoin('s.user', 'u')
            ->andWhere('s.user = :id')
            ->setParameter('id', $artistId)
            ->groupBy('s.id, s.nameSong,  a.cover')
            ->orderBy('COUNT(ul.id)', 'DESC')
            ->setMaxResults(3);
            
            
            
        $query = $query->getQuery();

        return $query->execute();
    }



    // find songs that a user liked
   /**
    * @return Song[] Returns an array of Song objects
    */
    public function findlikedSongs($userEmail): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.album', 'a')
            ->leftJoin('s.user', 'u')
            ->leftJoin('s.likes', 'l')
            ->where('l.email = :email')
            ->setParameter('email', $userEmail)
            ->getQuery()
           ->getResult()
        ;

    }

//    /**
//     * @return Song[] Returns an array of Song objects
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

//    public function findOneBySomeField($value): ?Song
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
