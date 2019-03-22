<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 22/03/2019
 * Time: 11:29
 */

namespace App\EventListener\User;


use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class JWTExpiredListener
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function onJWTExpired(JWTExpiredEvent $event)
    {
        $response = $event->getResponse();

        $response->setMessage($this->translator->trans('auth.token.expired'));
    }

}