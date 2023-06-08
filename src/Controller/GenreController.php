<?php

namespace App\Controller;

use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{

    // detail page for one genre 
    #[Route('/genre/{id}', name: 'app_genre')]
    public function index($id, EntityManagerInterface $em): Response
    {     

        $genre = $em->getRepository(Genre::class)->findOneBy(['uuid' => $id]);

        return $this->render('genre/index.html.twig', [
            'genre' => $genre,
        ]);
    }
}
