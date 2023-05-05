<?php

namespace App\Controller;

use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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


    #[Route('/playlist', name: 'app_myPlaylist')]
    public function myPlaylist(EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();
        if ($token) {
            $userEmail = $token->getUser()->getUserIdentifier();
            $repo = $em->getRepository(Playlist::class);
            $playlists = $repo->findPlaylistUser($userEmail);
    
            $like =  $repo->findlikedSongs($userEmail);

            return $this->render('playlist/myPlaylist.html.twig', [
                'playlists'=> $playlists,
                'like'=> $like,
            ]);
        
        }else{

            return $this->redirectToRoute('app_login');
        }
    }
}
