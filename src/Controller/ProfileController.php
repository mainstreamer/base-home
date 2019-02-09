<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use App\Services\FileUploaderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends Controller
{

    /**
     * @Template("profile/index.html.twig")
     */
    public function profile(Request $request, FileUploaderService $service, UserPasswordEncoderInterface $encoder)
    {

        $passwordForm = $this->createForm(UserPasswordType::class, $user = $this->getUser());
        $form = $this->createForm(UserType::class, $user);
        $form->remove('plainPassword');
        $form->handleRequest($request);
        $passwordForm->handleRequest($request);

        if ( ($form->isSubmitted() && $form->isValid())) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('message', 'changes_saved');

            return $this->redirectToRoute('profile');
        }

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $password = $encoder->encodePassword($user, $user->plainPassword);
            $user->setPassword($password);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('message', 'security.message.password_changed');

            return $this->redirectToRoute('profile');
        }

        return ['user' => $this->getUser(), 'form' => $form->createView(), 'password' => $passwordForm->createView()];
    }

    public function index()
    {
        return $this->redirectToRoute('my_places');

    }

    public function analytics()
    {
        return $this->render('content.html.twig');
    }

    public function changePicture(Request $request, User $user, FileUploaderService $service)
    {
        $response = $service->upload($request->files->get('file'));
        $user->setProfilePic($response);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse($response, \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
}
