<?php

// src/Controller/TwitterController.php

namespace App\Controller;

use App\Service\TwitterConnectionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwitterController extends AbstractController
{

    #[Route("/connect/twitter", name:"twitter_connect")]
    public function connectTwitter(TwitterConnectionService $twitterConnectionService): RedirectResponse
    {
        $url = $twitterConnectionService->getConnectionUrl();

        return $this->redirect($url);
    }

    #[Route("/connect/twitter/callback", name:"twitter_callback")]
    public function twitterCallback(Request $request, TwitterConnectionService $twitterConnectionService): Response
    {

        $oauthToken = $request->query->get('oauth_token');
        $oauthVerifier = $request->query->get('oauth_verifier');

        $twitterConnectionService->handleCallback($oauthToken, $oauthVerifier);

        // Redirect to the user's profile page or any other page
        return $this->redirectToRoute('user_profile');
    }
}

