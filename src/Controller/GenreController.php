<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{

    // detail page for one genre 
    #[Route('/genre/{id}', name: 'app_genre')]
    public function index($id, EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {     

        $genre = $em->getRepository(Genre::class)->findOneBy(['uuid' => $id]);

        // TODO: add breadcrumbs
        
        $songs = $paginator->paginate(
            $em->getRepository(Song::class)->findBy(['genre' => $genre]),
            $request->query->get('page', 1),
            10
        );

        return $this->render('genre/index.html.twig', [
            'genre' => $genre,
            'songs' => $songs,
            'description' => "Discover the Ultimate ". $genre->getGenreName() ." Experience | Explore our ". $genre->getGenreName() ." Detail Page: Uncover a captivating collection of ". $genre->getGenreName() ."-themed content, carefully curated to indulge your passions. Immerse yourself in a world of ". $genre->getGenreName() ." with expertly crafted articles, in-depth analysis, exclusive interviews, and much more. Unlock the magic of ". $genre->getGenreName() ." and dive into a treasure trove of knowledge and entertainment. Let your journey begin!"
        ]);
    }
}
