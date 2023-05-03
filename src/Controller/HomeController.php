<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Playlist;
use App\Entity\Song;
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
}
