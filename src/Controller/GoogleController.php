<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // on Symfony 3.3 or lower, $clientRegistry = $this->get('knpu.oauth2.registry');

        // will redirect to google!
        return $clientRegistry
            ->getClient('google') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }


    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)

        $client = $clientRegistry->getClient('google');

        try {
            // the exact class depends on which provider you're using
            $user = $client->fetchUser();

            // do something with all this new power!
            // $name = $user->getName();


            var_dump($user);
            // ...
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            var_dump($e->getMessage()); die;
        }
    }
}
