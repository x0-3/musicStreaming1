<?php

namespace App\Form;

use App\Entity\Playlist;
use App\Entity\Song;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaylistSongsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('image')
            // ->add('playlistName')
            // ->add('dateCreated')
            // ->add('user')
            ->add('songs', EntityType::class, [
                'class' => Song::class,
                'choice_label' => 'nameSong',
                'multiple' => true,
                'expanded' => true,
            ])
            // ->add('userFavorites')
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
