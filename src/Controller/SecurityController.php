<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordRequest;
use App\Form\UserPasswordType;
use App\Services\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SecurityController extends Controller
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return array
     * @Template
     */
    public function login(AuthenticationUtils $authenticationUtils): array
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(ChangePasswordRequest::class);

        return ['last_username' => $lastUsername, 'error' => $error, 'form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param TokenGeneratorInterface $generator
     * @return JsonResponse
     */
    public function changePassword(Request $request, TokenGeneratorInterface $generator)
    {
        $form =  $this->createForm(UserPasswordType::class, $user = $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken($generator->generateToken());
            $this->getDoctrine()->getManager()->flush();
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param MailerService $mailerService
     * @param TokenGeneratorInterface $generator
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function changePasswordRequest(Request $request, MailerService $mailerService, TokenGeneratorInterface $generator): Response
    {
        $form = $this->createForm(ChangePasswordRequest::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($form->getData()['email'])) {
                /** @var $user User*/
                $user->setToken($generator->generateToken());
                $mailerService->sendResetLink($user);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('message', 'security.message.reset_letter_sent');
            } else {
                $this->addFlash('error', 'security.message.not_found');
            }
        } else {
            $this->addFlash('error', 'security.form.not_valid');
        }

        return $this->redirectToRoute('login');
    }

    /**
     * @param Request $request
     * @param User $userObject
     * @param MailerService $mailerService
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @Security("user === userObject")
     */
    public function changePasswordConfirm(Request $request, User $userObject, MailerService $mailerService, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserPasswordType::class, $userObject);
        $form->remove('oldPassword');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($userObject, $userObject->plainPassword);
            $userObject->setPassword($password);
            $userObject->setToken(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $mailerService->sendPasswordChanged($userObject);
            $this->addFlash('message', 'security.message.password_changed');

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/password.html.twig', ['user' => $userObject, 'form'=> $form->createView()]);
    }

    /**
     * @param User $user
     * @return Response
     */
    public function confirmAccount(User $user): Response
    {
        $user->setEnabled(true);
        $user->setToken(null);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('message', 'security.message.account_enabled');

        return $this->redirectToRoute('login');
    }
}
