<?php

namespace App\Repository;

use App\Entity\Playlist;
use App\Model\SearchBar;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    // favorite recommendations
   /**
    * @return Playlist[] Returns an array of Playlist objects
    */
   public function findByMostFollow($limit = null): array
    {
        
        $query = $this->createQueryBuilder('p')
            ->select('p.id, p.uuid, p.image, p.playlistName, COUNT(pu.id) AS num_followers')
            ->leftJoin('p.userFavorites', 'pu')
            ->groupBy('p.id, p.uuid, p.playlistName')
            ->orderBy('num_followers', 'DESC')
            ->setMaxResults($limit)
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

        $query = $this->createQueryBuilder('p')
            ->select('p.id, p.uuid, p.image, p.playlistName, p.dateCreated')
            ->leftJoin('p.user', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $userEmail)
            ->orderBy('p.dateCreated', 'DESC');
        
        $query = $query->getQuery();

        return $query->execute();
    }


    // find user favorites playlists
   /**
    * @return Playlist[] Returns an array of Playlist objects
    */
   public function findFavoritePlaylists($userEmail, $limit = null): array
    {
       return $this->createQueryBuilder('p')
           ->select('p.id, p.uuid, p.image, p.playlistName, pu.email')
           ->leftJoin('p.userFavorites', 'pu')
           ->where('pu.email = :email')
           ->setParameter('email', $userEmail)
           ->groupBy('p.id, p.image, p.playlistName, pu.email')
           ->setMaxResults($limit)
           ->getQuery()
           ->getResult()
       ;
    }

    
   /**
    * @return Playlist[] Returns an array of Album objects
    */
    public function findBySearch(SearchBar $searchBar): array
    {

        // SELECT *
        // FROM playlist p 
        // WHERE p.playlist_name LIKE '%p%'
        
        return $this->createQueryBuilder('p')
            ->select('p')
            ->andWhere('p.playlistName LIKE :q')
            ->setParameter('q', "%{$searchBar->q}%")
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
