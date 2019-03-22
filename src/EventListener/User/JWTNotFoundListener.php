<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 22/03/2019
 * Time: 11:20
 */

namespace App\EventListener\User;


use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class JWTNotFoundListener
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $data = [
            'status' => false,
            'message' => $this->translator->trans('auth.token.notfound')
        ];

        $response = new JsonResponse($data, Response::HTTP_FORBIDDEN);

        $event->setResponse($response);
    }

}