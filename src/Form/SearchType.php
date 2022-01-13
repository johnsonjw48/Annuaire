<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('qFirstName', TextType::class, [
                'required'=>false,
                'label'=> false,
                'attr'=>[
                    'placeholder'=>'Search by first name',
                    'class'=>'mb-1'
                ]
            ])
            ->add('qGroup', TextType::class, [
                'required'=>false,
                'label'=> false,
                'attr'=>[
                    'placeholder'=>'Search by group: ex M1TL',
                ]
            ])
            ->add('clear', SubmitType::class, [
                'label'=> 'Reset',
                'attr'=>[
                    'class'=> 'btn-danger'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
        ]);
    }
}
