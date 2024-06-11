<?php

namespace App\Security\Voter;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventVoter extends Voter
{
    public const VIEW = 'view';
    public const REGISTER = 'register';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::REGISTER, self::EDIT, self::DELETE]) 
            && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, $event, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($event, $user);
            case self::REGISTER:
                return $this->canRegister($event, $user);
            case self::EDIT:
                return $this->canEdit($event, $user);
            case self::DELETE:
                return $this->canDelete($event, $user);
        }

        return false;
    }

    private function canView(Event $event, User $user): bool
    {
        return $event->isPublic();
    }


    private function canRegister(Event $event, User $user): bool
    {
        return $this->canView($event, $user);
    }

    private function canEdit(Event $event, User $user): bool
    {
        return $user === $event->getCreator();
    }

    private function canDelete(Event $event, User $user): bool
    {
        return $this->canEdit($event, $user);
    }
}
