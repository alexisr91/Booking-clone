<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher une page de connexion
     * @Route("/login",name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();

        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' =>$error!==null,
            'username'=>$username,
        ]);

    }


    /**
     * Permet de deconnecter
     * @Route("/logout",name="account_logout")
     * @return void
     */
   
    public function logout(){
        // besoin de rien puisque tout se passe via le fichier security.yaml
    }


    /**
     * Permet d'afficher une page s'inscrire 
     * @Route("/register",name="account_register")
     * 
     * @return Response
     */

    public function register(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager){

        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $hash = $encoder->encodePassword($user, $user->getPassword());

            // On modifie le mdp avec le setter

             $user->setPassword($hash);

             $manager->persist($user);
             $manager->flush();

             $this->addFlash("success","Votre compte a bien été crée");

             return $this->redirectToRoute("account_login");


        }

        return $this->render("account/register.html.twig",[
            'form'=>$form->createView()
        ]);
    }

    /**
     * Modification du profil utilisateur
     * 
     * @Route("/account/profile",name="account_profile")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request,EntityManagerInterface $manager){

        $user = $this->getUser();

        $form = $this->createForm(AccountType::class,$user);
        $form->handlerequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $manager->persist($user);

            $manager->flush();

            $this->addFlash("success","les informations de votre profil ont bien été modifiées ");
        }

        return $this->render('account/profile.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * Permet la modification du mot de passe
     * @Route("/account/password-update",name="account_password")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function updatePassword(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager){

        
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        

        $form=$this->createForm(PasswordUpdateType::class,$passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // Mot de passe actuel n'est pas le bon 
            if(!$encoder->isPasswordValid($user,$passwordUpdate->getOldPassword())){
                // Message d'erreur
                // $this->addFlash("warning","Votre mot de passe actuel est incorrect");

                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez entré n'est pas votre mot de passe actuel"));

            }else{
            // On récupere le nouveau mot de passe 
            $newPassword = $passwordUpdate->getNewPassword();
            // On crypte le nouveau mot de passe 
            $hash = $encoder->encodePassword($user,$newPassword);
            // On modifie ensuite le nouveau mdp dans le setter 
            $user->setPassword($hash);
            // On enregistre 
            $manager->persist($user);
            
            $manager->flush();
            


            // On ajoute un message 
            $this->addFlash("success","Votre nouveau mot de passe a été enregistré ");

            // On redirige 

            return $this->redirectToRoute('account_profile');
        }
     }
        return $this->render('account/password.html.twig',[
            'form'=>$form->createView()
        ]);
        }


        /**
         * Permet d'afficher ma page mon compte 
         * @Route("/account", name="account_home")
         * 
         * 
         * @return Response
         */
        public function myAccount(){

            return $this->render("user/index.html.twig",['user'=>$this->getUser()]);
         }


        /**
         * Affiche la liste des réserfvations de l'utilisateur 
         * @Route("/account/bookings", name="account_bookings")
         * 
         * @return Response
         */
        public function bookings(){

            return $this->render('account/bookings.html.twig');
        } 
}
