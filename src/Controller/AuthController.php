<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\User\UserEvent;
use App\Utils\JSON;
use App\Utils\Validator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthController extends AbstractController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/")
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('auth/index.html.twig');
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em,
                             SerializerInterface $serializer, ValidatorInterface $validator)
    {

        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $violations = $validator->validate($user);
        if ($violations->count() > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = $violation->getMessage();
            }
            return new JsonResponse([
                'status' => false,
                'messages' => $messages
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $event = new UserEvent($user, $request);
        $this->eventDispatcher->dispatch(UserEvent::ON_REGISTRATION_SUCCESS, $event);

        $pass = $user->getPassword();
        $user->setPassword($encoder->encodePassword($user, $pass));

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'status' => true,
            'message' => 'Registration successful'
        ], Response::HTTP_CREATED);
    }

    /**
     * @param null $token
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @throws \Exception
     */
    public function confirmEmail($token = null, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        if (null === $token) {
            return new JsonResponse([
                'status' => false,
                'message' => $translator->trans('account.confirmation.token.missing')
            ]);
        }

        $user = $em->getRepository(User::class)->loadByConfirmationToken($token);
        if (null === $user) {
            return new JsonResponse([
                'status' => false,
                'message' => $translator->trans('account.confirmation.token.invalid')
            ]);
        }

        $user->setIsActive(true);
        $user->setConfirmationToken(null);
        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'status' => true,
            'message' => $translator->trans('account.confirmation.confirmed')
        ]);
    }

    /**
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function profile(SerializerInterface $serializer)
    {
        $user = $this->getUser();

        return JSON::JSONResponseWithGroups($user, Response::HTTP_OK, $serializer, ['profile']);
    }


    public function editProfile(Request $request, SerializerInterface $serializer, ValidatorInterface $validator,
                                EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $updatedUser = $serializer->deserialize($request->getContent(), User::class, 'json');

        $user->setFirstName($updatedUser->getFirstName());
        $user->setLastName($updatedUser->getLastName());
        $user->setOccupation($updatedUser->getOccupation());
        $user->setBio($updatedUser->getBio());

        $validation = Validator::validate($validator, $user);
        if (!$validation['status']) {
            return JSON::JSONResponse([
                'status' => false,
                'messages' => $validation['messages']
            ], Response::HTTP_UNPROCESSABLE_ENTITY, $serializer);
        }

        $em->persist($user);
        $em->flush();

        return JSON::JSONResponseWithGroups([
            'status' => true,
            'message' => $translator->trans('account.edited'),
            'user' => $user
        ], Response::HTTP_ACCEPTED, $serializer, ['profile', 'public']);
    }


    public function changePassword(Request $request, SerializerInterface $serializer, ValidatorInterface $validator,
                                    EntityManagerInterface $em, TranslatorInterface $translator, UserPasswordEncoderInterface $encoder)
    {
        $json = json_decode($request->getContent());
        $user = $this->getUser();

        if ($user->getPassword() !== $encoder->encodePassword(User::class, $json->oldPassword)) {
            return JSON::JSONResponse([
                'status' => false,
                'message' => $translator->trans('account.password.oldInvalid')
            ], Response::HTTP_BAD_REQUEST, $serializer);
        }

        if ($json->newPassword !== $json->confirm) {
            return JSON::JSONResponse([
                'status' => false,
                'message' => $translator->trans('account.password.notmatch')
            ], Response::HTTP_BAD_REQUEST, $serializer);
        }

        $user->setPassword($encoder->encodePassword(User::class, $json->newPassword));
        $em->persist($user);
        $em->flush();

        return JSON::JSONResponse([
            'status' => true,
            'message' => $translator->trans('account.password.changed')
        ], Response::HTTP_ACCEPTED, $serializer);
    }

}
