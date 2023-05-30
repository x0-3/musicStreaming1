<?php

namespace App\Form;

use App\Model\SearchBar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchBarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class, [
                'attr' => [
                    'placeholder' => 'search for song',
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchBar::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}
