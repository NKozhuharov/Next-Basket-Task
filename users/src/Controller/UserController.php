<?php

namespace App\Controller;

use App\Entity\User;
use App\Message\NewUserMessage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    public function __construct(protected ValidatorInterface $validator)
    {
    }

    #[Route('/users', name: 'create_user')]
    public function createProduct(MessageBusInterface $bus, ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $user = new User();
        $user->setEmail($request->get('email'));
        $user->setFirstName($request->get('firstName'));
        $user->setLastName($request->get('lastName'));

        $validationErrors = $this->validator->validate($user);

        if (count($validationErrors) > 0) {
            $response = [];
            foreach ($validationErrors as $error) {
                $response[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse($response);
        }

        $entityManager->persist($user);

        $entityManager->flush();

        $bus->dispatch(new NewUserMessage($user));

        return new JsonResponse($user->toArray());
    }
}
