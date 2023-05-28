<?php

namespace App\Controller\Admin;

use App\Entity\User;

use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {

        yield EmailField::new('email');

        yield TextField::new('username');
        
        yield ImageField::new('avatar')
        ->setUploadDir('public/uploads/');
        
        // yield TextField::new('password')
        // ->setFormType(PasswordType::class);

        yield ChoiceField::new('roles')
        ->setChoices([
            'User' => 'ROLE_USER',
            'Artist'=> 'ROLE_ARTIST',
        ])
        ->renderExpanded()
        ->allowMultipleChoices();
        
        yield BooleanField::new('isVerified');

        yield BooleanField::new('isBanned')->renderAsSwitch(true);

    }
   
}
