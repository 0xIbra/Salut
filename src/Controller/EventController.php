<?php

namespace App\Controller;

use App\Entity\Event;
use App\Utils\JSON;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventController extends AbstractController
{

    private $em;
    private $serializer;
    private $validator;
    private $translator;
    private $eventDispatcher;


    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer,
                                ValidatorInterface $validator, TranslatorInterface $translator,
                                EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function myEvents()
    {
        $events = $this->getUser()->getEvents();
        return JSON::JSONResponse($events, Response::HTTP_OK, $this->serializer);
    }

    public function createEvent(Request $request)
    {
        $event = $this->serializer->deserialize($request->getContent(), Event::class, 'json');
        if ($event->getId() !== null) {
            $managedEvent = $this->em->getRepository(Event::class)->find($event->getId());
            if ($managedEvent !== null) {
                $managedEvent
                    ->setTitle($event->getTitle())
                    ->setDescription($event->getDescription())
                    ->setLocation($event->getLocation())
                    ->setStart($event->getStart())
                    ->setEnd($event->getEnd())
                    ->setSpots($event->getSpots())
                    ->setImage($event->getImage())
                    ->setPublicId($event->getPublicId())
                    ->setEnabled($event->getEnabled());

                $event = $managedEvent;
            }
        }
        $event->setOrganizer($this->getUser());
        $validation = Validator::validate($this->validator, $event);
        if ($validation['status']) {
            $message = null;
            if ($event->getId() !== null)
                $message = $this->translator->trans('event.edited');
            else
                $message = $this->translator->trans('event.created');

            $this->em->persist($event);
            $this->em->flush();

            return JSON::JSONResponse([
                'status' => true,
                'message' => $message
            ], Response::HTTP_CREATED, $this->serializer);
        }

        return JSON::JSONResponse([
            'status' => false,
            'messages' => $validation['messages']
        ], Response::HTTP_UNPROCESSABLE_ENTITY, $this->serializer);
    }

}
