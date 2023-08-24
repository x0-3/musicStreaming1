<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Song;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameSong', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'What\'s the name of your song'
                ]
            ])

            ->add('link', FileType::class, [
                'label' => 'Music Link',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'audio/mpeg',
                            'audio/mp4',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid mpeg or mp4 music file.',
                    ])
                ],
            ])



            ->add('genre', EntityType::class,[
                'class' =>Genre::class,
                'choice_label' => 'genreName',
                'label' => 'Music Genre',
            ])

            ->add('submit',SubmitType::class)


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
        ]);
    }
}
