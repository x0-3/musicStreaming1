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

    // detailed page for one playlist
    #[Route('/playlist/{id}', name: 'playlist_detail')]
    public function index(Playlist $playlist): Response
    {
        return $this->render('playlist/playlistDetail.html.twig', [
            'playlist' => $playlist,
        ]);
    }


    // music player page for one playlist
    #[Route('/playlist/musicPlayer/{id}', name: 'playlist_player')]
    public function playlistPlayer(Playlist $playlist): Response
    {

        $songs = $playlist->getSongs(); // get the list of songs from the playlist

        return $this->render('playlist/playlistPlayer.html.twig', [
            'playlist' => $playlist,
            'songs' => $songs,
        ]);
    }


    // get the playlists created by the user
    #[Route('/playlist', name: 'app_myPlaylist')]
    public function myPlaylist(EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();

        if ($token) {

            $userEmail = $token->getUser()->getUserIdentifier(); // get the user email
            
            $repo = $em->getRepository(Playlist::class); // get the playlist repository

            $playlists = $repo->findPlaylistUser($userEmail); // get the playlist created by the user
            $like =  $repo->findlikedSongs($userEmail); //get the liked songs of the user

            return $this->render('playlist/myPlaylist.html.twig', [
                'playlists'=> $playlists,
                'like'=> $like,
            ]);
        
            // if the user isn't logged in then redirect to login page
        }else{

            return $this->redirectToRoute('app_login');
        }
    }

}
