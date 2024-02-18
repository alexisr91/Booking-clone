<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends AbstractType
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
        $builder
        ->add('firstname',TextType::class,$this->getConfiguration("Nom", "Votre nom..."))
        ->add('lastname',TextType::class,$this->getConfiguration("Prénom", "Votre prenom..."))
        ->add('email',EmailType::class,$this->getConfiguration("Email","Un email valide"))
        ->add('password',PasswordType::class,$this->getConfiguration("Mot de passe","Choisissez un bon mot de passe "))
        ->add('passwordConfirm',PasswordType::class,$this->getConfiguration("Confirmation mot de passe","Confirmez votre mot de passe"))
        ->add('avatar',UrlType::class,$this->getConfiguration("Avatar","Url de votre avatar"))
        ->add('Introduction',TextType::class,$this->getConfiguration("Introduction","Description courte pour vous presenter"))
        ->add('description',TextareaType::class,$this->getConfiguration("Description","Description détaillée"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
