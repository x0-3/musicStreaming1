<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Comment;
use App\Entity\Genre;
use App\Entity\Song;
use App\Form\AlbumType;
use App\Form\CommentType;
use App\Service\CommentService;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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


    // // music player page for an album
    // #[Route('/album/Player/{id}', name: 'app_albumPlayer')]
    // public function albumMusicPlayer(Album $album): Response
    // {

    //     $songs = $album->getSongs(); // get the song list from the album

    //     return $this->render('album/albumPlayer.html.twig', [
    //         'album'=> $album,
    //         'songs'=> $songs,
    //     ]);
    // }


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
    public function albumDetail(Album $album): Response
    {        

        $songs = $album->getSongs();

        return $this->render('album/albumDetail.html.twig', [
            'album'=> $album,
            'songs'=> $songs,
        ]);
    }


    // tests
    // get the song for an album 
    #[Route('/skipForward', name: 'app_skipforward')]
    public function skipForward(Request $request): JsonResponse
    {
        $url = $request->get('url');

        // Logic to fetch the new audio file URL goes here

        $data = [
            'link' => $url,
        ];

        return $this->json($data);
    }


    // music player page for an album
    // with the comment form
    #[Route('/album/Player/{id}/song/{song}', name: 'app_albumPlayer')]
    public function albumMusicPlayer(Album $album, RouterInterface $router, Song $song, Security $security, RequestStack $requestStack, CommentService $commentService): Response
    {
        $songs = $album->getSongs(); // get the song list from the album
        $skipForwardUrl = $router->generate('app_skipforward', ['id' => $album->getId()]);


        // for the comment section 
        $user = $security->getUser();

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
            'skipForwardUrl' => $skipForwardUrl,
        ]);
    }


}
