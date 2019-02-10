<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Meter;
use App\Entity\Tariff;
use App\Form\TariffType;
use Omines\DataTablesBundle\Controller\DataTablesTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TariffController extends Controller
{
    use DataTablesTrait;

    public function index(): Response
    {
        return $this->render('tariff/index.html.twig', ['places' => $this->getUser()->getPlaces()]);
    }

    /**
     * @param Request $request
     * @return Response
     * TODO remove
     */
    public function new(Request $request): Response
    {
        $item = new Tariff();
        $item->setUser($this->getUser());
        $form = $this->createForm(TariffType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            $this->addFlash('message', 'tariff.created');

            return $this->redirectToRoute('tariff_index');
        }

        return $this->render('tariff/new.html.twig', [
            'tariff' => $item,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Meter $meter
     * @return Response
     * @Security("user === meter.getPlace().getUser()")
     */
    public function newTariffForMeter(Request $request, Meter $meter): Response
    {
        $item = new Tariff();
        $item->setMeter($meter);
        $item->setUser($this->getUser());
        $meter->addTariff($item);
        $form = $this->createForm(TariffType::class, $item);
        $form->remove('user');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            $this->addFlash('message', 'tariff.created');

            return $this->redirectToRoute('tariff_index');
        }

        return $this->render('tariff/new.html.twig', [
            'tariff' => $item,
            'form' => $form->createView(),
            'place' => $meter->getPlace(),
            'meter' => $meter
        ]);
    }

    /**
     * @param Tariff $tariff
     * @return Response
     * @Security("user === tariff.getUser()")
     */
    public function show(Tariff $tariff): Response
    {
        return $this->render('tariff/show.html.twig', ['tariff' => $tariff]);
    }

    /**
     * @param Request $request
     * @param Tariff $tariff
     * @return Response
     * @Security("user === tariff.getUser()")
     */
    public function edit(Request $request, Tariff $tariff): Response
    {
        $form = $this->createForm(TariffType::class, $tariff);
        $form->remove('place')->remove('user');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('message', 'changes_saved');

            return $this->redirectToRoute('tariff_edit', ['id' => $tariff->getId()]);
        }

        return $this->render('tariff/edit.html.twig', [
            'tariff' => $tariff,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Tariff $tariff
     * @return Response
     * @Security("user === tariff.getUser()")
     */
    public function delete(Request $request, Tariff $tariff): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tariff->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tariff);
            $em->flush();
            $this->addFlash('message', 'tariff.deleted');
        }

        return $this->redirectToRoute('tariff_index');
    }
}
