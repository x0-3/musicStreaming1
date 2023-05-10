<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Genre;
use App\Form\AlbumType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
    #[Route('/album/Player/{id}', name: 'app_albumPlayer')]
    public function albumMusicPlayer(Album $album): Response
    {

        $songs = $album->getSongs(); // get the song list from the album

        return $this->render('album/albumPlayer.html.twig', [
            'album'=> $album,
            'songs'=> $songs,
        ]);
    }


    // add a new Album
    #[Route('/album/add', name: 'add_album')]
    public function add(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {

        $album = new Album();

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
 
    }


    // add a new Album
    #[Route('/album/edit/{id}', name: 'edit_album')]
    public function edit(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, Album $album): Response
    {

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
    
    }


    // delete the album
    #[Route('/album/delete/{id}', name: 'delete_album')]
    public function delete(EntityManagerInterface $em, Album $album)
    {

        $em->remove($album);
        $em->flush();
        return $this->redirectToRoute('app_myPlaylist');
        
    }


    // detail page of one album
    #[Route('/album/{id}', name: 'app_albumDetail')]
    public function albumDetail(Album $album): Response
    {
        return $this->render('album/albumDetail.html.twig', [
            'album'=> $album,
        ]);
    }

    // tests 
    // #[Route('/skipForward', name: 'app_skipforward')]
    // public function skipForward(Request $request): JsonResponse
    // {
    //     $url = $request->get('url');

    //     // Logic to fetch the new audio file URL goes here

    //     $data = [
    //         'link' => $url,
    //     ];

    //     return $this->json($data);
    // }


    // // music player page for an album
    // #[Route('/album/Player/{id}', name: 'app_albumPlayer')]
    // public function albumMusicPlayer(Album $album, RouterInterface $router): Response
    // {
    //     $songs = $album->getSongs(); // get the song list from the album
    //     $skipForwardUrl = $router->generate('app_skipforward', ['id' => $album->getId()]);

    //     return $this->render('album/albumPlayer.html.twig', [
    //         'album' => $album,
    //         'songs' => $songs,
    //         'skipForwardUrl' => $skipForwardUrl,
    //     ]);
    // }


}
