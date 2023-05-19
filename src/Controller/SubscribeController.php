<?php

namespace App\Controller;

use App\Entity\Subscribe;
use App\Entity\User;
use App\Form\SubscribeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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


    // find all of the artist that has the most subscriptions
    #[Route('/subscribe/similarArtists', name: 'app_similarArtist')]
    public function mostPopularArtist(EntityManagerInterface $em ): Response
    {

        $artistMostSub = $em->getRepository(Subscribe::class)->findByMostSubscribers(); //find the artist's with the most subscribers 

        return $this->render('subscribe/mostSubscribers.html.twig', [
            'artistMostSub' => $artistMostSub,
            
        ]);
    }


    // // subscribe to the artist
    // // TODO: add the unsub if the user is already subscribed to the artist
    // #[Route('/subscribeTo/{id}', name: 'app_subscribeTo')]
    // public function subscribe(Request $request, EntityManagerInterface $em, User $artist, RequestStack $requestStack): Response
    // {

    //     $user = $this->getUser(); // get the user

    //     if ($user) {
    //         $subscribe = new Subscribe();

    //         $form = $this->createForm(SubscribeType::class, $subscribe);

    //         $subscribe->setDateFollow(new \DateTime());
    //         $subscribe->setUser1($user);
    //         $subscribe->setUser2($artist);

    //         $form->handleRequest($request);

    //         if ($form->isSubmitted() && $form->isValid()) {

    //             $subscribe = $form->getData();

    //             // ... perform some action, such as saving the task to the database
    //             $em->persist($subscribe);

    //             // actually executes the queries (i.e. the INSERT query)
    //             $em->flush();

    //             return $this->redirectToRoute('app_artistDetail', ['id' => $artist->getId()]);
    //         }
    //         return $this->render('subscribe/addSub.html.twig', [
    //             'form' => $form,
    //             'artist' => $artist,
    //         ]);
    //     }  
    
    // } 

    #[Route('/subscribeTo/{id}', name: 'app_subscribeTo')]
    public function subscribe(Request $request, EntityManagerInterface $em, User $artist): Response
    {
        $user = $this->getUser();
    
        if ($user) {        
    
            // find if the user is already subscribed to this artist
            $userSub = $em->getRepository(Subscribe::class)->findOneBy([
                'user1' => $user,
                'user2' => $artist,
            ]);
        
            
            // if the user is already subscribed
            if ($userSub) {

                $em->remove($userSub); // unsubscribe the user
                $em->flush();
        
                // redirect to the artist page
                return $this->redirectToRoute('app_artistDetail', ['id' => $artist->getId()]);
            }
            
            // else if the user is not subscribed then

            // add the user to the artist subscriptions
            $subscribe = new Subscribe();
            
            $form = $this->createForm(SubscribeType::class, $subscribe);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {

                $subscribe->setDateFollow(new \DateTime());
                $subscribe->setUser1($user);
                $subscribe->setUser2($artist);

                $em->persist($subscribe);
                $em->flush();
        
                return $this->redirectToRoute('app_artistDetail', ['id' => $artist->getId()]);                
            }

            return $this->render('subscribe/addSub.html.twig', [
                'form' => $form->createView(),
                'artist' => $artist,
            ]);

        }

        // if user is not connected then redirect to the login page
        return $this->redirectToRoute('app_login');

    }
    
}
