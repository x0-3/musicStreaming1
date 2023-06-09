<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\User;
use App\Entity\Album;
use App\Entity\Subscribe;
use App\Form\EditUserType;
use App\Form\SubscribeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    // with subscription form
    #[Route('/artist/{id}', name: 'app_artistDetail')]
    public function artistPage($id, EntityManagerInterface $em, Request $request): Response
    {

        $artist = $em->getRepository(User::class)->findOneBy(['username'=> $id]);

        $user = $this->getUser();

        // page without subscriptions functionality
        $songs = $em->getRepository(Song::class)->findByArtistMostLike($artist); //find the artist's most like songs
        $albums = $em->getRepository(Album::class)->findByMostRecentAlbumArtist($artist); //find the artist's most recent albums
        $artistMostSub = $em->getRepository(Subscribe::class)->find4ByMostSubscribers(); //find the artist's with the most subscribers 

        
        // if the user is the artist then redirect to the profile page
        if ($user == $artist) {
            return $this->redirectToRoute('app_profile');
        }

        
        // find if the user is subscribed to the artist
        $userSub = $em->getRepository(Subscribe::class)->findOneBy([
            'user1' => $user,
            'user2' => $artist,
        ]);

        
        //* ********************************************* add subscription ****************************************************************************** *//
        // add the user to the artist subscriptions
        $subscribe = new Subscribe();
        
        $form = $this->createForm(SubscribeType::class, $subscribe);
        $form->handleRequest($request);
        
        if ($userSub == null) {
            if ($form->isSubmitted() && $form->isValid()) {

                $subscribe->setDateFollow(new \DateTime());
                $subscribe->setUser1($user);
                $subscribe->setUser2($artist);

                $em->persist($subscribe);
                $em->flush();

                return $this->redirectToRoute('app_artistDetail', ['id' => $id]);

            }
            
        }

        return $this->render('user/artistDetail.html.twig', [
            'form' => $form->createView(),
            'artist' => $artist,
            'songs' => $songs,
            'albums' => $albums,
            'artistMostSub' => $artistMostSub,
            'userSub'=> $userSub,
        ]);
    }


    // find all of the artist albums ordered by most recent 
    #[Route('/artist/{id}/album', name: 'app_artistAlbum')]
    public function artistAlbum(User $artist, EntityManagerInterface $em ): Response
    {

        $albums = $em->getRepository(Album::class)->findByMostRecentAlbumArtist($artist); //find the artist's most recent albums

        return $this->render('user/moreArtistAlbum.html.twig', [
            'albums' => $albums,
            'artist' => $artist,
        ]);
    }


    // ************************************************* profil page ********************************************************** //
    #[Route('/profile', name: 'app_profile')]
    public function profilPage(EntityManagerInterface $em): Response
    {
        
        $user =  $this->getUser();

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


    // edit the user profile
    #[Route('/user/edit', name: 'edit_profile')]
    public function editUser(Request $request,EntityManagerInterface $em): Response
    {

        $user = $this->getUser(); // get the current user

        // if the user is logged in
        if ($user) {

            $editUserForm = $this->createForm(EditUserType::class, $user);

            $editUserForm->handleRequest($request);
            if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {

                $user = $editUserForm->getData();

                $em->persist($user);
                $em->flush();


                return $this->redirectToRoute('app_profile');
            }

            return $this->render('user/editUser.html.twig', [
                'editUserForm' => $editUserForm,
            ]);
        }

        return $this->redirectToRoute('app_home');

    }


    // delete the user profile
    #[Route('/user/delete', name: 'delete_profile')]
    public function deleteUser(EntityManagerInterface $em): Response
    {

        $session = new Session();

        $user = $this->getUser(); // get the current user

        // if the user is logged in
        if ($user) {

            $em->remove($user);
            $em->flush();

            $session->invalidate(); // remove the user from the session (invalidate() removes everything that is inside the session)

        }
        return $this->redirectToRoute('app_logout');
        
    }
}
