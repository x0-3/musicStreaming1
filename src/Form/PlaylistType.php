<?php

namespace App\Form;

use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => 'Playlist Image',

                // not associated to the Playlist entity
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                            'image/jpeg',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid jpg, png, jpeg or gif image.',
                    ])
                ],
            ])

            ->add('playlistName',TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Enter a name for youre playlist',
                ]
            ])
            
            ->add('description',TextType::class, [
                'label' => 'Playlist description',
                'attr' => [
                    'placeholder' => 'tell us what your playlist is about',
                ]
            ])


            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
