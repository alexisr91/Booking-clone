<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    /**
     * 
     * Permet d'avoir la configurtion de base d'un champ
     * 
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */

     protected function getConfiguration($label,$placeholder,$options=[]){
        return array_merge_recursive([
                            'label'=>$label,
                            'attr'=>['placeholder'=>$placeholder]                      
                             ],
                            $options);
     }
}
