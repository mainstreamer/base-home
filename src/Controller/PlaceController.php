<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Place;
use App\Event\StartPlaceEditionEvent;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Event\StartPlaceCreationEvent;

class PlaceController extends AbstractController
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function index(PlaceRepository $placeRepository): Response
    {
        return $this->render('place/index.html.twig', ['places' => $placeRepository->findAll()]);
    }

    public function new(Request $request): Response
    {
        $form = $this->createForm(PlaceType::class, $place = new Place());
        $this->dispatcher->dispatch(StartPlaceCreationEvent::NAME, new StartPlaceCreationEvent($form, $this->getUser(), $place));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();

            return $this->redirectToRoute('place_index');
        }

        return $this->render('place/new.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    public function show(Place $place): Response
    {
        return $this->render('place/show.html.twig', ['place' => $place]);
    }

    public function edit(Request $request, Place $place): Response
    {
        $form = $this->createForm(PlaceType::class, $place);
        $this->dispatcher->dispatch(StartPlaceEditionEvent::NAME, new StartPlaceEditionEvent($form, $this->getUser(), $place));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('place_edit', ['id' => $place->getId()]);
        }

        return $this->render('place/edit.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Place $place): Response
    {
        if ($this->isCsrfTokenValid('delete'.$place->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($place);
            $em->flush();
        }

        return $this->redirectToRoute('place_index');
    }

    /**
     * @return iterable
     * @Template()
     */
    public function myPlaces(): iterable
    {
        return ['places' => $this->getUser()->getPlaces()];
    }

    public function myNew(Request $request): Response
    {
        $place = new Place();
        $place->setUser($this->getUser());
        $form = $this->createForm(PlaceType::class, $place)->remove('user');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();

            return $this->redirectToRoute('place_index');
        }

        return $this->render('place/new.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }
}
