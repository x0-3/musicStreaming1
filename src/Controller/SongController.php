<?php

namespace App\Controller;

use App\Entity\Song;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SongController extends AbstractController
{
    #[Route('/song/{id}', name: 'app_songPlayer')]
    public function index(Song $song): Response
    {

        
        return $this->render('song/songMusicPlayer.html.twig', [
            'song' => $song,
        ]);
    }
}
