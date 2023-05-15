<?php

namespace App\Controller;


use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Comment;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="admin_comment_list")
     */
    public function index(CommentRepository $repo): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $repo->findAll()
        ]);
    }


    /**
     * Supression d'un commentaire
     * @Route("/admin/comment/{id}/delete",name="admin_comment_delete")
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */

    public function delete(Comment $comment,EntityManagerInterface $manager){

        $manager->remove($comment);
        $manager->flush();
        $this->addFlash('success', "Le commentaire{$comment->getId()} a bien été supprimé !!");

        return $this->redirectToRoute('admin_comment_list');
    }   
}
