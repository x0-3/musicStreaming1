<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Song;
use App\Entity\Subscribe;
use App\Entity\User;
use App\Form\SubscribeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    // page for the detail of another user  
    #[Route('/artist/{id}', name: 'app_artistDetail')]
    public function artistPage(User $artist, EntityManagerInterface $em, Request $request): Response
    {

        // TODO: put it in a service

        /* ***************************************** subscribe to the artist form ******************************************************** */
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
        
            }

        }
        /* ****************************************************************************************************** */


        $songs = $em->getRepository(Song::class)->findByArtistMostLike($artist); //find the artist's most like songs
        $albums = $em->getRepository(Album::class)->findByMostRecentAlbumArtist($artist); //find the artist's most recent albums
        $artistMostSub = $em->getRepository(Subscribe::class)->find4ByMostSubscribers(); //find the artist's with the most subscribers 

        return $this->render('user/artistDetail.html.twig', [
            'artist' => $artist,
            'songs' => $songs,
            'albums' => $albums,
            'artistMostSub' => $artistMostSub,
            'form' => $form->createView(),

        ]);
    }


    // find all of the artist albums ordered by most recent 
    #[Route('/artist/{id}/album', name: 'app_artistAlbum')]
    public function artistAlbum(User $artist, EntityManagerInterface $em ): Response
    {

        $albums = $em->getRepository(Album::class)->findByMostRecentAlbumArtist($artist); //find the artist's most recent albums

        return $this->render('user/moreArtistAlbum.html.twig', [
            'albums' => $albums,
        ]);
    }


    // ************************************************* profil page ********************************************************** //
    #[Route('/profile', name: 'app_profile')]
    public function profilPage(EntityManagerInterface $em, Security $security): Response
    {
        
        $user =  $security->getUser();

        if ($user) {
            
            $albums = $em->getRepository(Album::class)->findBy(['user' => $user],['releaseDate'=>'DESC'], 5);

            return $this->render('user/profile.html.twig', [
                'user' => $user,
                'albums' => $albums,
            ]);

        } else {

            return $this->redirectToRoute('app_login');

        }

    }
}
