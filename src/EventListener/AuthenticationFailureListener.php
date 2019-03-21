<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 21/03/2019
 * Time: 06:48
 */

namespace App\EventListener;


use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationFailureListener
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $message = $this->translator->trans('auth.incorrect');

        $response = new JWTAuthenticationFailureResponse($message);
        $event->setResponse($response);
    }
}