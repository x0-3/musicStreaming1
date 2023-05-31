<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\User;
use App\Entity\Album;
use Twig\Environment;
use App\Form\SongType;
use App\Entity\Comment;
use App\Entity\Playlist;
use App\Form\CommentType;
use App\Service\FileUploader;
use App\Service\CommentService;
use App\Form\AddSongsToPlaylistType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SongController extends AbstractController
{

    // find the top ten like song (most popular)
    #[Route('/topLiked', name: 'app_mostLiked')]
    public function index(EntityManagerInterface $em): Response
    {

        $songs = $em->getRepository(Song::class)->findTenMostLikes(); // find the top ten like song

        return $this->render('song/mostLiked.html.twig', [
            'songs'=> $songs,
        ]);
    }

    
    // add a new song
    #[Route('/song/{id}/add', name: 'add_song')]
    public function add(EntityManagerInterface $em, Request $request, Security $security, FileUploader $fileUploader, Album $album)
    {
        $user =  $security->getUser(); // get the user in session   

        // if the user is connected then proceed with the form submission
        if ($user) {

            $song = new Song();

            

            $form = $this->createForm(SongType::class, $song);
            $form ->handleRequest($request); //analyse whats in the request / gets the data

            // if the form is submitted and check security 
            if ($form->isSubmitted() && $form->isValid()) {

                $song = $form->getData(); // get the data submitted in form and hydrate the object 

                $song->setUser($user); //set the current user
                $song->setAlbum($album); // set the current album
                
                // music file upload
                $musicFile = $form->get('link')->getData();
                if ($musicFile) {
                    $musicFileName = $fileUploader->upload($musicFile);
                    $song->setLink($musicFileName);
                }

                // need the doctrine manager to get persist and flush
                $em->persist($song); // prepare
                $em->flush(); // execute

                return $this->redirectToRoute('app_albumDetail',['id'=>$album->getId()]);
            }

            // vue to show form
            return $this->render('song/newSong.html.twig', [

                'formAddSong'=> $form->createView(),   
                'album'=> $album,
            ]);

        } else {
            
            // else return to the current page
            return $this->redirectToRoute('app_albumDetail',['id'=>$album->getId()]);
        }
    }


    // edit song 
    #[Route('/song/edit/{id}', name: 'edit_song')]
    public function edit(EntityManagerInterface $em, Request $request, FileUploader $fileUploader, Song $song)
    {
        $user =  $this->getUser(); // get the user in session        
        $songOwner = $song->getUser(); // owner of the song


        // if the user is the same as the song owner
        if ($songOwner === $user) {

            $form = $this->createForm(SongType::class, $song);
            $form ->handleRequest($request); //analyse whats in the request / gets the data

            // if the form is submitted and check security 
            if ($form->isSubmitted() && $form->isValid()) {

                $song = $form->getData(); // get the data submitted in form and hydrate the object 

                $em->getRepository(Song::class);

                // music file upload
                $musicFile = $form->get('link')->getData();
                if ($musicFile) {
                    $musicFileName = $fileUploader->upload($musicFile);
                    $song->setLink($musicFileName);
                }

                // need the doctrine manager to get persist and flush
                $em->persist($song); // prepare
                $em->flush(); // execute

                return $this->redirectToRoute('app_profile', ['id' => $song->getAlbum()->getId()]);
            }

            // vue to show form
            return $this->render('song/newSong.html.twig', [

                'formAddSong'=> $form->createView(),   
            ]);

        } else  {

            // if the user isn't the song owner then redirect to current album page
            return $this->redirectToRoute('app_albumDetail', ['id' => $song->getAlbum()->getId()]);
        }
    }


    // delete song
    #[Route('/song/delete/{id}', name: 'delete_song')]
    public function delete(EntityManagerInterface $em, Song $song)
    {
        $user =  $this->getUser(); // get the user in session  

        $songOwner = $song->getUser(); // owner of the song

        // if the user is equal to the song owner then delete
        if ($songOwner === $user){ 
            $em->remove($song);
            $em->flush();
        }
        
        return $this->redirectToRoute('app_albumDetail', ['id' => $song->getAlbum()->getId()]);
        
    }


    // list of the user like songs
    #[Route('/song', name: 'app_like')]
    public function myPlaylist(EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();

        if ($token) {
            $userEmail = $token->getUser()->getUserIdentifier(); // get user email

            $repo = $em->getRepository(Song::class);
    
            $like =  $repo->findlikedSongs($userEmail); // find like song for the user

            return $this->render('song/likedSong.html.twig', [
                'like'=> $like,
            ]);
        
        }else{

            return $this->redirectToRoute('app_login');
        }
    }
    

    // FIXME: not adding songs in db change view to html form
    #[Route('/song/playlist/{id}', name: 'add_toSongsPlaylist')]
    public function addSong(Request $request, Song $song, EntityManagerInterface $em): Response
    {
        $user = $this->getUser()->getUserIdentifier(); // get user in session

        $form = $this->createForm(AddSongsToPlaylistType::class, $song);

        $playlists = $em->getRepository(Playlist::class)->findPlaylistUser($user);

        if ($user) {
            
            $form->handleRequest($request);
                       
            if ($form->isSubmitted() && $form->isValid()) {
                
                $song = $form->getData();
        
    
                $em->persist($song);
                $em->flush();
    
                return $this->redirectToRoute('app_like');
            }
    
            return $this->render('song/addSongsToPlaylist.html.twig', [
                'form' => $form->createView(),
                'playlists' => $playlists,
                'song' => $song
            ]);
        }

    }


    // song player for one song
    // with comment form
    #[Route('/song/{id}', name: 'app_songPlayer')]
    public function songPlayer(Song $song, RequestStack $requestStack, CommentService $commentService, EntityManagerInterface $em): Response
    {

        // for the comment section 
        $user = $this->getUser();

        // just set up a fresh $task object (remove the example data)
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
    

        // render the page for the media player section
        return $this->render('song/songMusicPlayer.html.twig', [
            'formAddComment' => $form->createView(),
            'song' => $song,
        ]);

    }

}
