<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    #[Route('/album', name: 'app_newRelease')]
    public function index(EntityManagerInterface $em): Response
    {

        $albums= $em->getRepository(Album::class)->findBy([],['releaseDate'=>'DESC'],4);
        $genres = $em->getRepository(Genre::class)->findAll();

        return $this->render('album/newRelease.html.twig', [
            'albums' => $albums,
            'genres' => $genres,
        ]);
    }
}
