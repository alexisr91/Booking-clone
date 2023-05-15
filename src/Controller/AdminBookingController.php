<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Affiche la liste des réservations 
     * @Route("/admin/bookings", name="admin_bookings_list")
     * @return Response
     */
    public function index(BookingRepository $repo): Response
    {
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $repo->findAll()
        ]);
    }

    

    /**
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit")
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    
    public function edit(Booking $booking, EntityManagerInterface $manager ,Request $request, BookingRepository $repo){
    
        $form = $this->createForm(AdminBookingType::class,$booking,['validation_groups'=>['default'] ]); // Application des contraintes dans le defaut
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //$booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration());

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
     * Supression d'une reservation
     * @Route("/admin/booking/{id}/delete", name="admin_booking_delete")
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     */

    public function delete(Booking $booking,EntityManagerInterface $manager){

        $manager->remove($booking);
        $manager->flush();

        $this->addFlash("success","reservation supprimée avec succès");

        return $this->redirectToRoute('admin_bookings_list');
    }


}
