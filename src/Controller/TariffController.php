<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Meter;
use App\Entity\Tariff;
use App\Form\TariffType;
use App\Repository\TariffRepository;
use Omines\DataTablesBundle\Controller\DataTablesTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class TariffController extends Controller
{
    use DataTablesTrait;

    public function index(TariffRepository $tariffRepository): Response
    {
        return $this->render('tariff/index.html.twig', ['places' => $this->getUser()->getPlaces()]);
    }

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

    public function newTariffForMeter(Request $request, Meter $meter): Response
    {

        $item = new Tariff();
        $item->setMeter($meter);
        $item->setUser($this->getUser());
        $meter->addTariff($item);
        $form = $this->createForm(TariffType::class, $item);
//        $form->remove('meter');
        $form->remove('user');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            $this->addFlash('message', 'tariff.created');

//            dump($item);exit;
            return $this->redirectToRoute('tariff_index');
        }

        return $this->render('tariff/new.html.twig', [
            'tariff' => $item,
            'form' => $form->createView(),
            'place' => $meter->getPlace(),
            'meter' => $meter
//            'user' => $item->getUser()
        ]);
    }

    public function show(Request $request, Tariff $tariff, TranslatorInterface $translator): Response
    {


//        return $this->render('place/show.html.twig', ['place' => $place, 'datatable' => $table]);


        return $this->render('tariff/show.html.twig', ['tariff' => $tariff]);
    }

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
