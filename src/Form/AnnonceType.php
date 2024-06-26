<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\ImageType;


class AnnonceType extends AbstractType
{
    /**
     *  Permet d'avoir la configuration de base d'un champ
     * 
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     * 
     */
    private function getConfiguration($label,$placeholder,$options=[]){

        return array_merge([
                            'label'=>$label,
                            'attr'=>['placeholder'=>$placeholder]
                            ],
                            $options);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Entité = Objet que l'on crée, il sert de mapping 

        $builder
            ->add('title',TextType::class,$this->getConfiguration('Titre','insérer un titre '))
            ->add('slug',TextType::class,$this->getConfiguration('Alias','personnalisez un alias pour générer l\'url ',['required'=>false]))
            ->add('coverImage',FileType::class,$this->getConfiguration('Image de couverture','Insérez une image'))
            ->add('introduction',TextType::class,$this->getConfiguration('Résumé','Présentez votre bien'))
            ->add('content',TextareaType::class,$this->getConfiguration('Description détaillé','Décrivez vos services'))
            ->add('rooms',IntegerType::class,$this->getConfiguration('Nombre de chambres','Nombre de chambres'))
            ->add('price',MoneyType::class,$this->getConfiguration('Prix','Prix des chambres/nuit'))
            ->add('images',CollectionType::class,['entry_type'=>ImageType::class,'allow_add'=>true,'allow_delete'=>true,'allow_delete'=>true]) // Autorise l'ajout et la suppression de nouvelles entrées images 
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'ad' => Ad::class,
        ]);
    }
}
