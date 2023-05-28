<?php

namespace App\Form;

use App\Entity\Song;
use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddSongsToPlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('nameSong')
            // ->add('link')
            // ->add('user')
            // ->add('album')
            // ->add('genre')
            // ->add('likes')

            // FIXME: not adding songs in db
            ->add('playlists', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'playlistName',
                'multiple' => true,
                'expanded' => true,
            ])
            
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
        ]);
    }
}
