<?php

namespace App\Service;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

class CommentService
{

    public function __construct(
        private EntityManagerInterface $em,
        private Environment $environment,
        private ParameterBagInterface $parameters
    ) {}


    public function handleCommentFormData(FormInterface $commentForm): JsonResponse
    {
        if ($commentForm->isValid()) {
            return $this->handleValidForm($commentForm);
        } else {
            return $this->handleInvalidForm($commentForm);
        }
    }


    private function handleValidForm(FormInterface $commentForm) : JsonResponse
    {
        /** @var Comment $comment */
        $comment = $commentForm->getData();


        $this->em->persist($comment);
        $this->em->flush();

        return new JsonResponse([
            'code' => Comment::COMMENT_ADDED_SUCCESSFULLY,
            'html' => $this->environment->render('comment/_comment.html.twig', [
                'comment' => $comment
            ]),
            'idComment' => $comment->getId()
        ]);
    }


    private function handleInvalidForm(FormInterface $commentForm) : JsonResponse
    {
        return new JsonResponse([
            'code' => Comment::COMMENT_ERROR,
        ]);
    }
}
