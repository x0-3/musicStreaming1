<?php

namespace App\Repository;

use App\Entity\Album;
use App\Model\SearchBar;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Album>
 *
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    public function save(Album $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Album $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    // find most recent albums for one artist
   /**
    * @return Album[] Returns an array of Album objects
    */
   public function findByMostRecentAlbumArtist($artistId, $limit = null): array
   {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('a.id, a.uuid, a.nameAlbum, a.cover, a.releaseDate')
            ->from('App\Entity\Song', 's')
            ->innerJoin('s.album', 'a')
            ->andWhere('s.user = :id')
            ->setParameter('id', $artistId)
            ->groupBy('a.id, a.nameAlbum, a.cover, a.releaseDate')
            ->orderBy('a.releaseDate', ' DESC')
            ->setMaxResults($limit);

        $query = $query->getQuery();

        return $query->execute();
    }

   /**
    * @return Album[] Returns an array of Album objects
    */
    public function findBySearch(SearchBar $searchBar): array
    {

        // SELECT *
        // FROM album a 
        // WHERE a.name_album LIKE '%m%'
        
        return $this->createQueryBuilder('a')
            ->select('a')
            ->andWhere('a.nameAlbum LIKE :q')
            ->setParameter('q', "%{$searchBar->q}%")
            ->getQuery()
            ->getResult()
        ;

    }

//    /**
//     * @return Album[] Returns an array of Album objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Album
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
