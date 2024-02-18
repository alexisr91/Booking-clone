<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Call for the CSRF Token 
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException; // For the CSRF attack 


// Crud de l'admin sur l'utilisateur et pagination

class AdminCommentController extends AbstractController
{
    /**
     * Call of the pagination service and its methods
     * @Route("/admin/comments", name="admin_comment_list")
     */
    public function index($page=1,Pagination $paginationService): Response
    {   
        $paginationService->setEntityClass(Comment::class)
                          ->setLimit(5)
                          ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $paginationService,
           
        ]);
    }


    /**
     * Delete a comment user / Token CSRF implemented
     * @Route("/admin/comment/{id}/delete",name="admin_comment_delete")
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */

    
    public function delete(Comment $comment,EntityManagerInterface $manager, Request $request){

        $token = $request->request->get('token');
        
        // CSRF token's validation
        // Il faut recuperer par la requete le mot token et non par le underscore token ( uniquement preconfiguré par Symfony ) quand le token est personnalisable, on met uniquement token
        if(
            $this->isCsrfTokenValid(
            'delete' . $comment->getId(),
            $token
        )){
            $manager->remove($comment);
            $manager->flush();
            $this->addFlash('success', "<div class='text-center'>Le commentaire{$comment->getId()} a bien été supprimé</div>");
            return $this->redirectToRoute('admin_comment_list');
                    
        }else{
            throw new BadRequestHttpException("REQUETE INTERDITE !");
        }
        
    }   
}
