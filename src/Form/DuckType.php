<?php

namespace App\Form;

use App\Entity\Duck;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class DuckType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('duckname')
            ->add('avatar', FileType::class, [
                'label' => 'Avatar (JPEG, PNG or GIF file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'mimeTypesMessage' => 'Please upload a valid JPEG, PNG or GIF image',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                    ]),
                ],
                'data_class' => null,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Duck::class,
        ]);
    }
}
