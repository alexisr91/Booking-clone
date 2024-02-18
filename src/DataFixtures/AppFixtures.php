<?php


// Partie BDD, jeu de fausses données / Executable invisible qui est un service à part

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use DateTimeImmutable;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


// Datafixture ( Picsum )
// Encodage du password
// Création d'un role admin 
class AppFixtures extends Fixture
{   
    private $passwordEncoder;
 
    public function __construct(UserPasswordEncoderInterface $passwordEncoder){ // encoder le mdp
        
        $this->passwordEncoder = $passwordEncoder; 
 
    }



    public static function slugify($text, string $divider = '-'){
 
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text); // Met les tirets
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text); // Enleve les elements et ne mets rien 
    $text = trim($text, $divider);
    $text = preg_replace('~-+~', $divider, $text);
    $text = strtolower($text);
 

    if(empty($text)){
    return 'n-a';
    }   
    return $text;  
    }

    public function load(ObjectManager $manager): void
    {   
        $faker = Factory::create('FR-fr');
        // Gestion des roles 

        $adminRole = new Role(); // Instanciation de l'objet
        $adminRole->setTitle('ROLE_ADMIN'); // Super globable 
        $manager->persist($adminRole);

        // Création d'un utiilisateur spécial avec un role admin

        $adminUser = new User();

        $adminUser->setFirstname('Alexis')
                  ->setlastName('Ramboarina')
                  ->setEmail('alexis@formation31.fr')
                //   ->sethash($this->encoder->encodePassword($adminUser,'password'))
                  ->setPassword($this->passwordEncoder->encodePassword($adminUser,"password"))
                  ->setAvatar('https://randomuser.me/api/portraits/men/55.jpg')
                  ->setIntroduction($faker->sentence())
                  ->setDescription("<p>".join("</p><p>",$faker->paragraphs(5)). "</p>")
                  ->addUserRole($adminRole)
                  ;

        $manager->persist($adminUser);

        $users = [];
        $genres = ['male','female'];
        
        // Utilisateurs 
        for($i=1;$i<=10;$i++){
            
            $genre = $faker->randomElement($genres);
            $avatar = 'https://randomuser.me/api/portraits/';
            $avatarId = $faker->numberBetween(1,99). '.jpg';
            $avatar .=($genre == 'male' ?  'men/' : 'women/') . $avatarId;
            
            

            $user = new User();
            $description = "<p>".join("</p><p>",$faker->paragraphs(5)). "</p>";
            $user->setDescription($description)
                 ->setFirstname($faker->firstname)
                 ->setLastname($faker->lastname)
                 ->setemail($faker->email)
                 ->setintroduction($faker->sentence())
                 // Mise en place du mot de passe password pour tous les utilisateurs avec la classe encoderu de Symfony
                 ->setPassword($this->passwordEncoder->encodePassword($user,"password"))
                 ->setAvatar($avatar)
                 ;
                 $manager->persist($user);
                 $users[]=$user;
        }

        // ADD  

        for($i=1;$i<=5;$i++){
         $user = New User(); $a=$i*10; // Pour afficher les ID de 1 à 10 sur lorempicsum
         $user
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setEmail("email_".$i."@email.fr")
            ->setAvatar("https://picsum.photos/id/".$a."/300")
            ->setIntroduction($faker->paragraph(2))
            ->setDescription($faker->paragraph(5))
            ->setPassword($this->passwordEncoder->encodePassword($user,"password"))
            ->setSlug($this->slugify($faker->lastname.'-'.$faker->firstname))
            ;

            $manager->persist($user); // on dit au manager d'enregistrer cette donnée mais il ne l'enregistre pas mais il faut utiliser le flush et c'est le flush qui va charger en BDD 

            for($a=1;$a<=10;$a++){
                $ad = new Ad();
        
                $title = $faker->sentence();
                $coverImage = "https://picsum.photos/id/".mt_rand(30,1000)."/1000/350";
                $introduction = $faker->paragraph(2);
                $content = "<p>".join("</p><p>",$faker->paragraphs(5)). "</p>";
                $user = $users[mt_rand(0,count($users)-1)];
        
                $ad->setTitle($title) // $ad est une entité ( objet avec des attributs aves des getters et setters )
                   ->setCoverImage($coverImage)
                   ->setIntroduction($introduction)
                   ->setContent($content)
                   ->setPrice(mt_rand(30,200))
                   ->setRooms(mt_rand(1,5))
                   ->setUser($user)
                   ;
        
                $manager ->persist($ad); // méthode qui prépare une entité pour la création. C'est l'étape qui va dire "Cette entité va être liée à quelque chose en base".
        
                for($j=1;$j<=mt_rand(2,5);$j++){
        
                    // On crée une nouvelle instance de l'entité image
                    // By calling the method setURL, we will be able to generate random images from the picsum URL 
                    $image = new Image();
                    $image->setUrl("https://picsum.photos/id/".mt_rand(30,1000)."/640/480")
                          ->setCaption($faker->sentence())
                          ->setAd($ad)
                          ;
                    
                    // On sauvegarde
                    $manager->persist($image);
                     }

                    // Gestion des reservations 

                    for($k=1;$k <= mt_rand(0,5); $k++){

                        $booking = new Booking();
                        $createdAt = DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months'));
                        $startDate = $faker->dateTimeBetween('-3 months');
                        $duration = mt_rand(3,10);
                        $endDate = (clone $startDate)->modify("+ $duration days");
                        $amount = $ad->getPrice() * $duration;

                        // trouver le booker 
                        $booker = $users[mt_rand(0,count($users)-1)];
                        $comment = $faker->paragraph();

                        // Configuration de la reservation 

                        $booking->setBooker($booker)
                                ->setAd($ad)
                                ->setStartDate($startDate)
                                ->setEndDate($endDate)
                                ->setCreatedAt($createdAt)
                                ->setAmount($amount)
                                ->setComment($comment);
                                $manager->persist($booking);
                        
                        // Gestion des commentaires

                        if(mt_rand(0,1)){

                            
                            $comment = new Comment();
                            
                            $comment->setContent($faker->paragraph())
                            ->setRating(mt_rand(1,5))
                            ->setAuthor($booker)
                            ->setAd($ad)
                            ;

                            $manager->persist($comment);
                        }

                    }
                }
            }

        $manager->flush(); // méthode qui va envoyer les informations en base de données. C'est donc la méthode à n
    }
}
// Si on refait une insertion, la donnée de l'id 1 prendra l'id 2 et ainsi de suite puisque l'on purge la db et donc l'ID fera à chaque load un +1 par rapport à la dernière donnée ( id : 32 ) 
