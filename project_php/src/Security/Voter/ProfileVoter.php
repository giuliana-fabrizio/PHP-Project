<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileVoter extends Voter
{
    const VIEW = 'view_profile';
    const EDIT = 'edit_profile';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            // L'utilisateur doit être connecté
            return false;
        }

        /** @var User $subject */
        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
                return $user === $subject;
        }

        return false;
    }
}