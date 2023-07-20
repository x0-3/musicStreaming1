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
            'description' => "Discover the Ultimate ". $genre->getGenreName() ." Experience | Explore our ". $genre->getGenreName() ." Detail Page: Uncover a captivating collection of ". $genre->getGenreName() ."-themed content, carefully curated to indulge your passions. Immerse yourself in a world of ". $genre->getGenreName() ." with expertly crafted articles, in-depth analysis, exclusive interviews, and much more. Unlock the magic of ". $genre->getGenreName() ." and dive into a treasure trove of knowledge and entertainment. Let your journey begin!"
        ]);
    }
}
