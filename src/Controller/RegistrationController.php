<?php

namespace App\Controller;

use App\Event\NewUserCreatedEvent;
use App\Form\UserType;
use App\Entity\User;
use App\Services\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * Class RegistrationController.
 */
class RegistrationController extends Controller
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGeneratorInterface $generator
     * @param MailerService $mailer
     * @param EventDispatcherInterface $dispatcher
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @Route("/register", name="registration")
     * @Template
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGeneratorInterface $generator,
        MailerService $mailer,
        EventDispatcherInterface $dispatcher
    )
    {
        $form = $this->createForm(UserType::class, $user = new User());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->plainPassword);
            $user->setPassword($password);
            $user->setToken($generator->generateToken());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $mailer->sendInvitation($user);
            $this->addFlash('message', 'security.message.reset_letter_sent');
            $dispatcher->dispatch(NewUserCreatedEvent::NAME, new NewUserCreatedEvent($user));

            return $this->redirectToRoute('index');
        }

        return ['form' => $form->createView()];
    }
}
