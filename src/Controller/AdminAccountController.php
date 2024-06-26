<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


// AUTHENTIFICATION pour l'admin
class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login",name="admin_account_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('admin/account/login.html.twig', [
            
            'hasError'=>$error !==null,
            'username'=>$username
        ]);
    }

    /**
     * Deconnexion de la partie admin
     * @Route("/admin/logout",name="admin_account_logout")
     * @return void
     */
    public function logout(){
        // Le fichier securitt.yaml se charge de la déconnexion donc pas besoin de bloc d'instruction
    }
}
