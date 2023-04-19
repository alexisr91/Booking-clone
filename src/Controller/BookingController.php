<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * Permet d'afficher le formulaire de reservation 
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     * @param Ad $ad
     * @return Response 
     */
    public function book(Ad $ad,Request $request,EntityManagerInterface $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class,$booking);

        $form->handleRequest($request);
        
        dump($booking);
        if($form->isSubmitted() && $form->isValid()){

            $user = $this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad);

            // Si les dates ne sont pas dispo

            if(!$booking->isBookable()){

                $this->addFlash("warning","Ces dates ne sont pas disponibles choisissez d'autres dates pour votre séjour");
            }
            
            else{

                $manager->persist($booking);
                $manager->flush();
                
                return $this->redirectToRoute("booking_show",['id'=>$booking->getId(),'alert'=>true]);
            }


        }

        return $this->render('booking/book.html.twig', [
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);
    }


    /**
     * Affiche une reservation
     * @Route("/booking/{id}",name="booking_show")
     * @param Booking $booking
     * @return Response
     */


    public function show(Booking $booking){

        return $this->render("booking/show.html.twig",['booking'=>$booking]);

    }
}
