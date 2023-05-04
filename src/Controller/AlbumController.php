<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Genre;
use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    #[Route('/album', name: 'app_newRelease')]
    public function index(EntityManagerInterface $em): Response
    {

        $albums= $em->getRepository(Album::class)->findBy([],['releaseDate'=>'DESC'],4);
        $genres = $em->getRepository(Genre::class)->findAll();

        return $this->render('album/newRelease.html.twig', [
            'albums' => $albums,
            'genres' => $genres,
        ]);
    }

    #[Route('/album/{id}', name: 'app_albumDetail')]
    public function albumDetail(Album $album): Response
    {


        return $this->render('album/albumDetail.html.twig', [
            'album'=> $album,
        ]);
    }

    #[Route('/album/Player/{id}', name: 'app_albumPlayer')]
    public function albumMusicPlayer(Album $album, EntityManagerInterface $em): Response
    {

        $songs = $album->getSongs();

        return $this->render('album/albumPlayer.html.twig', [
            'album'=> $album,
            'songs'=> $songs,
        ]);
    }
}
