<?php

namespace App\Controller;

use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
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
}
