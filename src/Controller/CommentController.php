<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Song;
use App\Form\CommentType;
use App\Service\CommentService;
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
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {

        return $this->render('comment/index.html.twig', [
            'CommentController' => 'CommentController',
        ]);
        
        
    }


    // FIXME: redirection issue
    // delete comment
    #[Route('/comment/delete/{id}', name: 'delete_comment')]
    public function delete(EntityManagerInterface $em, Comment $comment, Security $security, Environment $environment, Request $request): JsonResponse
    {

        $user =  $security->getUser(); // get the user in session        

        $commentOwner = $comment->getUser(); // owner of the playlist

        // if the user is equal to the playlist owner then delete
        if ($commentOwner === $user){ 
            
            $em->remove($comment);
            $em->flush();

            
            return new JsonResponse([
                'code' => 'COMMENT_DELETED_SUCCESSFULLY',
            ]);

        }
      
        return $this->redirectToRoute('app_home');

    }

}
