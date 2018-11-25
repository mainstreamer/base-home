<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Meter;
use App\Form\MeterType;
use App\Repository\MeterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MeterController extends AbstractController
{
    public function index(MeterRepository $meterRepository): Response
    {
        return $this->render('meter/index.html.twig', ['meters' => $meterRepository->findAll()]);
    }

    public function new(Request $request): Response
    {
        $item = new Meter();
        $form = $this->createForm(MeterType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('meter_index');
        }

        return $this->render('meter/new.html.twig', [
            'meter' => $item,
            'form' => $form->createView(),
        ]);
    }

    public function show(Meter $meter): Response
    {
        return $this->render('meter/show.html.twig', ['meter' => $meter]);
    }

    public function edit(Request $request, Meter $meter): Response
    {
        $form = $this->createForm(MeterType::class, $meter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('meter_edit', ['id' => $meter->getId()]);
        }

        return $this->render('meter/edit.html.twig', [
            'meter' => $meter,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Meter $meter): Response
    {
        if ($this->isCsrfTokenValid('delete'.$meter->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($meter);
            $em->flush();
        }

        return $this->redirectToRoute('meter_index');
    }
}
