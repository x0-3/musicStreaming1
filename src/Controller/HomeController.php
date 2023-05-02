<?php

namespace App\Controller;

use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {

        $playlists = $em->getRepository(Playlist::class)->findBy([],['dateCreated'=>'DESC'], 4);

        return $this->render('home/index.html.twig', [
            'playlists' => $playlists,

        ]);
    }
}
