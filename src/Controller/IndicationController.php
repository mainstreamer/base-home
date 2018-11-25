<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Indication;
use App\Form\IndicationType;
use App\Repository\IndicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndicationController extends AbstractController
{
    public function index(IndicationRepository $indicationRepository): Response
    {
        return $this->render('indication/index.html.twig', ['indications' => $indicationRepository->findAll()]);
    }

    public function new(Request $request): Response
    {
        $item = new Indication();
        $form = $this->createForm(IndicationType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('indication_index');
        }

        return $this->render('indication/new.html.twig', [
            'indication' => $item,
            'form' => $form->createView(),
        ]);
    }

    public function show(Indication $indication): Response
    {
        return $this->render('indication/show.html.twig', ['indication' => $indication]);
    }

    public function edit(Request $request, Indication $indication): Response
    {
        $form = $this->createForm(IndicationType::class, $indication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('indication_edit', ['id' => $indication->getId()]);
        }

        return $this->render('indication/edit.html.twig', [
            'indication' => $indication,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Indication $indication): Response
    {
        if ($this->isCsrfTokenValid('delete'.$indication->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($indication);
            $em->flush();
        }

        return $this->redirectToRoute('indication_index');
    }
}
