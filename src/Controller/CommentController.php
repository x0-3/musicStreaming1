<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Song;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class CommentController extends AbstractController
{
    #[Route('/comment/{id}', name: 'app_comment')]
    public function index(EntityManagerInterface $entityManager, Security $security, Song $song, RequestStack $requestStack, Environment $environment): Response
    {

        $user = $security->getUser();
            
        if ($user) {
            
            $request = $requestStack->getMainRequest(); // get the request from the request stack

            // just set up a fresh $task object (remove the example data)
            $comment = new Comment();

            $comment->setUser($user); // set the user to connect user
            $comment->setDateMess(new \DateTime()); // set the date message to the current date
            $comment->setSong($song); // set the song id to the current song
            
            
            $form = $this->createForm(CommentType::class, $comment);
            
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                
                $comment = $form->getData();

                
                // ... perform some action, such as saving the task to the database
                $entityManager->persist($comment);

                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();

                return new JsonResponse([
                    'code'=> Comment::COMMENT_ADDED_SUCCESSFULLY,
                    'html'=> ''
        
                ]);
            }
            
            return $this->render('comment/_add.html.twig', [
                'formAddComment' => $form->createView(),
                // 'song' => $song,
            ]);
        } 

        return new JsonResponse([
            'code'=> Comment::COMMENT_ERROR,
        ]);
        
    }




    // #[Route('/comment/form/{id}', name: 'add_comment')]
    // public function addComment(Request $request, EntityManagerInterface $entityManager, Security $security, Song $song, RequestStack $requestStack)
    // {
    //     $user = $security->getUser();
    //     if ($user) {
            
    //         // just set up a fresh $task object (remove the example data)
    //         $comment = new Comment();

    //         $comment->setUser($user); // set the user to connect user
    //         $comment->setDateMess(new \DateTime()); // set the date message to the current date
    //         $comment->setSong($song); // set the song id to the current song
            
    //         $form = $this->createForm(CommentType::class, $comment);
            
    //         // FIXME: add to db but show error message
    //         $request = $requestStack->getMainRequest();
    //         $form->handleRequest($request);

    //         if ($form->isSubmitted() && $form->isValid()) {
                
    //             $comment = $form->getData();

                
    //             // ... perform some action, such as saving the task to the database
    //             $entityManager->persist($comment);

    //             // actually executes the queries (i.e. the INSERT query)
    //             $entityManager->flush();

    //             return $this->redirectToRoute('app_home');
            
    //         }
           
    //         return $this->render('comment/_add.html.twig', [
    //             'formAddComment' => $form->createView(),
    //             // 'song' => $song,
    //         ]);
    //     } 

    // }

}
