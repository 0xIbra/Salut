<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 21/03/2019
 * Time: 09:34
 */

namespace App\Event\User;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class UserEvent extends Event
{
    const ON_REGISTRATION_SUCCESS = 'onRegistrationSuccess';
    const ON_REGISTRATION_FAILURE = 'onRegistrationFailure';


    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    public function __construct(UserInterface $user, Request $request, Response $response = null)
    {
        $this->user = $user;
        $this->request = $request;
        $this->response = $response;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }



}