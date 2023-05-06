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

    // find top 10 of the most followed playlists
   /**
    * @return Playlist[] Returns an array of Playlist objects
    */
   public function findByMoreMostFollow(): array
    {
        
        $query = $this->createQueryBuilder('pl')
            ->select('p.id, p.image, p.playlistName, COUNT(pu.id) AS num_followers')
            ->from('App\Entity\Playlist', 'p')
            ->leftJoin('p.userFavorites', 'pu')
            ->groupBy('p.id, p.playlistName')
            ->orderBy('num_followers', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $query;

    }
    

    // find the created playlists for a user
   /**
    * @return Playlist[] Returns an array of Playlist objects
    */
   public function findPlaylistUser($userEmail): array
   {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('p.id, p.image, p.playlistName, p.dateCreated')
            ->from('App\Entity\Playlist', 'p')
            ->leftJoin('p.user', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $userEmail)
            ->orderBy('p.dateCreated', 'DESC')

        ;
        
        $query = $query->getQuery();

        return $query->execute();
    }


    // find songs that a user liked
   /**
    * @return Playlist[] Returns an array of Playlist objects
    */
    public function findlikedSongs($userEmail): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('l.id,  u.id, s.nameSong, u.username, a.cover')
            ->from('App\Entity\Song', 's')
            ->leftJoin('s.album', 'a')
            ->leftJoin('s.user', 'u')
            ->leftJoin('s.likes', 'l')
            ->where('l.email = :email')
            ->setParameter('email', $userEmail)
        ;
        
        $query = $query->getQuery();

        return $query->execute();
    }


    // find user top 4 favorite playlist
   /**
    * @return Playlist[] Returns an array of Playlist objects
    */
   public function find4FavoritePlaylists($userEmail): array
    {
       return $this->createQueryBuilder('p')
           ->select('pl.id, pl.image, pl.playlistName, pu.email')
           ->from('App\Entity\Playlist', 'pl')
           ->leftJoin('pl.userFavorites', 'pu')
           ->where('pu.email = :email')
           ->setParameter('email', $userEmail)
           ->setMaxResults(4)
           ->getQuery()
           ->getResult()
       ;
    }

    // find user favorites playlists
   /**
    * @return Playlist[] Returns an array of Playlist objects
    */
   public function findFavoritePlaylists($userEmail): array
    {
       return $this->createQueryBuilder('p')
           ->select('pl.id, pl.image, pl.playlistName, pu.email')
           ->from('App\Entity\Playlist', 'pl')
           ->leftJoin('pl.userFavorites', 'pu')
           ->where('pu.email = :email')
           ->setParameter('email', $userEmail)
           ->groupBy('pl.id, pl.image, pl.playlistName, pu.email')
           ->getQuery()
           ->getResult()
       ;
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
