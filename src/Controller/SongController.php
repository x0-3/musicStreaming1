<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SongController extends AbstractController
{

    // song player for one song
    #[Route('/song/{id}', name: 'app_songPlayer')]
    public function index(Song $song): Response
    {
        return $this->render('song/songMusicPlayer.html.twig', [
            'song' => $song,
        ]);
    }


    // list of the user like songs
    #[Route('/song', name: 'app_like')]
    public function myPlaylist(EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();

        if ($token) {
            $userEmail = $token->getUser()->getUserIdentifier(); // get user email

            $repo = $em->getRepository(Playlist::class);
    
            $like =  $repo->findlikedSongs($userEmail); // find like song for the user

            return $this->render('song/likedSong.html.twig', [
                'like'=> $like,
            ]);
        
        }else{

            return $this->redirectToRoute('app_login');
        }
    }


    // find the top ten like song (most popular)
    #[Route('/topLiked', name: 'app_mostLiked')]
    public function topLiked(EntityManagerInterface $em): Response
    {

        $songs = $em->getRepository(Song::class)->findTenMostLikes(); // find the top ten like song

        return $this->render('song/mostLiked.html.twig', [
            'songs'=> $songs,
        ]);
    }
}
