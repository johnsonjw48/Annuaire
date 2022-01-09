<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class)
            ->add('Last_name', TextType::class)
            ->add('birth_date', DateTimeType::class,  [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5'=>false
            ])
            ->add('address')
            ->add('city')
            ->add('alternance_job')
            ->add('avatar', FileType::class, [
                'label' =>'Photo de profil',
                'multiple'=> false,
                'mapped'=> false,
                'required'=> false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
