<?php

namespace App\Controller;

use App\Entity\Subscribe;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SubscribeController extends AbstractController
{

    // get the list of artist that the user is subscribed to
    #[Route('/subscribe', name: 'app_subscribe')]
    public function index(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, Request $request, PaginatorInterface $paginator): Response
    {

        $token = $tokenStorage->getToken();

        if ($token) {
            $userId = $token->getUser()->getUserIdentifier(); // get the user email

            $subs =  // get the user subscriptions

            $subs = $paginator->paginate(
                $em->getRepository(Subscribe::class)->findUserSubscriber($userId),
                $request->query->get('page', 1),
                10
            );


            return $this->render('subscribe/ArtistSubscribe.html.twig', [
                'subs' => $subs,
                'description' => "Discover your favorite artists! Our personalized list of subscribed artists keeps you updated on the latest music, events, and news from the musicians you love. Stay in the loop and never miss a beat with our user-friendly platform. Subscribe now and immerse yourself in a world of musical bliss. Your go-to destination for curated artist content awaits!"
            ]);

        }
    }


    // find all of the artist that has the most subscriptions
    #[Route('/subscribe/similarArtists', name: 'app_similarArtist')]
    public function mostPopularArtist(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator ): Response
    {


        $artistMostSub = $paginator->paginate(
            $em->getRepository(Subscribe::class)->findByMostSubscribers(), //find the artist's with the most subscribers 
            $request->query->get('page', 1),
            8
        );

        return $this->render('subscribe/mostSubscribers.html.twig', [
            'artistMostSub' => $artistMostSub,
            'description' => "Discover the most subscribed artists of all time on our page! Explore a diverse collection of top-tier talents with a massive following. From mesmerizing musicians and captivating painters to visionary photographers and ingenious sculptors, our platform showcases the cream of the crop. Uncover the art world's finest, all in one place. Subscribe now to stay up-to-date with the creative geniuses shaping the cultural landscape today."
        ]);
    }


    // case if the user is subscribed to the artist 
    #[Route('/subscribeTo/{id}', name: 'app_subscribeTo')]
    public function deleteSubscribe(EntityManagerInterface $em, $id): Response
    {
        $artist = $em->getRepository(User::class)->findOneBy(['username'=> $id]);

        $user = $this->getUser();
    
        if ($user) {        
    
            // find if the user is already subscribed to this artist
            $userSub = $em->getRepository(Subscribe::class)->findOneBy([
                'user1' => $user,
                'user2' => $artist,
            ]);
        
            
            // if the user is already subscribed
            if ($userSub !== null) {

                $em->remove($userSub); // unsubscribe the user
                $em->flush();
        
                // redirect to the artist page
                return $this->redirectToRoute('app_artistDetail', ['id' => $id]);
            }
            
            return $this->render('subscribe/_addSub.html.twig', [
                'artist' => $artist,
                'userSub' => $userSub,
                'description' => ''
            ]);

        }

        // if user is not connected then redirect to the login page
        return $this->redirectToRoute('app_login');

    } 

    
    // release album in youre subscription page
    #[Route('/moreSubscrition', name: 'app_subscribeAlbum')]
    public function moreAlbum(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {

        $user = $this->getUser();

        if ($user) {

            // $subscriptionSongs = $em->getRepository(Subscribe::class)->findBySubscriptionSong($user, 12);

            $pagination = $paginator->paginate(
                $em->getRepository(Subscribe::class)->findBySubscriptionAlbum($user->getUserIdentifier()),
                $request->query->get('page', 1),
                10
            );


            return $this->render('subscribe/moreAlbum.html.twig',[
                'pagination' => $pagination,
                'description' => "Discover the mesmerizing beats and soul-stirring melodies of our latest release album on our subscription feed page. Immerse yourself in a musical journey like no other, featuring a diverse collection of genres and artists. Subscribe now to access this exclusive content and let the rhythm of our new album captivate your senses. Unleash the power of music and elevate your listening experience today!"
            ]);
        }

        return $this->redirectToRoute('app_home');


    }

    // release song in youre subscription page
    #[Route('/moreSongSubscrition', name: 'app_subscribeSong')]
    public function moreSong(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {

        $user = $this->getUser();

        if ($user) {


            $pagination = $paginator->paginate(
                $em->getRepository(Subscribe::class)->findBySubscriptionSong($user->getUserIdentifier()),
                $request->query->get('page', 1),
                10
            );


            return $this->render('subscribe/moreSongsSub.html.twig',[
                'pagination' => $pagination,
                'description' => "Discover the latest and most captivating tunes on our subscription page! Unleash the power of music with our exclusive release song, carefully curated to mesmerize your senses. Join our vibrant community of music enthusiasts and stay up-to-date with the hottest tracks. Subscribe now and let the melodies take you on an unforgettable journey. Your ultimate destination for a symphony of emotions awaits."
            ]);
        }

        return $this->redirectToRoute('app_home');


    }
}
