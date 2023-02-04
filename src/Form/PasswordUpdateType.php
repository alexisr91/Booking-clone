<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class PasswordUpdateType extends AbstractType
{
    /**
     *  Permet d'avoir la configuration de base d'un champ
     * 
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     * 
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
            ->add('oldPassword',PasswordType::class,$this->getConfiguration("Ancien mot de passe","Tapez votre mot de passe actuel"))
            ->add('newPassword',PasswordType::class,$this->getConfiguration("Nouveau mot de passe","Tapez votre nouveau mot de passe "))
            ->add('confirmPassword',PasswordType::class,$this->getConfiguration("Confirmez votre mot de passe actuel","Retapez votre nouveau mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'passwordUpdate' => NULL
        ]);
    }
}
