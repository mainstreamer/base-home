<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Indication;
use App\Entity\Meter;
use App\Entity\Tariff;
use App\Entity\TariffType;
use App\Entity\Place;
use App\Form\TariffTypeType;
use App\Repository\TariffTypeRepository;
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

class TariffTypeController extends Controller
{
    use DataTablesTrait;

    public function index(TariffTypeRepository $repo): Response
    {
        return $this->render('tariff_type/index.html.twig', ['tariffTypes' => $repo->findAll()]);
    }

    public function new(Request $request): Response
    {
        $item = new TariffType();
        $form = $this->createForm(TariffTypeType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('tariff_type_index');
        }

        return $this->render('tariff_type/new.html.twig', [
            'tariff' => $item,
            'form' => $form->createView(),
        ]);
    }

    public function newTariffTypeAndTariffForMeter(Request $request, Meter $meter): Response
    {
        $user = $meter->getPlace()->getUser();
        $tariffType = new TariffType();
        $tariffType->setName($request->request->get('content'));
        $tariffType->setUser($user);


        $tariff = new Tariff();
        $tariff->setType($tariffType);
        $tariff->setUser($user);

        $tariff->setName($tariffType->getName().' tariff for '.$meter->getId());
        $em = $this->getDoctrine()->getManager();

        $em->persist($tariff);

        $meter->addTariff($tariff);
        $em->flush();

        return new Response($tariff->getId());
    }

    public function show(Request $request, TariffType $tariff, TranslatorInterface $translator): Response
    {


//        return $this->render('place/show.html.twig', ['place' => $place, 'datatable' => $table]);


        return $this->render('tariff/show.html.twig', ['tariff' => $tariff]);
    }

    public function edit(Request $request, TariffType $tariff): Response
    {
        $form = $this->createForm(TariffTypeType::class, $tariff);
        $form->remove('place');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tariff_type_edit', ['id' => $tariff->getId()]);
        }

        return $this->render('tariff_type/edit.html.twig', [
            'tariff' => $tariff,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, TariffType $tariffType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tariffType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tariffType);
            $em->flush();
            $this->addFlash('message', 'tariff type deleted');
        }

        return $this->redirectToRoute('tariff_index');
    }
}
