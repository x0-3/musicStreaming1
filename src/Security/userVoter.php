<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class userVoter extends Voter
{

    protected function supports(string $attribute, mixed $subject): bool
    {
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser(); // get the user
    
        if (!$user instanceof User) {
            return false;
        }
    
        // if the user is banned then logout the user
        if ($user->isBanned()) {
            session_unset();
            return false;
        }
    
        // if the user has the role user in the array of roles
        if (in_array('ROLE_USER', $user->getRoles())) {
            return false;
        }
    
        return true;
    }
    

    
}