<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function save(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // like recommendations
   /**
    * @return Playlist[] Returns an array of Playlist objects
    */
   public function findByMostFollow(): array
    {
        $entityManager = $this->getEntityManager();
        
        $query = $entityManager->createQueryBuilder()
            ->select('p.id, p.image, p.playlistName, COUNT(pu.id) AS num_followers')
            ->from('App\Entity\Playlist', 'p')
            ->leftJoin('p.userFavorites', 'pu')
            ->groupBy('p.id, p.playlistName')
            ->orderBy('num_followers', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();

        return $query;

    }
    
   /**
    * @return Playlist[] Returns an array of Playlist objects
    */
   public function findPlaylistUser($userEmail): array
   {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('p.id, u.id, p.image, p.playlistName, p.dateCreated')
            ->from('App\Entity\Playlist', 'p')
            ->leftJoin('p.user', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $userEmail)
            ->orderBy('p.dateCreated', 'DESC')

        ;
        
        $query = $query->getQuery();

        return $query->execute();
    }

   /**
    * @return Playlist[] Returns an array of Playlist objects
    */
    public function findlikedSongs($userEmail): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('l.id')
            ->from('App\Entity\Song', 's')
            ->leftJoin('s.likes', 'l')
            ->where('l.email = :email')
            ->setParameter('email', $userEmail)
        ;
        
        $query = $query->getQuery();

        return $query->execute();
    }

//    /**
//     * @return Playlist[] Returns an array of Playlist objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Playlist
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
