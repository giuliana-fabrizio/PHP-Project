<?php

namespace App\Security\Voter;

use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventVoter extends Voter
{
    const VIEW = 'view';
    const REGISTER = 'register';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::REGISTER]) && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, $event, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof \App\Entity\User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($event, $user);
            case self::REGISTER:
                return $this->canRegister($event, $user);
        }

        return false;
    }

    private function canView(Event $event, $user): bool
    {
        return $event->isPublic() || $user->isLoggedIn();
    }

    private function canRegister(Event $event, $user): bool
    {
        return $this->canView($event, $user);
    }
}
