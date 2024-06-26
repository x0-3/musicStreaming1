<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Service\CommentService;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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


    #[Route('/like/shuffle', name: 'shuffle_like')]
    public function shuffleLike(SessionInterface $session, EntityManagerInterface $em): Response
    {
        $user = $this->getUser()->getUserIdentifier();

        // get the user song list
        $songs = $em -> getRepository(Song::class)->findlikedSongs($user);

        shuffle($songs);

        // Create an array of shuffled songs Ids
        $shuffledSongOrder = array_map(fn($song) => $song->getId(), $songs);

        // store the song order in the session
        $session->set('shuffled_song_order', $shuffledSongOrder);

        return $this->redirectToRoute('like_Player', ['id' => $shuffledSongOrder[0], 'isShuffled' => true]);
    }


    // music player like song
    // with the comment form
    #[Route('/likePlayer/song/{id}', name: 'like_Player')]
    public function likePlayer($id, EntityManagerInterface $em, RequestStack $requestStack, CommentService $commentService, SessionInterface $session): Response
    {

        $song = $em->getRepository(Song::class)->findOneBy(['id' => $id]);

        $user = $this->getUser();

        $userIdentifier = $user->getUserIdentifier(); // get user identifier in session

        $comment = new Comment();
        
        $form = $this->createForm(CommentType::class, $comment);

        if($user){

            $likeSongs = $em -> getRepository(Song::class)->findlikedSongs($userIdentifier); // get all like songs of the user

            $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);
            
            // if the song is shuffled then get the order of the shuffled song
            if ($isShuffled) {
                
                // get the ordered list of the shuffled song that is in session
                $shuffledSongOrder = $session->get('shuffled_song_order', []);

                $songs = $this->getShuffledSongsFromOrder($shuffledSongOrder, $likeSongs);
            } else {
    
                $songs = $likeSongs;
            }
            
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
                'song'=> $song,
                'isShuffled' => $isShuffled,
                'description' => "Discover the ultimate music player experience on our Song Page! Dive into a world of seamless melodies and crystal-clear sound. Enjoy personalized playlists, easy navigation, and an extensive library of your favorite tunes. Embrace the rhythm and explore our innovative music player today."

            ]);

        } else {

            return $this->redirectToRoute('app_login');
        }

    }

    // skip to the next song of the liked songs
    #[Route('/like/skipForward/{id}', name: 'like_skipforward')]
    public function skipForward($id, SongRepository $songRepository, EntityManagerInterface $em, RequestStack $requestStack, SessionInterface $session): Response
    {

        $user = $this->getUser()->getUserIdentifier();

        $likeSongs = $em ->getRepository(Song::class)->findlikedSongs($user);

        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        // if the song is shuffled then get the order of the shuffled song
        if ($isShuffled) {
            
            // get the ordered list of the shuffled song that is in session
            $shuffledSongOrder = $session->get('shuffled_song_order', []);
            $songs = $this->getShuffledSongsFromOrder($shuffledSongOrder, $likeSongs);
        } else {

            $songs = $likeSongs; // get the song list from the album
        }

        $currentIndex = null;

        foreach ($songs as $key => $song) {
            
            if ($song->getId() == $id) {

                $currentIndex = $key;
            }
        }

        if (isset($songs[$currentIndex + 1])) {
            
            $nextSong = $songs[$currentIndex + 1]->getId();

            $song = $songRepository->find($nextSong);
            $song->setId($nextSong);

            $em->persist($song);
            $em->flush();

            return $this->redirectToRoute('like_Player', ['id' => $nextSong, 'isShuffled' => $isShuffled]);
        
        } elseif (!isset($songs[$currentIndex +  1])) {

            $firstSongId = $songs[0]->getId();
            
            $song = $songRepository->find($firstSongId);
            $song->setId($firstSongId);

            $em->persist($song);
            $em->flush();
            
            return $this->redirectToRoute('like_Player', ['id' => $firstSongId, 'isShuffled' => $isShuffled]);

        }
    }


    // play previous song of the liked songs
    #[Route('/like/prevSong/{id}', name: 'like_prevSong')]
    public function prevSong($id, EntityManagerInterface $em, songRepository $songRepository, RequestStack $requestStack, SessionInterface $session): Response
    {

        $user = $this->getUser()->getUserIdentifier();

        $likeSongs = $em ->getRepository(Song::class)->findlikedSongs($user);

        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        // if the song is shuffled then get the order of the shuffled song
        if ($isShuffled) {
        
            // get the ordered list of the shuffled song that is in session
            $shuffledSongOrder = $session->get('shuffled_song_order', []);
            $songs = $this->getShuffledSongsFromOrder($shuffledSongOrder, $likeSongs);
        } else {

            $songs = $likeSongs; // get the song list from the album
        }

        $currentIndex = null;

        foreach ($songs as $key => $song) {
            
            if ($song->getId() == $id) {

                $currentIndex = $key;
            }
        }

        if (isset($songs[$currentIndex - 1])) {
            
            $nextSong = $songs[$currentIndex - 1]->getId();

            $song = $songRepository->find($nextSong);
            $song->setId($nextSong);

            $em->persist($song);
            $em->flush();

            return $this->redirectToRoute('like_Player', ['id' => $nextSong, 'isShuffled' => $isShuffled]);
        
        }

        return $this->redirectToRoute('like_Player', ['id' => $id, 'isShuffled' => $isShuffled]);

    }


    private function getShuffledSongsFromOrder(array $songOrder, array $songs): ArrayCollection
    {
    
        // store the order of the shuffle list
        $shuffledSongs = new ArrayCollection();
    
        foreach ($songOrder as $songId) {
            foreach ($songs as $song) {

                // if the song has the same id as the current id in the array 
                if ($song->getId() === $songId) {

                    // then add the song to the shuffledSongs collection
                    $shuffledSongs->add($song);
                    break;
                }
            }
        }
    
        return $shuffledSongs;
    }
}




                
