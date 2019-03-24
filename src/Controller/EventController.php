<?php

namespace App\Controller;

use App\Entity\Event;
use App\Security\Voter\Voters;
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

    public function getEvent(Event $event = null)
    {
        if (null === $event) {
            return JSON::JSONResponse([
                'status' => false,
                'message' => $this->translator->trans('event.notfound')
            ], Response::HTTP_NOT_FOUND, $this->serializer);
        }

        if (!$this->isGranted(Voters::VIEW, $event)) {
            return JSON::JSONResponse([
                'status' => false,
                'message' => $this->translator->trans('event.denied')
            ], Response::HTTP_FORBIDDEN, $this->serializer);
        }

        return JSON::JSONResponseWithGroups($event, Response::HTTP_OK, $this->serializer, ['public']);
    }

    public function getPublicEvent($publicId = null)
    {
        if (null === $publicId) {
            return JSON::JSONResponse([
                'status' => false,
                'message' => $this->translator->trans('event.notfound')
            ], Response::HTTP_NOT_FOUND, $this->serializer);
        }

        $event = $this->em->getRepository(Event::class)->findOneBy(['publicId' => $publicId]);

        if (null === $event) {
            return JSON::JSONResponse([
                'status' => false,
                'message' => $this->translator->trans('event.notfound')
            ], Response::HTTP_NOT_FOUND, $this->serializer);
        }

        if (!$this->isGranted(Voters::VIEW, $event)) {
            return JSON::JSONResponse([
                'status' => false,
                'message' => $this->translator->trans('event.denied')
            ], Response::HTTP_FORBIDDEN, $this->serializer);
        }

        return JSON::JSONResponseWithGroups($event, Response::HTTP_OK, $this->serializer, ['public']);
    }

    public function createEvent(Request $request)
    {
        $event = $this->serializer->deserialize($request->getContent(), Event::class, 'json');
        $event->setOrganizer($this->getUser());
        $validation = Validator::validate($this->validator, $event);
        if ($validation['status']) {
            $this->em->persist($event);
            $this->em->flush();
            return JSON::JSONResponse([
                'status' => true,
                'message' => $this->translator->trans('event.created')
            ], Response::HTTP_CREATED, $this->serializer);
        }

        return JSON::JSONResponse([
            'status' => false,
            'messages' => $validation['messages']
        ], Response::HTTP_UNPROCESSABLE_ENTITY, $this->serializer);
    }

    public function editEvent(Request $request)
    {
        $event = $this->serializer->deserialize($request->getContent(), Event::class, 'json');
        $managedEvent = $this->em->getRepository(Event::class)->find($event->getId());
        if (null === $managedEvent) {
            return JSON::JSONResponse([
                'status' => false,
                'message' => $this->translator->trans('event.notfound')
            ], Response::HTTP_NOT_FOUND, $this->serializer);
        }

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

        $this->em->persist($managedEvent);
        $this->em->flush();

        return JSON::JSONResponse([
            'status' => true,
            'message' => $this->translator->trans('event.edited')
        ], Response::HTTP_ACCEPTED, $this->serializer);
    }

    public function deleteEvent(Event $event = null)
    {
        if ($event === null) {
            return JSON::JSONResponse([
                'status' => false,
                'message' => $this->translator->trans('event.notfound')
            ], Response::HTTP_BAD_REQUEST, $this->serializer);
        }

        $this->em->remove($event);
        $this->em->flush();
        return JSON::JSONResponse([
            'status' => false,
            'message' => $this->translator->trans('event.deleted')
        ], Response::HTTP_ACCEPTED, $this->serializer);
    }

}
