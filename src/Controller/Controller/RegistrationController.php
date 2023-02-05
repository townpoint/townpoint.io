<?php

declare(strict_types = 1);

namespace App\Controller\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserWebAuthenticator;
use App\Service\AvailableHandleGenerator;
use App\Service\AvatarService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly AvatarService $avatarService,
        private readonly AvailableHandleGenerator $availableHandleGenerator,
        private readonly StripeClient $stripeClient
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserWebAuthenticator $authenticator,
        EntityManagerInterface $entityManager
    ): ?Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'suggestions' => $this->availableHandleGenerator->generate(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $this->avatarService->createAvatar($user->getEmail());
            $user->setAvatar($avatar);

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword') ->getData()));

            $user->setHandle(trim((string)$form->get('handle')->getData()));

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser($user, $authenticator, $request);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
