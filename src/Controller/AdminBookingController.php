<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\Pagination;
use App\Form\AdminBookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Call for the CSRF Token 
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException; // For the CSRF attack 


// CRUD de l'admin sur les reservations des utilisateurs 

class AdminBookingController extends AbstractController
{
    /**
     * Affiche la liste des réservations 
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_list")
     * @return Response
     */
    public function index( Pagination $paginationService,$page): Response
    {
        $paginationService->setEntityClass(Booking::class)
                          ->setPage($page)
                          // ->setRoute('admin_bookings_list')
                          ;
                          
        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $paginationService

        ]);
    }

    

    /**
     * Edition d'une reservation
     * 
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit")
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * Ce qui passe dans le paramètre de la méthode est l'entité Booking, c'est ce qui fait le lien entre le Controller et le Model
     */
    
    public function edit(Booking $booking, EntityManagerInterface $manager ,Request $request){
    
        $form = $this->createForm(AdminBookingType::class,$booking,['validation_groups'=>['default'] ]); // Application des contraintes dans le defaut
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash("success","La reservation a bien ete modifiée");
        }

        return $this->render('admin/booking/edit.html.twig',[
            'booking'=>$booking,
            'form'=>$form->createView()]);
    }


    /**
     * Supression d'une reservation / Token CSRF implemented
     * @Route("/admin/booking/{id}/delete", name="admin_booking_delete")
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     */

    public function delete(Booking $booking,EntityManagerInterface $manager, Request $request){

        $token = $request->request->get('token');

        if(
            $this->isCsrfTokenValid(
                'delete' . $booking->getId(),
                $token
        )){
            $manager->remove($booking);
            $manager->flush();
            $this->addFlash("success","La reservation {$booking->getId()}a été supprimée avec succès");
            return $this->redirectToRoute('admin_bookings_list');
        }else{
            throw new BadRequestHttpException("REQUETE EGALEMENT INTERDITE !");
        }
    }


}
