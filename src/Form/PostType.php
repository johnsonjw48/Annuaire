<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label'=>false,
                'required'=>'true',
                'attr'=>[
                    'placeholder'=> 'Quoi de neuf ?',
                ],
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Merci dajouter une desc'
                    ])
                ]
            ])
           
            ->add('photos', FileType::class, [
                'label' =>false,
                'multiple'=> true,
                'mapped'=> false,
                'required'=> false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
