<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\Playlist;
use App\Form\SearchBarType;
use App\Model\SearchBar;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HomeController extends AbstractController
{
    // main page
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, Request $request, SongRepository $songRepository): Response
    {

        $playlists = $em->getRepository(Playlist::class)->findByMostFollow(); //find by most followed playlists
        $songs = $em->getRepository(Song::class)->findByMostLikes(); //find the most like songs        
        

        // searchBar
        $searchBar = new SearchBar();

        $form = $this->createForm(SearchBarType::class, $searchBar);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // put the request query at getInt to get new post from value
            $searchBar->page = $request->query->getInt('page', 1);

            $songs = $songRepository->findBySearch($searchBar);
        }


        $token = $tokenStorage->getToken();   

        if ($token) {  

            $user = $tokenStorage->getToken()->getUserIdentifier(); //get user identifier (email)
            
            $favoritePlaylists = $em->getRepository(Playlist::class)->find4FavoritePlaylists($user); //find the user's favorite playlists
            
            return $this->render('home/index.html.twig', [
                'form' => $form->createView(),
                'playlists' => $playlists,
                'songs' => $songs,
                'favoritePlaylists' => $favoritePlaylists,

            ]);

            // if the user isn't signed in, render the page without the favorites list 
        } else {

            return $this->render('home/index.html.twig', [
                'form' => $form->createView(),
                'playlists' => $playlists,
                'songs' => $songs,

            ]);
        }
    }


    // top ten most followed playlists
    #[Route('home/recommended', name: 'more_recommended')]
    public function TopFollowedPlaylists(EntityManagerInterface $em): Response
    {

        $playlists = $em->getRepository(Playlist::class)->findByMoreMostFollow(); //find followed playlists ordered by most followed

        return $this->render('home/recommended.html.twig', [
            'playlists' => $playlists,
        ]);
    }

    
    // page for all of the current user favorite playlists
    #[Route('/home/favorites', name: 'app_favorites')]
    public function favoritePlaylists(EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {   
        $token = $tokenStorage->getToken();
        
        if ($token) {  
            $user = $tokenStorage->getToken()->getUserIdentifier(); //get the user identifier (email)

            $favoritePlaylists = $em->getRepository(Playlist::class)->findFavoritePlaylists($user); //find the user's favorite playlists

            return $this->render('home/favoritePlaylists.html.twig', [
                'favoritePlaylists' => $favoritePlaylists,

            ]);
        }
    }

}
