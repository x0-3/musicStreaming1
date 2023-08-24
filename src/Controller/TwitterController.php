<?php

namespace App\Controller;

use App\Entity\User;
use Abraham\TwitterOAuth\TwitterOAuth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TwitterController extends AbstractController
{

    #[Route("/twitter/auth", name: "twitter_auth")]
    public function auth(Request $request)
    {        
        // intitialize the authentication key and secret
        $twitterOAuth = new TwitterOAuth(
            $apiKey = $this->getParameter('twitter_api_key'), // get the API key from the .env variable
            $apiSecret = $this->getParameter('twitter_api_secret'),
            $callbackUrl = $this->getParameter('CALLBACK_URL'),
        );

        // Get the temporary request token
        $requestToken = $twitterOAuth->oauth('oauth/request_token', [
            'oauth_callback' => $callbackUrl
        ]);

        // Store the request token in the session
        $request->getSession()->set('oauth_token', $requestToken['oauth_token']);
        $request->getSession()->set('oauth_token_secret', $requestToken['oauth_token_secret']);

        // Generate the authorization URL 
        $url = $twitterOAuth->url(
            'oauth/authorize',
            ['oauth_token' => $requestToken['oauth_token']]
        );

        // redirect the user
        return new RedirectResponse($url);
    }



    #[Route("/twitter/callback", name: "twitter_callback")]
    public function callback(Request $request, EntityManagerInterface $entityManager)
    {
        // get the oauthToken and oauthTokenSecret from the session and get verifier from the callback URL
        $oauthToken = $request->getSession()->get('oauth_token');
        $oauthTokenSecret = $request->getSession()->get('oauth_token_secret');
        $verifier = $request->query->get('oauth_verifier');


        // Create a new TwitterOAuth instance with the stored tokens
        $twitterOAuth = new TwitterOAuth(
            $apiKey = $this->getParameter('twitter_api_key'),
            $apiSecret = $this->getParameter('twitter_api_secret'),
            $oauthToken,
            $oauthTokenSecret
        );

        //  Get the access token using the verifier
        $accessToken = $twitterOAuth->oauth(
            'oauth/access_token',
            array('oauth_verifier' => $verifier)
        );

        // Store the user's twitter username in the database
        $twitterUsername = $accessToken['screen_name'];

        // get the user that has connected with the Twitter account
        $user = $this->getUser();

        if ($user instanceof User) {

            // add the twitter username to the database
            $user->setTwitterId($twitterUsername);
            $entityManager->persist($user);
            $entityManager->flush();

        }


        // Redirect the user to the home page
        return $this->redirectToRoute('app_home');
    }


    // disconnect from twitter
    #[Route("/twitter/disconnect", name: "twitter_disconnect")]
    public function disconnect(EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        if($user instanceof User){

            // set the twitter id to null
            $user->setTwitterId('');
            $em->flush();

        }

        return $this->redirectToRoute('app_profile');
    }
}
