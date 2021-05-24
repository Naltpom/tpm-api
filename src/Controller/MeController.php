<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MeController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/me", name="me")
     */
    public function __invoke(): JsonResponse
    {
        $user = $this->security->getUser();

        $json = [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'status' => $user->getStatus(),
            'token' => $user->getToken(),
        ];

        return new JsonResponse($json) ;
    }
}
