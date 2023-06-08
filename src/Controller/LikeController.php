<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Service\CommentService;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LikeController extends AbstractController
{

    // like a song / remove song from like list
    #[Route('/like/song/{id}', name: 'like_song')]
    public function index(Song $song, EntityManagerInterface $em): Response
    {

        $user = $this->getUser(); // get user in session

        // if the user has like the song the remove the like
        if ($song->isLikeByUser($user)) {

            $song->removeLike($user);
            $em->flush();

            // return a json response
            return $this->json(['message' => 'the like has been removed']);
        }

        // else add the like
        $song->addLike($user);
        $em->flush();

        // return a json response
        return $this->json(['message' => 'the like has been added']);
    }


    // music player like song
    // with the comment form
    #[Route('/likePlayer/song/{id}', name: 'like_Player')]
    public function likePlayer($id, EntityManagerInterface $em, RequestStack $requestStack, CommentService $commentService): Response
    {

        $song = $em->getRepository(Song::class)->findOneBy(['id' => $id]);

        $user = $this->getUser();

        $userIdentifier = $user->getUserIdentifier(); // get user identifier in session

        $comment = new Comment();
        
        $form = $this->createForm(CommentType::class, $comment);

        if($user){

            $songs = $em -> getRepository(Song::class)->findlikedSongs($userIdentifier); // get all like songs of the user
            
            // comment form
            $request = $requestStack->getMainRequest(); // get the request from the request stack

            $comment->setUser($user); // set the user to current user
            $comment->setDateMess(new \DateTime()); // set the date message to the current date
            $comment->setSong($song); // set the song id to the current song
        
        
            $form->handleRequest($request);
        
            if ($form->isSubmitted()) {
                
                return $commentService->handleCommentFormData($form);
        
            }

            return $this->render('like/likePlayer.html.twig', [
                'formAddComment' => $form->createView(),
                'songs'=> $songs,
                'song'=> $song
            ]);

        } else {

            return $this->redirectToRoute('app_login');
        }

    }

    // skip to the next song of the liked songs
    #[Route('/like/skipForward/{id}', name: 'like_skipforward')]
    public function skipForward($id, SongRepository $songRepository, EntityManagerInterface $em): Response
    {

        $user = $this->getUser()->getUserIdentifier();

        $likeSongs = $em ->getRepository(Song::class)->findlikedSongs($user);

        $currentIndex = null;

        foreach ($likeSongs as $key => $song) {
            
            if ($song->getId() == $id) {

                $currentIndex = $key;
            }
        }

        if (isset($likeSongs[$currentIndex + 1])) {
            
            $nextSong = $likeSongs[$currentIndex + 1]->getId();

            $song = $songRepository->find($nextSong);
            $song->setId($nextSong);

            $em->persist($song);
            $em->flush();

            return $this->redirectToRoute('like_Player', ['id' => $nextSong]);
        
        } elseif (!isset($likeSongs[$currentIndex +  1])) {

            $firstSongId = $likeSongs[0]->getId();
            
            $song = $songRepository->find($firstSongId);
            $song->setId($firstSongId);

            $em->persist($song);
            $em->flush();
            
            return $this->redirectToRoute('like_Player', ['id' => $firstSongId]);

        }
    }


    // play previous song of the liked songs
    #[Route('/like/prevSong/{id}', name: 'like_prevSong')]
    public function prevSong($id, EntityManagerInterface $em, songRepository $songRepository): Response
    {

        $user = $this->getUser()->getUserIdentifier();

        $likeSongs = $em ->getRepository(Song::class)->findlikedSongs($user);

        $currentIndex = null;

        foreach ($likeSongs as $key => $song) {
            
            if ($song->getId() == $id) {

                $currentIndex = $key;
            }
        }

        if (isset($likeSongs[$currentIndex - 1])) {
            
            $nextSong = $likeSongs[$currentIndex - 1]->getId();

            $song = $songRepository->find($nextSong);
            $song->setId($nextSong);

            $em->persist($song);
            $em->flush();

            return $this->redirectToRoute('like_Player', ['id' => $nextSong]);
        
        }

        return $this->redirectToRoute('like_Player', ['id' => $id]);

    }
}




                
