<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 21/03/2019
 * Time: 06:24
 */

namespace App\EventListener;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$user->getIsActive()) {
            return;
        }

        $this->refreshUser($user);

        $data['user'] = [
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'bio' => $user->getBio(),
            'occupation' => $user->getOccupation(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'is_active' => $user->getIsActive(),
            'last_login' => $user->getLastLogin()
        ];

        $event->setData($data);
    }


    public function refreshUser(User $user)
    {
        $user->setLastLogin(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

}