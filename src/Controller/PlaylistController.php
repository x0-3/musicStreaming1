<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Playlist;
use App\Entity\Song;
use App\Form\CommentType;
use App\Form\PlaylistSongsType;
use App\Form\PlaylistType;
use App\Service\CommentService;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PlaylistController extends AbstractController
{

    // my playlist page
    #[Route('/playlist', name: 'app_myPlaylist')]
    public function index(EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();

        if ($token) {

            $userEmail = $token->getUser()->getUserIdentifier(); // get the user email
            
            $repo = $em->getRepository(Playlist::class); // get the playlist repository

            $playlists = $repo->findPlaylistUser($userEmail); // get the playlist created by the user
            
            $like = $em->getRepository(Song::class)->findlikedSongs($userEmail);
            // $like =  $repo->findlikedSongs($userEmail); //get the liked songs of the user

            return $this->render('playlist/myPlaylist.html.twig', [
                'playlists'=> $playlists,
                'like'=> $like,
            ]);
        
            // if the user isn't logged in then redirect to login page
        }else{

            return $this->redirectToRoute('app_login');
        }
    }


    // music player page for one playlist
    // with the comment form
    #[Route('/musicPlayer/{id}/song/{song}', name: 'playlist_player')]
    public function playlistPlayer(Playlist $playlist, Song $song, Security $security, RequestStack $requestStack, CommentService $commentService): Response
    {

        $songs = $playlist->getSongs(); // get the list of songs from the playlist

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
        ]);
    }

    
    // skip to the next song of the playlist
    #[Route('/playlist/skipForward/{id}/{song}', name: 'playlist_skipforward')]
    public function skipForward(Playlist $playlist, Song $song): Response
    {

        $songId=$song->getId(); // get the song id

        $songId++; // increment the song id

        // redirect to the page of the next song
        return $this->redirectToRoute('playlist_player', ['id' => $playlist->getId(), 'song' => $songId]);  
    }


    // play previous song of the playlist
    #[Route('/playlist/prevSong/{id}/{song}', name: 'playlist_prevSong')]
    public function prevSong(Playlist $playlist, Song $song): Response
    {

        $songId=$song->getId(); // get the song id

        $songId--; // increment the song id

        // redirect to the page of the next song
        return $this->redirectToRoute('playlist_player', ['id' => $playlist->getId(), 'song' => $songId]);  
    }


    // create a new playlist
    #[Route('/playlist/add', name: 'add_playlist')]
    public function add(EntityManagerInterface $em, Playlist $playlist = null, Request $request, Security $security, FileUploader $fileUploader)
    {
        $user =  $security->getUser(); // get the user in session        

        // if the user is connected then proceed with the form submission
        if ($user) {


            $playlist = new Playlist();

            $playlist->setDateCreated(new \DateTime()); //get current date
            $playlist->setUser($user); //set the current user

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
            ]);

        }
    }


    // edit playlists
    #[Route('/playlist/{id}/edit', name: 'edit_playlist')]
    public function edit(EntityManagerInterface $em, Playlist $playlist, Request $request, Security $security, FileUploader $fileUploader)
    {
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
    
                return $this->redirectToRoute('playlist_detail', ['id' => $playlist->getId()]);
            }
    
        }

        return $this->render('playlist/addSongPlaylist.html.twig', [
            'form' => $form,
            'playlist' => $playlist
        ]);
    }

    // detailed page for one playlist
    #[Route('/playlist/{id}', name: 'playlist_detail')]
    public function detailPlaylist(Playlist $playlist): Response
    {

        $songs = $playlist->getSongs();

        return $this->render('playlist/playlistDetail.html.twig', [
            'playlist' => $playlist,
            'songs' => $songs,
        ]);
    }

}
