<?php

namespace App\Controller;

use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/like/song/{id}', name: 'like_song')]
    public function index(Song $song, EntityManagerInterface $em): Response
    {

        $user = $this->getUser(); // get user in session

        // if the user has like the song the remove the like
        if ($song->isLikeByUser($user)) {

            $song->removeLike($user);
            $em->flush();

            // return a json response
            return $this->json(['message' => 'the like has been removed']);
        }

        // else add the like
        $song->addLike($user);
        $em->flush();

        // return a json response
        return $this->json(['message' => 'the like has been added']);
    }
}
