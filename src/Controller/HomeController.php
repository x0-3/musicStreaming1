<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Playlist;
use App\Entity\Song;
use App\Entity\Subscribe;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {

        $playlists = $em->getRepository(Playlist::class)->findByMostFollow();
        $songs = $em->getRepository(Song::class)->findByMostLikes();

        return $this->render('home/index.html.twig', [
            'playlists' => $playlists,
            'songs' => $songs,

        ]);
    }

    // page for the detail of another user  
    #[Route('/artist/{id}', name: 'app_artistDetail')]
    public function artistPage(User $artist, EntityManagerInterface $em ): Response
    {

        $songs = $em->getRepository(Song::class)->findByArtistMostLike($artist);
        $albums = $em->getRepository(Album::class)->findByMostRecentAlbumArtist($artist);
        $artistMostSub = $em->getRepository(Subscribe::class)->findByMostSubscribers();


        return $this->render('home/artistDetail.html.twig', [
            'artist' => $artist,
            'songs' => $songs,
            'albums' => $albums,
            'artistMostSub' => $artistMostSub,

        ]);
    }



}
