<?php

namespace App\Form;

use App\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cover', FileType::class, [
                'label' => 'Playlist Image',

                // unmapped means that this field is not associated to any entity property
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

            ->add('nameAlbum',TextType::class)

            // TODO: add a colection type for the songs
            
            // ->add('programs', CollectionType::class, [

            //     // the collection waits for the element that will go into the form
            //     // not mandatory for it to be another form
            //     'entry_type' => ProgramType::class,
            //     'prototype' => true,

            //     // allows to add new elements into the Session entity that will be persisted thanks to cascade_persist on the programs element 
            //     // it will activate a prototype date that will be an HTML attribute that can be manipulated in JavaScript 
            //     'allow_add' => true, // allow to add a new element
            //     'allow_delete' => true, // allow to delete an element
            //     'by_reference' => false, // mandatory : Session doesn't have a set Programe but program has a setSession
            //     // Program is the owner of the relation 
            //     // to avoid a mapping false we are obligated to set a by_reference
            // ])

        

            ->add('submit', SubmitType::class)

            // ->add('releaseDate')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
        ]);
    }
}
