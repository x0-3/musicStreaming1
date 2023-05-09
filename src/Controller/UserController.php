<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Song;
use App\Entity\Subscribe;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    // page for the detail of another user  
    #[Route('/artist/{id}', name: 'app_artistDetail')]
    public function artistPage(User $artist, EntityManagerInterface $em ): Response
    {

        $songs = $em->getRepository(Song::class)->findByArtistMostLike($artist); //find the artist's most like songs
        $albums = $em->getRepository(Album::class)->findByMostRecentAlbumArtist($artist); //find the artist's most recent albums
        $artistMostSub = $em->getRepository(Subscribe::class)->find4ByMostSubscribers(); //find the artist's with the most subscribers 


        return $this->render('user/artistDetail.html.twig', [
            'artist' => $artist,
            'songs' => $songs,
            'albums' => $albums,
            'artistMostSub' => $artistMostSub,

        ]);
    }


    // find all of the artist albums ordered by most recent 
    #[Route('/artist/{id}/album', name: 'app_artistAlbum')]
    public function artistAlbum(User $artist, EntityManagerInterface $em ): Response
    {

        $albums = $em->getRepository(Album::class)->findByMostRecentAlbumArtist($artist); //find the artist's most recent albums

        return $this->render('user/moreArtistAlbum.html.twig', [
            'albums' => $albums,
        ]);
    }


    // ************************************************* profil page ********************************************************** //
    #[Route('/profil', name: 'app_profil')]
    public function profilPage(EntityManagerInterface $em, Security $security): Response
    {
        
        $user =  $security->getUser();

        if ($user) {
            
            $albums = $em->getRepository(Album::class)->findByMostRecentAlbumArtist($user); //find the artist's most recent albums


            return $this->render('user/profil.html.twig', [
                'user' => $user,
                'albums' => $albums,
            ]);

        } else {

            return $this->redirectToRoute('app_login');

        }

    }
}
