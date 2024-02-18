<?php 
// Compiled language is faster than interpreted language

// namespace : controller path
namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


// Controleur de la page d'accueil où on va renvoyer les datas de la bdd et parametrer les routes 
// Pour créer une page : 1- Une fonction publique ( classe ) / 2- une route / 3- Une réponse 

class HomeController extends Controller{
 
    /**
     *  1st route of our page
     *  @Route ("/", name="homepage")
     */

    public function home(AdRepository $adRepo, UserRepository $userRepo){

        // render is a method that return to the view what is passed in the parameters
        return $this->render('home.html.twig',
                             ['ads'=>$adRepo->findBestAds(6),
                             'users'=>$userRepo->findBestUsers(4)
                            ]);
    }

    /**
     * Show the page that says hi to the user
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
