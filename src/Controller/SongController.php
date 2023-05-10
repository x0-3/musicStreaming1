<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Entity\Song;
use App\Form\SongType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    #[Route('/song/add', name: 'add_song')]
    public function add(EntityManagerInterface $em, Request $request, Security $security, FileUploader $fileUploader)
    {
        $user =  $security->getUser(); // get the user in session        

        // if the user is connected then proceed with the form submission
        if ($user) {


            $song = new Song();

            $song->setUser($user); //set the current user

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

                return $this->redirectToRoute('app_profile');
            }

            // vue to show form
            return $this->render('song/newSong.html.twig', [

                'formAddSong'=> $form->createView(),   
            ]);

        }
    }


    // edit song 
    #[Route('/song/edit/{id}', name: 'edit_song')]
    public function edit(EntityManagerInterface $em, Request $request, Security $security, FileUploader $fileUploader, Song $song)
    {
        $user =  $security->getUser(); // get the user in session        
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

                return $this->redirectToRoute('app_profile');
            }

            // vue to show form
            return $this->render('song/newSong.html.twig', [

                'formAddSong'=> $form->createView(),   
            ]);

        }
    }


    // delete song
    #[Route('/song/delete/{id}', name: 'delete_song')]
    public function delete(EntityManagerInterface $em, Song $song, Security $security)
    {
        $user =  $security->getUser(); // get the user in session        

        $songOwner = $song->getUser(); // owner of the song

        // if the user is equal to the song owner then delete
        if ($songOwner === $user){ 
            $em->remove($song);
            $em->flush();
        }
        return $this->redirectToRoute('app_profile');
        
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
    

    // song player for one song
    #[Route('/song/{id}', name: 'app_songPlayer')]
    public function songPlayer(Song $song): Response
    {
        return $this->render('song/songMusicPlayer.html.twig', [
            'song' => $song,
        ]);
    }

}
