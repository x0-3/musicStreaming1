<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\Comment;
use App\Entity\Playlist;
use App\Form\CommentType;
use App\Form\PlaylistType;
use App\Service\FileUploader;
use App\Form\PlaylistSongsType;
use App\Service\CommentService;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PlaylistController extends AbstractController
{

    // my playlist page
    #[Route('/playlist', name: 'app_myPlaylist')]
    public function index(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, Request $request, PaginatorInterface $paginator): Response
    {
        $token = $tokenStorage->getToken();

        if ($token) {

            $userEmail = $token->getUser()->getUserIdentifier(); // get the user email
            
            $repo = $em->getRepository(Playlist::class); // get the playlist repository

            $playlists = $repo->findPlaylistUser($userEmail); // get the playlist created by the user
            
            $like = $em->getRepository(Song::class)->findlikedSongs($userEmail);

            $playlists = $paginator->paginate(
                $playlists, $request->query->getInt('page', 1),
                6
            );

            return $this->render('playlist/myPlaylist.html.twig', [
                'playlists'=> $playlists,
                'like'=> $like,
                'description' => "Discover the ultimate playlist collection at Page! Our hand-curated selection of music playlists spans various genres and moods, designed to captivate your senses and elevate your musical journey. Whether you're seeking energizing beats for workouts or soulful melodies to unwind, Page's playlist page has something for every music enthusiast. Explore now and let the rhythm of our expertly crafted playlists transport you to a world of musical bliss. Start listening today!",
            ]);
        
            // if the user isn't logged in then redirect to login page
        }else{

            return $this->redirectToRoute('app_login');
        }
    }

    
    // shuffles the song inside the playlist
    #[Route('/playlist/shuffle/{id}', name: 'shuffle_playlist')]
    public function shufflePlaylist(Playlist $playlist, SessionInterface $session)
    { 
        // get the songs of an playlist 
        $songs = $playlist->getSongs()->toArray();
        shuffle($songs);

        // Create an array of shuffled songs Ids
        $shuffledSongOrder = array_map(fn($song) => $song->getId(), $songs);

        // store the song order in the session
        $session->set('shuffled_song_order', $shuffledSongOrder);
    
        return $this->redirectToRoute('playlist_player', ['id' => $playlist->getId(), 'songId' => $shuffledSongOrder[0], 'isShuffled' => true]);
        
    }


    // music player page for one playlist
    // with the comment form
    #[Route('/musicPlayer/{id}/song/{songId}', name: 'playlist_player')]
    public function playlistPlayer($id, EntityManagerInterface $em, $songId, Security $security, RequestStack $requestStack, CommentService $commentService, SessionInterface $session, Breadcrumbs $breadcrumbs): Response
    {
        $playlist = $em->getRepository(Playlist::class)->findOneBy(['id'=>$id]);
        $song = $em->getRepository(Song::class)->findOneBy(['id'=>$songId]);

        // breadcrumbs
        $breadcrumbs->addRouteItem($playlist->getPlaylistName(), "playlist_detail", [
            'id' => $playlist->getUuid(),
        ]);
        
        $breadcrumbs->addRouteItem($song->getNameSong(), "playlist_player", [
            'id' => $playlist->getUuid(),
            'songId' => $songId,
        ]);

        $breadcrumbs->prependRouteItem("Home", "app_home");

        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        // if the song is shuffled then get the order of the shuffled song
        if ($isShuffled) {

            // get the ordered list of the shuffled song that is in session
            $shuffledSongOrder = $session->get('shuffled_song_order', []);
            $songs = $this->getShuffledSongsFromOrder($shuffledSongOrder, $playlist->getSongs());
        } else {

            $songs = $playlist->getSongs();
        }

        // for the comment section 
        $user = $security->getUser();

        $comment = new Comment();
        
        $form = $this->createForm(CommentType::class, $comment);

        if ($user) {
            $request = $requestStack->getMainRequest(); // get the request from the request stack

            $comment->setUser($user); // set the user to connect user
            $comment->setDateMess(new \DateTime()); // set the date message to the current date
            $comment->setSong($song); // set the song id to the current song


            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                
                return $commentService->handleCommentFormData($form);

            }
                        
        }

        return $this->render('playlist/playlistPlayer.html.twig', [
            'formAddComment' => $form->createView(),
            'playlist' => $playlist,
            'songs' => $songs,
            'song' => $song,
            'isShuffled' => $isShuffled,
            'description' => "Immerse yourself in a captivating musical journey with our cutting-edge music player for the ultimate playlist experience. Discover a curated selection of songs meticulously arranged to match your mood and taste. With seamless navigation and stunning visuals, our music player page brings harmony and rhythm to your ears. Explore, play, and indulge in the melodic bliss today!", 
        ]);

    }

    
    // skip to the next song of the playlist
    #[Route('/playlist/skipForward/{id}/{songId}', name: 'playlist_skipforward')]
    public function skipForward(Playlist $playlist, $songId, SongRepository $songRepository, EntityManagerInterface $em, RequestStack $requestStack, SessionInterface $session): Response
    {

        // $songs = $playlist->getSongs();
        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        if ($isShuffled) {

            // get the ordered list of the shuffled song that is in session
            $shuffledSongOrder = $session->get('shuffled_song_order', []);
            $songs = $this->getShuffledSongsFromOrder($shuffledSongOrder, $playlist->getSongs());
        } else {
            $songs = $playlist->getSongs();
        }

        $currentIndex = null;

        foreach ($songs as $key => $song) {
            
            if ($song->getId() == $songId) {
                
                $currentIndex = $key;
            }
        }


        if (isset($songs[$currentIndex + 1])) {

            $nextSongId = $songs[$currentIndex + 1]->getId();

            $song = $songRepository->find($nextSongId);
            $song->setId($nextSongId);

            $em->persist($song);
            $em->flush();


            return $this->redirectToRoute('playlist_player', ['id' => $playlist->getId(), 'songId' => $nextSongId, 'isShuffled' => $isShuffled]);

        } elseif (!isset($songs[$currentIndex + 1])) {
            
            $firstSongId = $songs[0]->getId();

            $song = $songRepository->find($firstSongId);
            $song->setId($firstSongId);

            $em->persist($song);
            $em->flush();


            return $this->redirectToRoute('playlist_player', ['id' => $playlist->getId(), 'songId' => $firstSongId, 'isShuffled' => $isShuffled]);
        }
     
    }


    // play previous song of the playlist
    #[Route('/playlist/prevSong/{id}/{songId}', name: 'playlist_prevSong')]
    public function prevSong(Playlist $playlist, $songId, SongRepository $songRepository, EntityManagerInterface $em, RequestStack $requestStack, SessionInterface $session): Response
    {

        // $songs = $playlist->getSongs();

        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        if ($isShuffled) {

            // get the ordered list of the shuffled song that is in session
            $shuffledSongOrder = $session->get('shuffled_song_order', []);
            $songs = $this->getShuffledSongsFromOrder($shuffledSongOrder, $playlist->getSongs());
        } else {

            $songs = $playlist->getSongs();
        }

        $currentIndex = null;

        foreach ($songs as $key => $song) {
            
            if ($song->getId() == $songId) {
                
                $currentIndex = $key;
            }
        }

        if (isset($songs[$currentIndex - 1])) {

            $nextSongId = $songs[$currentIndex - 1]->getId();

            $song = $songRepository->find($nextSongId);
            $song->setId($nextSongId);

            $em->persist($song);
            $em->flush();


            return $this->redirectToRoute('playlist_player', ['id' => $playlist->getId(), 'songId' => $nextSongId, 'isShuffled' => $isShuffled]);

        } 

        return $this->redirectToRoute('playlist_player', ['id' => $playlist->getId(), 'songId' => $songId, 'isShuffled' => $isShuffled]);  
    }


    private function getShuffledSongsFromOrder(array $songOrder, Collection $songs): ArrayCollection
    {
        // store the shuffled song order
        $shuffledSongs = new ArrayCollection();
    
        foreach ($songOrder as $songId) {

            // filter is used to find the song with the same id
            $song = $songs->filter(fn($s) => $s->getId() === $songId)->first();
            
            // if there is a song with the same id
            if ($song) {
                // then add the song to the shuffledSongs collection
                $shuffledSongs->add($song);
            }
        }
    
        return $shuffledSongs;
    }


    // create a new playlist
    #[Route('/playlist/add', name: 'add_playlist')]
    public function add(EntityManagerInterface $em, Playlist $playlist = null, Request $request, Security $security, FileUploader $fileUploader)
    {
        $user =  $this->getUser(); // get the user in session

        // if the user is connected then proceed with the form submission
        if ($user) {

            // create new instance of Playlist object
            $playlist = new Playlist();

            $playlist->setDateCreated(new \DateTime()); //set the date to the current date
            $playlist->setUser($user); //set the user the current user

            // create a form from the type PlaylistType
            $form = $this->createForm(PlaylistType::class, $playlist);
            $form ->handleRequest($request); //analyse whats in the request / gets the data

            // if the form is submitted and check security 
            if ($form->isSubmitted() && $form->isValid()) {

                $playlist = $form->getData(); // get the data submitted in form and hydrate the object 


                // file upload
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $playlist->setImage($imageFileName);
                }

                // need the doctrine manager to get persist and flush
                $em->persist($playlist); // prepare
                $em->flush(); // execute

                // redirect to the detail page of the playlist that was just created
                return $this->redirectToRoute('playlist_detail', ['id' => $playlist->getUuid()]);
            }

            // vue to show form
            return $this->render('playlist/newPlaylist.html.twig', [

                'formAddPlaylist'=> $form->createView(),   
                'description' => ''
            ]);

        }
    }


    // edit playlists
    #[Route('/playlist/{id}/edit', name: 'edit_playlist')]
    public function edit(EntityManagerInterface $em, $id, Request $request, Security $security, FileUploader $fileUploader)
    {

        $playlist = $em->getRepository(Playlist::class)->findOneBy(['uuid' => $id]);

        $user =  $security->getUser(); // get the user in session 


        $playlistOwner = $playlist->getUser(); // owner of the playlist

        // if the owner of the playlist is strictly equal to the user in session then proceed with the edit
        if ($playlistOwner === $user) {            
            
            $playlist->setDateCreated(new \DateTime()); //get current date

            $form = $this->createForm(PlaylistType::class, $playlist);
            $form ->handleRequest($request); //analyse whats in the request / gets the data

            // if the form is submitted and check security 
            if ($form->isSubmitted() && $form->isValid()) {

                $playlist = $form->getData(); // get the data submitted in form and hydrate the object 

                $em->getRepository(Playlist::class);

                // file upload
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $playlist->setImage($imageFileName);
                }

                // need the doctrine manager to get persist and flush
                $em->persist($playlist); // prepare
                $em->flush(); // execute

                return $this->redirectToRoute('app_myPlaylist');
            }

            // vue to show form
            return $this->render('playlist/newPlaylist.html.twig', [

                'formAddPlaylist'=> $form->createView(),   
                'edit'=> $playlist->getId(), 
                'description' => ''
            ]);

        } else {

            // else show the error message and redirect to the home page

            echo "You are not the owner of this playlist";
            return $this->redirectToRoute('app_home');

        }
    }


    // delete playlist
    #[Route('/playlist/{id}/delete', name: 'delete_playlist')]
    public function delete(EntityManagerInterface $em, Playlist $playlist, Security $security)
    {
        $user =  $security->getUser(); // get the user in session 

        $playlistOwner = $playlist->getUser(); // owner of the playlist

        // if the user is equal to the playlist owner then delete
        if ($playlistOwner === $user){ 
            $em->remove($playlist);
            $em->flush();
        }
        return $this->redirectToRoute('app_myPlaylist');
        
    }


    // add / remove a playlist from favorites
    #[Route('/favorite_playlist/{id}', name: 'favorite_playlist')]
    public function favorite(Playlist $playlist, EntityManagerInterface $em)
    {
        $user = $this->getUser(); // get user in session

        // if the user has like the song the remove the like
        if ($playlist->isPlaylistByUser($user)) {

            $playlist->removeUserFavorite($user);
            $em->flush();

            // return a json response
            return $this->json(['message' => 'the favorites has been removed']);
        }

        // else add the like
        $playlist->addUserFavorite($user);
        $em->flush();

        // return a json response
        return $this->json(['message' => 'the favorites has been added']);
        
    }

    
    // TODO: remove or find a better way to display
    // add songs to a playlist
    #[Route('/playlist/song/{id}', name: 'add_toPlaylist')]
    public function addSongs(Request $request, Playlist $playlist, EntityManagerInterface $em): Response
    {
        $user = $this->getUser(); // get user in session

        $form = $this->createForm(PlaylistSongsType::class, $playlist);

        if ($user) {
            
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
    
                $playlist = $form->getData();
    
                // ... perform some action, such as saving the task to the database
                $em->persist($playlist);
    
                // actually executes the queries (i.e. the INSERT query)
                $em->flush();
    
                return $this->redirectToRoute('playlist_detail', ['id' => $playlist->getUuid()]);
            }
    
        }

        return $this->render('playlist/_addSongPlaylist.html.twig', [
            'form' => $form,
            'playlist' => $playlist,
            'description' => ''
        ]);
    }

    // detailed page for one playlist
    #[Route('/playlist/{id}', name: 'playlist_detail')]
    public function detailPlaylist($id, EntityManagerInterface $em, Breadcrumbs $breadcrumbs): Response
    {

        $playlist = $em->getRepository(Playlist::class)->findOneBy(['uuid' => $id]);

        // breadcrumbs
        $breadcrumbs->addRouteItem($playlist->getPlaylistName(), "playlist_detail", [
            'id' => $playlist->getUuid(),
        ]);
    
        $breadcrumbs->prependRouteItem("Home", "app_home");

        $songs = $playlist->getSongs();

        return $this->render('playlist/playlistDetail.html.twig', [
            'playlist' => $playlist,
            'songs' => $songs,
            'description' => "Discover the ultimate playlist page filled with an expertly curated selection of music and audio delights. Unleash your senses with a diverse array of genres, artists, and moods, meticulously arranged for your listening pleasure. Whether you're seeking heart-pounding beats or soothing melodies, this detailed playlist page has it all. Elevate your music experience and embark on a sonic journey like never before. Tune in now and immerse yourself in the harmonious world of extraordinary playlists."
        ]);
    }

}
