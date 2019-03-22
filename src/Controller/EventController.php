<?php

namespace App\Controller;

use App\Utils\JSON;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EventController extends AbstractController
{
    public function myEvents(SerializerInterface $serializer)
    {
        $events = $this->getUser()->getEvents();
        return JSON::JSONResponse($events, Response::HTTP_OK, $serializer);
    }
}
