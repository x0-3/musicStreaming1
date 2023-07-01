<?php
// src/Controller/TwitterController.php

namespace App\Controller;

use App\Entity\User;
use Abraham\TwitterOAuth\TwitterOAuth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TwitterController extends AbstractController
{

    #[Route("/twitter/auth", name: "twitter_auth")]
    public function auth(Request $request)
    {
        // Step 1: Initialize the TwitterOAuth library
        $twitterOAuth = new TwitterOAuth(
            'k27Mjqjj0RSSsYhOspIPEtmBF',
            'SxUzefzl9TSZ01QTtEsCDXjqHAEGQA6PxGLlO0Z2mGG2Nz6zW2'
        );

        // Step 2: Get the temporary request token
        $requestToken = $twitterOAuth->oauth('oauth/request_token', [
            'oauth_callback' => 'https://f587-2a01-e0a-903-9dc0-f0c2-ead7-d653-68ba.ngrok-free.app/twitter/callback'
        ]);

        // Step 3: Store the request token in the session
        $request->getSession()->set('oauth_token', $requestToken['oauth_token']);
        $request->getSession()->set('oauth_token_secret', $requestToken['oauth_token_secret']);

        // Step 4: Generate the authorization URL and redirect the user
        $url = $twitterOAuth->url(
            'oauth/authorize',
            ['oauth_token' => $requestToken['oauth_token']]
        );

        return new RedirectResponse($url);
    }



    #[Route("/twitter/callback", name: "twitter_callback")]
    public function callback(Request $request, EntityManagerInterface $entityManager)
    {
        // Step 5: Retrieve the request token and verifier from the callback
        $oauthToken = $request->getSession()->get('oauth_token');
        $oauthTokenSecret = $request->getSession()->get('oauth_token_secret');
        $verifier = $request->query->get('oauth_verifier');


        // Step 6: Create a new TwitterOAuth instance with the stored tokens
        $twitterOAuth = new TwitterOAuth(
            'k27Mjqjj0RSSsYhOspIPEtmBF',
            'SxUzefzl9TSZ01QTtEsCDXjqHAEGQA6PxGLlO0Z2mGG2Nz6zW2',
            $oauthToken,
            $oauthTokenSecret
        );

        // Step 7: Get the access token using the verifier
        $accessToken = $twitterOAuth->oauth(
            'oauth/access_token',
            array('oauth_verifier' => $verifier)
        );

        // Step 8: Store the user's screen_name in the database
        $twitterUsername = $accessToken['screen_name'];

        
        $user = $this->getUser();

        if ($user instanceof User) {

            $user->setTwitterId($twitterUsername);
            $entityManager->persist($user);
            $entityManager->flush();

        }
        // You can perform additional actions with the access token, such as retrieving user data or making API calls


        // Step 9: Redirect the user to a success page or perform any other necessary actions
        return $this->redirectToRoute('app_home');
    }
}
