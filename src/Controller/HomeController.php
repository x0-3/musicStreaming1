<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\User;
use App\Entity\Playlist;
use App\Model\SearchBar;
use App\Entity\Subscribe;
use App\Form\SearchBarType;
use App\Repository\SongRepository;
use App\Repository\UserRepository;
use App\Repository\AlbumRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HomeController extends AbstractController
{
    // main page
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {

        $playlists = $em->getRepository(Playlist::class)->findByMostFollow(5); //find by most followed playlists
        $songs = $em->getRepository(Song::class)->findByMostLikes(12); //find the most like songs   
        $artist = $em->getRepository(User::class)->findByRoles('["ROLE_ARTIST"]');

        shuffle($artist);// shuffle the array of artists

        $token = $tokenStorage->getToken();   

        if ($token) {  

            $user = $tokenStorage->getToken()->getUserIdentifier(); //get user identifier (email)
            
            $favoritePlaylists = $em->getRepository(Playlist::class)->findFavoritePlaylists($user, 5); //find the user's favorite playlists
            $artists = $em->getRepository(Subscribe::class)->findUserSubscriber($user, 10);
            $subscriptionSongs = $em->getRepository(Subscribe::class)->findBySubscriptionSong($user, 12);
            $subscriptionAlbums = $em->getRepository(Subscribe::class)->findBySubscriptionAlbum($user, 5);


            return $this->render('home/index.html.twig', [
                'playlists' => $playlists,
                'songs' => $songs,
                'favoritePlaylists' => $favoritePlaylists,
                'artists'=> $artists,
                'subscriptionSongs'=> $subscriptionSongs,
                'subscriptionAlbums'=> $subscriptionAlbums,
                'artist'=> $artist,
                'description' => "Welcome to StreamVibe - Your Gateway to Exceptional Music Experience! Discover a world of innovation, top-notch services, and unbeatable solutions tailored to meet your unique needs. Explore our diverse range of musics/genres and immerse yourself in unrivaled excellence. Join thousands of satisfied customers who have made us their go-to destination for Music. Explore, Engage, and Elevate with us today!"
            ]);

            // if the user isn't signed in, render the page without the favorites list 
        } else {

            return $this->render('home/index.html.twig', [
                'playlists' => $playlists,
                'songs' => $songs,
                'artist'=> $artist,
                'description' => "Welcome to StreamVibe - Your Gateway to Exceptional Music Experience! Discover a world of innovation, top-notch services, and unbeatable solutions tailored to meet your unique needs. Explore our diverse range of musics/genres and immerse yourself in unrivaled excellence. Join thousands of satisfied customers who have made us their go-to destination for Music. Explore, Engage, and Elevate with us today!"
            ]);
        }
    }


    // searchBar
    #[Route('/search', name: 'search')]
    public function search(Request $request, SongRepository $songRepository, UserRepository $userRepository, AlbumRepository $albumRepository, PlaylistRepository $playlistRepository): Response
    {

        $searchBar = new SearchBar();

        $form = $this->createForm(SearchBarType::class, $searchBar);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // put the request query at getInt to get new post from value
            $searchBar->page = $request->query->getInt('page', 1);

            $songs = $songRepository->findBySearch($searchBar);
            $artists = $userRepository->findBySearch($searchBar);
            $albums = $albumRepository->findBySearch($searchBar);
            $playlists = $playlistRepository->findBySearch($searchBar);

            return $this->render('home/searchPage.html.twig', [
                'form' => $form->createView(),
                'songs' => $songs,
                'artists' => $artists,
                'albums' => $albums,
                'playlists' => $playlists,
                'description' => "Discover the Power of Our Search Bar - Find what you're looking for instantly! Our user-friendly Search Bar page offers efficient and accurate results, streamlining your browsing experience. Unlock a vast database of information with ease. Try our cutting-edge Search Bar today and simplify your online journey."
            ]);
        }

        return $this->render('home/searchPage.html.twig', [
            'form' => $form->createView(),
            'songs' => $songRepository,
            'artists' => $userRepository,
            'albums' => $albumRepository,
            'playlists' => $playlistRepository,
            'description' => "Discover the Power of Our Search Bar - Find what you're looking for instantly! Our user-friendly Search Bar page offers efficient and accurate results, streamlining your browsing experience. Unlock a vast database of information with ease. Try our cutting-edge Search Bar today and simplify your online journey."
        ]);

 
    }


    // top ten most followed playlists
    #[Route('home/recommended', name: 'more_recommended')]
    public function TopFollowedPlaylists(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {

        $pagination = $paginator->paginate(
            $em->getRepository(Playlist::class)->findByMostFollow(), //find followed playlists ordered by most followed,
            $request->query->get('page', 1),
            10
        );

        return $this->render('home/recommended.html.twig', [
            'pagination' => $pagination,
            'description' => "Discover the ultimate playlist collection with our Top Ten Most Followed Playlists Page! Immerse yourself in a world of music, curated by millions of passionate users. From chart-topping hits to timeless classics, our top ten playlists are meticulously crafted to cater to every mood and taste. Start streaming now and let the melodies take you on a journey like never before. 🎶🔥 #Playlists #MusicHeaven #TopTenPlaylists"
        ]);
    }

    
    // page for all of the current user favorite playlists
    #[Route('/home/favorites', name: 'app_favorites')]
    public function favoritePlaylists(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, Request $request, PaginatorInterface $paginator): Response
    {   
        $token = $tokenStorage->getToken();
        
        if ($token) {  
            $user = $tokenStorage->getToken()->getUserIdentifier(); //get the user identifier (email)

            
            $pagination = $paginator->paginate(
                $em->getRepository(Playlist::class)->findFavoritePlaylists($user),
                $request->query->get('page', 1),
                10
            );

            return $this->render('home/favoritePlaylists.html.twig', [
                'pagination' => $pagination,
                'description' => "Discover and explore all of your favorite playlists in one place! Our user-friendly page brings you a curated collection of top-rated playlists, tailored to your unique tastes. From the latest chart-toppers to timeless classics, find the perfect soundtrack for any mood or occasion. Start enjoying your favorite tunes now!"
            ]);
        }
    }

}
