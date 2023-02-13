<?php

namespace App\Controller;

use App\Entity\User;
use App\Message\NewUserMessage;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    public function __construct(
        protected ValidatorInterface  $validator,
        protected MessageBusInterface $bus,
        protected UserRepository      $repository,
        protected RequestStack        $requestStack
    )
    {
    }

    #[Route('/users', name: 'create_user')]
    /**
     * Validate the incoming request.
     * Create a new User in the database.
     * Send a NewUserMessage to the RabbitMQ.
     */
    public function createUser(): JsonResponse
    {
        $request = $this->requestStack->getCurrentRequest();
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

            return new JsonResponse($response, 422);
        }

        $this->repository->save($user, true);

        $this->bus->dispatch(new NewUserMessage($user));

        return new JsonResponse($user->toArray());
    }
}
