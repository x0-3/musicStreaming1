<?php

namespace App\Controller;

use App\Entity\Song;
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
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

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
            'description' => "Discover the freshest sounds and hottest tunes with our New Released Albums page! Stay up-to-date on the latest music releases from top artists across all genres. Whether you're a pop enthusiast, a hip-hop aficionado, or a rock devotee, our curated collection has something for everyone. Don't miss out on the latest hits - immerse yourself in the best of today's music scene on our New Released Albums page. Tune in now and let the melodies take you on an unforgettable journey!",
        ]);
    }


    // new release page for 20 albums
    #[Route('/album/newRelease', name: 'app_albums')]
    public function newReleasedAlbum(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {

        $albums= $em->getRepository(Album::class)->findBy([],['releaseDate'=>'DESC'],20); // get the new released albums

        $albums = $paginator->paginate(
            $em->getRepository(Album::class)->findBy([],['releaseDate'=>'DESC']),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('album/mostRecent.html.twig', [
            'albums' => $albums,
            'description' => "Discover the latest and greatest in music with our '20 Albums Page'! Explore the hottest new releases from diverse genres, curated to satisfy all musical tastes. Stay ahead of the curve and immerse yourself in the freshest sounds of 2023. From chart-topping hits to hidden gems, this is your ultimate destination for staying up-to-date with the music scene. Don't miss out on the buzz – start exploring the 20 must-listen albums today!",
        ]);
    }


    // add a new Album
    #[Route('/album/add', name: 'add_album')]
    public function add(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, Security $security): Response
    {

        $user =  $security->getUser(); // get the user in session 

        if($user){

            // create a new instance of the Album object
            $album = new Album();

            // create a form to handle the submission
            $form = $this->createForm(AlbumType::class, $album);

            $album->setReleaseDate(new \DateTime()); // set the release date to now
            $album->setUser($user); // set the user to the one stored in session

            // inspect the form request
            $form->handleRequest($request);

            // check if the form is submitted and valid
            if ($form->isSubmitted() && $form->isValid()) {
                // retrieve the data from the form
                $album = $form->getData();

                // file upload
                $imageFile = $form->get('cover')->getData();
                if ($imageFile) {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $album->setCover($imageFileName);
                }

                $entityManager->persist($album); // persist the album to the database
                $entityManager->flush(); // flush it to the database

                // redirect to the user profile page
                return $this->redirectToRoute('app_profile');
            }

            // if the form is not submitted or is not valid
            // then show the form again
            return $this->render('album/newAlbum.html.twig', [
                'formAddAlbum' => $form,
                'description' => ''
            ]);

        } else {
            // if the user is not logged in then return to the home page
            return $this-> redirectToRoute('app_home');
        }
 
    }


    // edit Album
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
                'description' => ''
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

        // if the owner of the album is strictly equal to the user in session then proceed delete the album
        if ($albumOwner === $user) {  

            // delete the album
            $em->remove($album);
            // push to the db
            $em->flush();

        }

        return $this->redirectToRoute('app_profile');
    }


    // detail page of one album
    #[Route('/album/{id}', name: 'app_albumDetail')]
    public function albumDetail($id, EntityManagerInterface $em, Breadcrumbs $breadcrumbs): Response
    {        

        $album = $em->getRepository(Album::class)->findOneBy(['uuid' => $id]);


        // breadcrumbs
        $breadcrumbs->addRouteItem($album->getNameAlbum(), "app_albumDetail", [
            'id' => $album->getUuid(),
        ]);
        
        $breadcrumbs->prependRouteItem("Home", "app_home");


        $songs = $album->getSongs();

        return $this->render('album/albumDetail.html.twig', [
            'album'=> $album,
            'songs'=> $songs,
            'description' => "Explore the mesmerizing world of ". $album->getNameAlbum() ." on our detail page. Immerse yourself in the captivating melodies and lyrical artistry of this critically acclaimed album. Discover insightful reviews, tracklist, and behind-the-scenes stories that shed light on the creative journey. Uncover the essence of ". $album->getNameAlbum() ." and experience music like never before. Join us now and be swept away by its enchanting harmonies!",
        ]);
    }


    #[Route('/album/shuffle/{id}', name: 'shuffle_album')]
    public function shuffleAlbums(Album $album, SessionInterface $session): Response
    {

        // get the songs of an album 
        $songs = $album->getSongs()->toArray();
        shuffle($songs); 

        // Create an array of shuffled songs Ids
        $shuffledSongOrder = array_map(fn($song) => $song->getId(), $songs);

        // store the song order in the session
        $session->set('shuffled_song_order', $shuffledSongOrder);

        return $this->redirectToRoute('app_albumPlayer', ['id' => $album->getId(), 'songId' => $shuffledSongOrder[0], 'isShuffled' => true]);
    }


    // music player page for an album
    // with the comment form
    #[Route('/album/Player/{id}/song/{songId}', name: 'app_albumPlayer')]
    public function albumMusicPlayer(EntityManagerInterface $em, $id, $songId, RequestStack $requestStack, CommentService $commentService, SessionInterface $session, Breadcrumbs $breadcrumbs): Response
    {
        $album= $em->getRepository(Album::class)->findOneBy(['id' => $id]);
        $song= $em->getRepository(Song::class)->findOneBy(['id' => $songId]);


        // breadcrumbs
        $breadcrumbs->addRouteItem($album->getNameAlbum(), "app_albumDetail", [
            'id' => $album->getUuid(),
        ]);

        $breadcrumbs->addRouteItem($song->getNameSong(), "app_albumPlayer", [
            'id' => $album->getId(),
            'songId' => $songId,
        ]);
        
        $breadcrumbs->prependRouteItem("Home", "app_home");


        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        if ($isShuffled) {
            
            // get the ordered list of the shuffled song that is in session
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
            'description' => "Discover the mesmerizing sounds of ". $album->getNameAlbum() ." with our cutting-edge music player. Immerse yourself in a seamless audio experience, streaming ". $album->getUser()->getUsername() ."'s latest album effortlessly. Explore tracks, lyrics, and album artwork while enjoying crystal-clear sound quality. Unleash your musical passion on our dynamic album page today!",
        ]);
    }


    // skip to the next song of the album
    #[Route('/album/skipForward/{id}/{songId}', name: 'app_skipforward')]
    public function skipForward(Album $album, EntityManagerInterface $entityManager, $songId, SongRepository $songRepository, RequestStack $requestStack, SessionInterface $session): Response
    {

        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        if ($isShuffled) {
            
            // get the ordered list of the shuffled song that is in session
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

        $isShuffled = $requestStack->getCurrentRequest()->query->getBoolean('isShuffled', false);

        if ($isShuffled) {
            
            // get the ordered list of the shuffled song that is in session
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
}