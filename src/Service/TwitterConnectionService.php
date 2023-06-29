<?php
// src/Service/TwitterConnectionService.php

namespace App\Service;

use App\Entity\User;
use Abraham\TwitterOAuth\TwitterOAuth;
use Doctrine\ORM\EntityManagerInterface;

class TwitterConnectionService
{
    private $apiKey;
    private $apiSecret;
    private $accessToken;
    private $accessTokenSecret;
    private $em;

    public function __construct(string $apiKey, string $apiSecret, string $accessToken, string $accessTokenSecret , EntityManagerInterface $em)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->accessToken = $accessToken;
        $this->accessTokenSecret = $accessTokenSecret;
        $this->em = $em;
    }

    public function getConnectionUrl()
    {
        $connection = new TwitterOAuth(
            $this->apiKey,
            $this->apiSecret
        );

        $requestToken = $connection->oauth('oauth/request_token', ['oauth_callback' => '']);

        $url = $connection->url('oauth/authorize', ['oauth_token' => $requestToken['oauth_token']]);

        return $url;
    }

    public function handleCallback($oauthToken, $oauthVerifier)
    {
        $connection = new TwitterOAuth(
            $this->apiKey,
            $this->apiSecret,
            $oauthToken,
            $oauthVerifier
        );

        $accessToken = $connection->oauth('oauth/access_token', ['oauth_verifier' => $oauthVerifier]);




    }
}
