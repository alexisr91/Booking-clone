<?php

namespace App\Form;

use DateTimeImmutable;
use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\FrToDatetimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends AbstractType
{   
    

    private function getConfiguration($label,$placeholder,$options=[]){

        return array_merge([
                            'label'=>$label,
                            'attr'=>['placeholder'=>$placeholder]
                            ],
                            $options);
    }
    


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /* $builder->add('startDate', DateType::class, [
    
                    'widget' => 'single_text',
                    'html5' => false,
                    'label' => 'Date de début',
                    'attr' => ['class' => 'datepicker']
                ]) */

/* 
            ->add('endDate', DateType::class,[

                'widget' => 'single_text',
                'html5' => false,
                'label' => 'Date de fin',
                'attr' => ['class' => 'datepicker']
                ]) 
            ->add('comment',TextareaType::class,$this->getConfiguration(false,"Ajoutez un commentaire pour votre séjour "));

            /* builder->get('startDate')->addModelTransformer($this->transformer);
            $builder->get('endDate')->addModelTransformer($this->transformer); */

            $builder->add('startDate', DateType::class,[
                'format' => 'd/m/Y',
                'widget' => 'single_text',
                'html5' => false, 
                'label' => 'Date de fin',
                'input'  => 'datetime',
                'attr' => ['class' => 'datepicker']])

                ->add('endDate', DateType::class, [
                    'format' => 'd/m/Y',
                    'widget' => 'single_text',
                    'html5' => false, 
                    'label' => 'Date de fin',
                    'input'  => 'datetime',
                    'attr' => ['class' => 'datepicker']])

            ->add('comment',TextareaType::class,$this->getConfiguration(false,"Ajoutez un commentaire pour votre séjour "));
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
