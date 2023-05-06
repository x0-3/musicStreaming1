<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    // new released page
    #[Route('/album', name: 'app_newRelease')]
    public function index(EntityManagerInterface $em): Response
    {

        $albums= $em->getRepository(Album::class)->findBy([],['releaseDate'=>'DESC'],4); // get the new released albums
        $genres = $em->getRepository(Genre::class)->findAll(); // find all the genre

        return $this->render('album/newRelease.html.twig', [
            'albums' => $albums,
            'genres' => $genres,
        ]);
    }


    // TODO: add the new release page for 20 albums


    // detail page of one album
    #[Route('/album/{id}', name: 'app_albumDetail')]
    public function albumDetail(Album $album): Response
    {
        return $this->render('album/albumDetail.html.twig', [
            'album'=> $album,
        ]);
    }


    // music player page for an album
    #[Route('/album/Player/{id}', name: 'app_albumPlayer')]
    public function albumMusicPlayer(Album $album): Response
    {

        $songs = $album->getSongs(); // get the song list from the album

        return $this->render('album/albumPlayer.html.twig', [
            'album'=> $album,
            'songs'=> $songs,
        ]);
    }
}
