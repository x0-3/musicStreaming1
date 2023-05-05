<?php

namespace App\Controller;

use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    #[Route('/genre/{id}', name: 'app_genre')]
    public function index(EntityManagerInterface $em, Genre $genre): Response
    {     
        return $this->render('genre/index.html.twig', [
            'genre' => $genre,
        ]);
    }
}
