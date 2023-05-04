<?php

namespace App\Controller;

use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistController extends AbstractController
{
    #[Route('/playlist/{id}', name: 'playlist_detail')]
    public function index(Playlist $playlist): Response
    {
        return $this->render('playlist/playlistDetail.html.twig', [
            'playlist' => $playlist,
        ]);
    }

    #[Route('/playlist/musicPlayer/{id}', name: 'playlist_player')]
    public function playlistPlayer(Playlist $playlist): Response
    {

        $songs = $playlist->getSongs();

        return $this->render('playlist/playlistPlayer.html.twig', [
            'playlist' => $playlist,
            'songs' => $songs,
        ]);
    }
}
