<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 22/03/2019
 * Time: 13:51
 */

namespace App\EventListener\User;


use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class JWTInvalidListener
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    public function onJWTInvalid(JWTInvalidEvent $event)
    {
        $response = new JWTAuthenticationFailureResponse($this->translator->trans('auth.token.invalid'));

        $event->setResponse($response);
    }

}