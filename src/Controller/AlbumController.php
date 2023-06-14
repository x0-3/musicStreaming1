<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\User;
use App\Entity\Album;
use App\Entity\Genre;
use App\Entity\Comment;
use App\Form\AlbumType;
use App\Form\CommentType;
use App\Service\FileUploader;
use App\Service\CommentService;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AlbumController extends AbstractController
{
    // new released page
    #[Route('/album', name: 'app_newRelease')]
    public function index(EntityManagerInterface $em): Response
    {

        $albums= $em->getRepository(Album::class)->findBy([],['releaseDate'=>'DESC'],4); // get the new released albums
        $genres = $em->getRepository(Genre::class)->findAll(); // find all the genre

        return $this->render('album/newRelease.html.twig', [
            'albums' => $albums,
            'genres' => $genres,
        ]);
    }


    // new release page for 20 albums
    #[Route('/album/newRelease', name: 'app_albums')]
    public function newReleasedAlbum(EntityManagerInterface $em): Response
    {

        $albums= $em->getRepository(Album::class)->findBy([],['releaseDate'=>'DESC'],20); // get the new released albums

        return $this->render('album/mostRecent.html.twig', [
            'albums' => $albums,
        ]);
    }


    // add a new Album
    #[Route('/album/add', name: 'add_album')]
    public function add(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, Security $security): Response
    {

        $user =  $security->getUser(); // get the user in session 

        if($user){

            $album = new Album();

            $form = $this->createForm(AlbumType::class, $album);

            $album->setReleaseDate(new \DateTime()); // set the release date to now
            $album->setUser($user); // set the user in session

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $album = $form->getData();

                // file upload
                $imageFile = $form->get('cover')->getData();
                if ($imageFile) {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $album->setCover($imageFileName);
                }

                $entityManager->persist($album);
                $entityManager->flush();

                return $this->redirectToRoute('app_profile');
            }

            return $this->render('album/newAlbum.html.twig', [
                'formAddAlbum' => $form,
            ]);

        } else {
            return $this-> redirectToRoute('app_home');
        }
 
    }


    // add a new Album
    #[Route('/album/edit/{id}', name: 'edit_album')]
    public function edit(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, Album $album, Security $security): Response
    {

        $user =  $security->getUser(); // get the user in session 

        $albumOwner = $album->getUser(); // get the owner of the album

        // if the owner of the album is strictly equal to the user in session then proceed with the edit
        if ($albumOwner === $user) {            

            $form = $this->createForm(AlbumType::class, $album);

            $album->setReleaseDate(new \DateTime());

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $album = $form->getData();

                // file upload
                $imageFile = $form->get('cover')->getData();
                if ($imageFile) {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $album->setCover($imageFileName);
                }

                $entityManager->persist($album);
                $entityManager->flush();

                return $this->redirectToRoute('app_profile');
            }

            return $this->render('album/newAlbum.html.twig', [
                'formAddAlbum' => $form,
            ]);

        }else {

            // else show the error message and redirect to the home page

            echo "You are not the owner of this playlist";
            return $this->redirectToRoute('app_home');

        }
        
    }


    // delete the album
    #[Route('/album/delete/{id}', name: 'delete_album')]
    public function delete(EntityManagerInterface $em, Album $album, Security $security)
    {

        $user =  $security->getUser(); // get the user in session        
        $albumOwner = $album->getUser(); // get the owner of the album

        // if the owner of the album is strictly equal to the user in session then proceed with the edit
        if ($albumOwner === $user) {  

            $em->remove($album);
            $em->flush();

        }

        return $this->redirectToRoute('app_profile');
    }


    // detail page of one album
    #[Route('/album/{id}', name: 'app_albumDetail')]
    public function albumDetail($id, EntityManagerInterface $em): Response
    {        

        $album = $em->getRepository(Album::class)->findOneBy(['uuid' => $id]);

        $songs = $album->getSongs();

        return $this->render('album/albumDetail.html.twig', [
            'album'=> $album,
            'songs'=> $songs,
        ]);
    }


    // TODO: comment this out
    #[Route('/album/shuffle/{id}', name: 'shuffle_album')]
    public function shuffleAlbums(Album $album, SessionInterface $session): Response
    {

        $songs = $album->getSongs()->toArray();
        shuffle($songs);

        $shuffledSongOrder = array_map(fn($song) => $song->getId(), $songs);

        $session->set('shuffled_song_order', $shuffledSongOrder);

        return $this->redirectToRoute('app_albumPlayer', ['id' => $album->getId(), 'songId' => $shuffledSongOrder[0], 'isShuffled' => true]);
    }


    // music player page for an album
    // with the comment form
    #[Route('/album/Player/{id}/song/{songId}', name: 'app_albumPlayer')]
    public function albumMusicPlayer(EntityManagerInterface $em, $id, $songId, RequestStack $requestStack, CommentService $commentService, SessionInterface $session): Response
    {
        $album= $em->getRepository(Album::class)->findOneBy(['id' => $id]);
        $song= $em->getRepository(Song::class)->findOneBy(['id' => $songId]);

        // $songs = $album->getSongs(); // get the song list from the album

        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        if ($isShuffled) {
            
            $shuffledSongOrder = $session->get('shuffled_song_order', []);
            $songs = $this->getShuffledSongsFromOrder($shuffledSongOrder, $album->getSongs());
        } else {

            $songs = $album->getSongs(); // get the song list from the album
        }

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

        return $this->render('album/albumPlayer.html.twig', [
            'formAddComment' => $form->createView(),
            'album' => $album,
            'songs' => $songs,
            'song' => $song,
            'isShuffled' => $isShuffled,
        ]);
    }


    // skip to the next song of the album
    #[Route('/album/skipForward/{id}/{songId}', name: 'app_skipforward')]
    public function skipForward(Album $album, EntityManagerInterface $entityManager, $songId, SongRepository $songRepository, RequestStack $requestStack, SessionInterface $session): Response
    {

        // Get the list of songs in the album
        // $albumSongs = $album->getSongs();

        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        if ($isShuffled) {
            
            $shuffledSongOrder = $session->get('shuffled_song_order', []);
            $songs = $this->getShuffledSongsFromOrder($shuffledSongOrder, $album->getSongs());
        } else {

            $songs = $album->getSongs(); // get the song list from the album
        }

        $currentIndex = null; // set the current array index to null
        
        // Find the index of the current song in the list
        foreach ($songs as $index => $song) {

            // if the song id the same as the current songId
            if ($song->getId() == $songId) {

                // then set the current index to the index of the current song
                $currentIndex = $index;
                
            }
        }


        // if there is a next song in the album
        if (isset($songs[$currentIndex + 1])) {
        // Get the next song ID
            $nextSongId = $songs[$currentIndex + 1]->getId();

            // Update the ID of the song
            $song = $songRepository->find($nextSongId);
            $song->setId($nextSongId);

            // Persist the changes
            $entityManager->persist($song);
            $entityManager->flush();

            // Redirect to the page of the next song
            return $this->redirectToRoute('app_albumPlayer', ['id' => $album->getId(), 'songId' => $nextSongId, 'isShuffled' => $isShuffled]);
            
        } elseif (!isset($songs[$currentIndex + 1])) {

            $firstSong = $songs[0]->getId();

            // Update the ID of the song
            $song = $songRepository->find($firstSong);
            $song->setId($firstSong);

            // Persist the changes
            $entityManager->persist($song);
            $entityManager->flush();

            // redirect it to the first song in the album
            return $this->redirectToRoute('app_albumPlayer', ['id' => $album->getId(), 'songId' => $firstSong, 'isShuffled' => $isShuffled]);
        }

    }

    // play previous song of the album
    #[Route('/album/prevSong/{id}/{songId}', name: 'app_prevSong')]
    public function prevSong(Album $album, $songId, SongRepository $songRepository, EntityManagerInterface $em, RequestStack $requestStack, SessionInterface $session): Response
    {   

        // $albumSongID = $album->getSongs();

        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        if ($isShuffled) {
            
            $shuffledSongOrder = $session->get('shuffled_song_order', []);
            $songs = $this->getShuffledSongsFromOrder($shuffledSongOrder, $album->getSongs());
        } else {

            $songs = $album->getSongs(); // get the song list from the album
        }

        $currentIndex = null;

        foreach ($songs as $index=>$song) {

            if ($song->getId() == $songId) {
                
                $currentIndex = $index;
            }
        }


        if (isset($songs[$currentIndex - 1])) {
            
            $prevSong = $songs[$currentIndex - 1]->getId();

            $song = $songRepository->find($prevSong);
            $song->setId($prevSong);

            $em ->persist($song);
            $em ->flush();

            return $this->redirectToRoute('app_albumPlayer', ['id' => $album->getId(), 'songId' => $prevSong, 'isShuffled' => $isShuffled]);

        }

        // redirect to the page of the song
        return $this->redirectToRoute('app_albumPlayer', ['id' => $album->getId(), 'songId' => $songId, 'isShuffled' => $isShuffled]);  
    }
    

    private function getShuffledSongsFromOrder(array $songOrder, Collection $songs): ArrayCollection
    {
    
        $shuffledSongs = new ArrayCollection();
    
        foreach ($songOrder as $songId) {
            $song = $songs->filter(fn($s) => $s->getId() === $songId)->first();
            if ($song) {
                $shuffledSongs->add($song);
            }
        }
    
        return $shuffledSongs;
    }
}