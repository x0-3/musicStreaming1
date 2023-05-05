<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscribeController extends AbstractController
{
    #[Route('/subscribe', name: 'app_subscribe')]
    public function index(): Response
    {
        
        return $this->render('subscribe/index.html.twig', [
            'controller_name' => 'SubscribeController',
        ]);
    }
}
