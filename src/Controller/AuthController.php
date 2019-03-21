<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{

    /**
     * @Route("/")
     */
    public function index()
    {
        return new JsonResponse(['Hello World!']);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return JsonResponse
     *
//     * @Route("/register", methods={"POST"})
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

        $pass = $user->getPassword();
        $user->setPassword($encoder->encodePassword($user, $pass));

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'status' => true,
            'message' => 'Registration successful'
        ], Response::HTTP_CREATED);
    }


}
