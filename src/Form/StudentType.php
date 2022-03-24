<?php

namespace App\Form;

use App\Entity\Cursus;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('civility', ChoiceType::class,[
                'choices'=> [
                    'Garçon' => true,
                    'Fille'=> false
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('city',TextType::class)
            ->add('zipcode',TextType::class,[
                'required'=>false,
                'attr'=>[
                    'placeholder' => 'Facultatif'
                ]
            ])
            ->add('address',TextType::class,[
                'required'=>false,
                'attr'=>[
                    'placeholder' => 'Facultatif'
                    ]
            ])
            ->add('private', ChoiceType::class,[
                'choices'=> [
                    'Public' => true,
                    'Privée'=> false
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('cursus',EntityType::class, [
                'class' => Cursus::class,
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
