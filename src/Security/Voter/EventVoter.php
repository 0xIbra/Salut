<?php

namespace App\Security\Voter;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventVoter extends Voter
{

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [Voters::VIEW, Voters::EDIT], true)
            && $subject instanceof Event;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case Voters::VIEW:
                return $this->canView($subject, $user);
                break;
            case Voters::EDIT:
                return $this->canEdit($subject, $user);
                break;
        }

        return false;
    }

    public function canView(Event $event, User $user)
    {
        if (Voters::isAdmin($user)) {
            return true;
        }

        if ($event->getEnabled()) {
            return true;
        }

        if ($event->getOrganizer()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }


    public function canEdit(Event $event, User $user)
    {
        if (Voters::isAdmin($user)) {
            return true;
        }

        if ($event->getOrganizer()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }


}
