<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Indication;
use App\Entity\Tariff;
use App\Entity\Place;
use App\Form\TariffType;
use App\Repository\TariffRepository;
use App\Services\FileUploaderService;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\Controller\DataTablesTrait;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $form = $this->createForm(TariffType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('tariff_index');
        }

        return $this->render('tariff/new.html.twig', [
            'tariff' => $item,
            'form' => $form->createView(),
        ]);
    }

    public function newTariffForPlace(Request $request, Place $place): Response
    {
        $item = new Tariff();
        $item->setPlace($place);
        $form = $this->createForm(TariffType::class, $item);
        $form->remove('place');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('place_show', ['id' => $place->getId()]);
        }

        return $this->render('tariff/new.html.twig', [
            'tariff' => $item,
            'form' => $form->createView(),
            'place' => $place
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
        $form->remove('place');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

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
        }

        return $this->redirectToRoute('place_show', ['id' => $tariff->getPlace()->getId()]);
    }
}
