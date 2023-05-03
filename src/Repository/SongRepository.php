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

    /**
    * @return Song[] Returns an array of Song objects
    */
    public function findByMostLikes(): array
    {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('s.id,s.nameSong, a.cover, COUNT(ul.id) AS num_like')
            ->from('App\Entity\Song', 's')
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
    public function findByArtistMostLike($artistId): array
    {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('s.id,s.nameSong, a.cover, u.username, COUNT(ul.id) AS num_like')
            ->from('App\Entity\Song', 's')
            ->leftJoin('s.album', 'a')
            ->leftJoin('s.likes', 'ul')
            ->leftJoin('s.user', 'u')
            ->andWhere('s.user = :id')
            ->setParameter('id', $artistId)
            ->groupBy('s.id, s.nameSong,  a.cover')
            ->orderBy('num_like', 'DESC')
            ->setMaxResults(3);
            
            
            
        $query = $query->getQuery();

        return $query->execute();
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
