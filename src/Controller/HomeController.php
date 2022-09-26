<?php 

// namespace : chemin du controller

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


// Pour créer une page : 1- Une fonction publique ( classe ) / 2- une route / 3- Une réponse 

class HomeController extends Controller{
 
    /**
     *  Création de notre 1er route
     *  @Route ("/", name="homepage")
     * 
     *  
     */

    public function home(){

        $noms = ['Durand'=>'visiteur','Francois'=>'admin','Dupont'=>'contributeur'];

        // render est une méthode de la classe controller
        return $this->render('home.html.twig',['titre'=>'Site d\'annonces !','acces'=>'visiteur','tableau'=>$noms]);
    }

    /**
     * Montre la page qui salue l'utilisateur
     * 
     * @Route("/hello/{nom}",name="hello")
     * @Route("/profil",name="hello-base")
     * @Route("/profil/{nom}/acces/{acces}", name="hello-profil")
     * @return void
     */

    public function hello($nom="anonyme",$acces="visiteur"){

        return $this->render('hello.html.twig',['title'=>'Page de profil','nom'=>$nom,'acces'=>$acces]);
    }
}
