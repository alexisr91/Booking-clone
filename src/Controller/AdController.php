<?php

namespace App\Controller; // Sert à identifier 




use App\Entity\Ad;
use App\Form\AnnonceType;
use EasySlugger\Slugger; 
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

// accès des -> : pour les objets, les fonctions    / => : les tableaux 
// CRUD des annonces côté users 

class AdController extends AbstractController
{
    /**
     * Permet d'afficher une liste d'annonces 
     * @Route("/ads", name="ads_list")
     */
    public function index(AdRepository $repo){ // :Response pas utile 

        
        // Via $repo, on va aller chercher toutes les annonces via la méthode findAll

        $ads= $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Nos annonces',
            'ads'=>$ads
        ]);
    }

    
    /**
    * Permet de créer une annonce 
    * @Route("/ads/new",name="ads_create")
    * @IsGranted("ROLE_USER")
    * @return response
    */
    public function create(Request $request,EntityManagerInterface $manager){
        // fabricant de formulaire : FORMBUILDER 

        $ad = new Ad(); // On crée/instancie une annonce fantôme dont on sert comme modèle 
 
        // On lance la fabrication et la configuration de notre formulaire 
        $form = $this->createForm(AnnonceType::class,$ad);

        // Récuperation des données du formulaire 
        $form -> handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){ // booléan 

            // si le formulaire est soumis ET si le formulaire est valide, on demande à Doctrine de sauvegarder 

            // Ces données dans l'objet manager 

            // Pour chaque image supplémentaire ajoutée 

            foreach($ad->getImages() as $image){

                // On relie l'image à l'annonce et on modifie l'annonce 
                $image->setAd($ad);

                // On sauvegarde les images 
                $manager->persist($image);
            }
            
            $coverImage = $form->get('coverImage')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($coverImage){
                $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = Slugger::slugify($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $coverImage->move(
                        $this->getParameter('coverImage_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $ad->setCoverImage($newFilename);
            }

            $ad->setUser($this->getUser());
            $manager ->persist($ad); // Persist = Validation de données 
            $manager->flush(); // ça les envoie en BDD 

            $this->addFlash('success',"Annonce <strong>{$ad->getTitle()}</strong> crée avec succès");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]); // 

        }

        // Render = a quel vue est associé le controller, le 2eme parametre est le tableau  
            return $this->render('ad/new.html.twig',['form'=>$form->createView()]);
        }

    /**
     * Permet d'afficher une seule annonce
     * @Route("/ads/{slug}", name="ads_single")
     * 
     * @return Response
     */

    public function show($slug,AdRepository $repo){

        // Je récupere l'annonce qui correspond au slug ( l'alias )
        // X = 1 champ de la table, à préciser à la place de X 

        // findByX = renvoi un tableau d'annonces ( plusieurs elements )

        // findOneByX = renvoi un élément 
        $ad = $repo->findOneBySlug($slug);
        return $this->render('ad/show.html.twig',['ad'=>$ad]);
    }

    /**
     * Permet d'éditer et de modifier un article avec le rôle admin, dans cette annotation on crée également la fonction Twig  is_Granted pour determiner le role de l'admin
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getUser()", statusCode=404 ,message="Cette annonce ne vous appartient pas vous ne pouvez pas la modifier")
     * @return Response 
     */
    public function edit(Ad $ad,Request $request,EntityManagerInterface $manager){

        $form = $this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);
        

        if($form->isSubmitted() && $form->isValid()){

            foreach($ad->getImages() as $image){

                $image -> setAd($ad);

                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash("success","les modifications ont été faites !");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig',['form'=>$form->createView(),'ad'=>$ad]);
    }


    /**
     * Suppression d'une annonce
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user == ad.getUser()", message="Vous n'avez pas le droit d'accéder à cette ressource")
     * @param Ad $ad
     * @return void
     */
    public function delete(Ad $ad,EntityManagerInterface $manager){

        $manager->remove($ad);
        $manager->flush();
        $this->addFlash("Success","L'annonce <em>{$ad->getTitle()}</em> a bien été supprimée");

        return $this->redirectToRoute("ads_list");
    }
}
