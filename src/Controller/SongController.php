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
    #[Route('/song/{id}', name: 'app_songPlayer')]
    public function index(Song $song): Response
    {
        return $this->render('song/songMusicPlayer.html.twig', [
            'song' => $song,
        ]);
    }


    #[Route('/song', name: 'app_like')]
    public function myPlaylist(EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();
        if ($token) {
            $userEmail = $token->getUser()->getUserIdentifier();
            $repo = $em->getRepository(Playlist::class);
    
            $like =  $repo->findlikedSongs($userEmail);

            return $this->render('song/likedSong.html.twig', [
                'like'=> $like,
            ]);
        
        }else{

            return $this->redirectToRoute('app_login');
        }
    }
}
