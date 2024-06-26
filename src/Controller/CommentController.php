<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{ 
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {

        return $this->render('comment/index.html.twig', [
            'CommentController' => 'CommentController',
            'description' => ''
        ]);
        
        
    }


    // delete comment
    #[Route('/comment/delete/{id}', name: 'delete_comment')]
    public function delete(EntityManagerInterface $em, Comment $comment, Security $security): JsonResponse
    {

        $user =  $security->getUser(); // get the user in session

        $commentOwner = $comment->getUser(); // owner of the comment

        // if the user is equal to the comment owner then delete
        if ($commentOwner === $user){ 

            $idComment = $comment->getId();
            
            $em->remove($comment);
            $em->flush();
   
            return new JsonResponse([
                'code' => Comment::COMMENT_DELETED_SUCCESSFULLY,
                'id' => $idComment
            ]);

            
        }
        return new JsonResponse([
            'code' => 'COMMENT_ERROR',
        ]);
      
    }

}
