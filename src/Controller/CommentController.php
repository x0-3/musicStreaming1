<?php

namespace App\Controller;

use App\Entity\Song;
use Twig\Environment;
use App\Entity\Comment;
use App\Entity\Playlist;
use App\Form\CommentType;
use App\Service\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

        $commentOwner = $comment->getUser(); // owner of the comment

        // if the user is equal to the comment owner then delete
        if ($commentOwner === $user){ 

            $idComment = $comment->getId();
            
            $em->remove($comment);
            $em->flush();
   
            return new JsonResponse([
                'code' => Comment::COMMENT_DELETED_SUCCESSFULLY,
                // 'html' => $environment->render('comment/_comment.html.twig', [
                //     'comment' => $comment,
                //     'id' => $comment->getId()
                // ])
                'id' => $idComment
            ]);

            // return $this->render('playlist/playlistPlayer.html.twig', [
            //     'code' => 'COMMENT_DELETED_SUCCESSFULLY',
            //     'html' => $environment->render('comment/_comment.html.twig', [
            //         'comment' => $comment,
            //         'id' => $comment->getId(),
            //         'playlist' => $playlist
            //     ])
            // ]);
            
        }
        return $this->redirectToRoute('app_home');
        // return new JsonResponse([
        //     'code' => 'COMMENT_ERROR',
        // ]);
      
    }

}
