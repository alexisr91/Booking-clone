<?php


// Partie BDD 

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {   
        $faker = Factory::create('FR-fr');

        for($i=1;$i<=30;$i++){
        $ad = new Ad();

        $title = $faker->sentence();
        $coverImage = "https://picsum.photos/id/".mt_rand(30,1000)."/1000/350";
        $introduction = $faker->paragraph(2);
        $content = "<p>".join("</p><p>",$faker->paragraphs(5)). "</p>";

        $ad->setTitle($title) // $ad est une entité ( objet avec des attributs aves des getters et setters )
           ->setCoverImage($coverImage)
           ->setIntroduction($introduction)
           ->setContent($content)
           ->setPrice(mt_rand(30,200))
           ->setRooms(mt_rand(1,5))
           ;

        $manager ->persist($ad); // méthode qui prépare une entité pour la création. C'est l'étape qui va dire "Cette entité va être liée à quelque chose en base".

        for($j=1;$j<=mt_rand(2,5);$j++){

            // On crée une nouvelle instance de l'entité image
            $image = new Image();
            $image->setUrl("https://picsum.photos/id/".mt_rand(30,1000)."/640/480")
                  ->setCaption($faker->sentence())
                  ->setAd($ad)
                  ;
            
            // On sauvegarde
            $manager->persist($image);

             }
        }
        $manager->flush(); // méthode qui va envoyer les informations en base de données. C'est donc la méthode à n
    }
}
// Si on refait une insertion, la donnée de l'id 1 prendra l'id 2 et ainsi de suite puisque l'on purge la db et donc l'ID fera à chaque load un +1 par rapport à la dernière donnée ( id : 32 ) 
