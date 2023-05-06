<?php

namespace App\Controller;

use App\Entity\Subscribe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SubscribeController extends AbstractController
{

    // get the list of artist that the user is subscribed to
    #[Route('/subscribe', name: 'app_subscribe')]
    public function index(EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {

        $token = $tokenStorage->getToken();

        if ($token) {
            $userId = $token->getUser()->getUserIdentifier(); // get the user email

            $subs = $em->getRepository(Subscribe::class)->findUserSubscriber($userId); // get the user subscriptions

            return $this->render('subscribe/ArtistSubscribe.html.twig', [
                'subs' => $subs,
            ]);

        }
    }
}
