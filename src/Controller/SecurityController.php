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

    public function changePassword(Request $request, TokenGeneratorInterface $generator)
    {
        $form =  $this->createForm(UserPasswordType::class, $user = $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken($generator->generateToken());
        }
//        $encoder->encodePassword()
//        $response = $service->upload($request->files->get('file'));
//        $user->setProfilePic($response);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse('pl', \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function changePasswordRequest(Request $request, MailerService $mailerService, TokenGeneratorInterface $generator): Response
    {
        $form = $this->createForm(ChangePasswordRequest::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($form->getData()['email'])) {
                /** @var $user User*/
                $user->setToken($generator->generateToken());
                $mailerService->sendResetLink($user);
//                $user->setPassword(null);
                $this->getDoctrine()->getManager()->flush();
                $statusCode = Response::HTTP_OK;
                $this->addFlash('message', 'security.message.reset_letter_sent');

                return $this->redirectToRoute('login');
            }
        }

        return new JsonResponse();
    }

    /**
     * @param Request $request
     * @param User $token
     * @param MailerService $mailerService
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function changePasswordConfirm(Request $request, User $user, MailerService $mailerService, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->remove('oldPassword');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->plainPassword);
            $user->setPassword($password);
            $user->setToken(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $mailerService->sendPasswordChanged($user);
            $this->addFlash('message', 'security.message.password_changed');

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/password.html.twig', ['user' => $user, 'form'=> $form->createView()]);
    }

    public function confirmAccount(User $user)
    {
        $user->setEnabled(true);
        $user->setToken(null);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('message', 'security.message.account_enabled');

        return $this->redirectToRoute('login');
    }
}
