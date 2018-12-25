<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Indication;
use App\Entity\Meter;
use App\Entity\Place;
use App\Form\MeterType;
use App\Repository\MeterRepository;
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

class MeterController extends Controller
{
    use DataTablesTrait;

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

    public function newMeterForPlace(Request $request, Place $place): Response
    {
        $item = new Meter();
        $item->setPlace($place);
        $form = $this->createForm(MeterType::class, $item);
        $form->remove('place');
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

    public function show(Request $request, Meter $meter, TranslatorInterface $translator): Response
    {
        $table = $this->createDataTable()
            ->add('id', TwigColumn::class, [
                'className' => 'd-flex flex-row comment-row',
                'template' => 'indication/cell.html.twig',
                'label' => $translator->trans('id'),
            ])
            ->add('name', TwigColumn::class, [
                'className' => '',
                'template' => 'indication/cell.html.twig',
                'label' => $translator->trans('name'),
            ])
            ->add('unit', TwigColumn::class, [
                'className' => '',
                'template' => 'indication/cell.html.twig',
                'label' => $translator->trans('unit'),
            ])
            ->add('value', TwigColumn::class, [
                'className' => '',
                'template' => 'indication/cell.html.twig',
                'label' => $translator->trans('value'),
            ])
//            ->add('period', TextColumn::class, ['field' => 'bill.textPeriod', 'orderField' => 'bill.period', 'label' => $translator->trans('billPeriod')])
//            ->add('type', TwigColumn::class, [
//                'className' => '',
//                'template' => 'tables/cell.html.twig',
//                'label' => $translator->trans('service'),
//            ])
//            ->add('amount', TwigColumn::class, [
//                'className' => '',
//                'template' => 'tables/cell.html.twig',
//                'label' => $translator->trans('due'),
//            ])
//            ->add('actuallyPaid', TwigColumn::class, [
//                'className' => '',
//                'template' => 'tables/cell.html.twig',
//                'label' => $translator->trans('actuallyPaid'),
//            ])
//
            ->add('date', DateTimeColumn::class)
//            ->add('date', DateTimeColumn::class, ['field' => 'indictaion.textDate', 'orderField' => 'bill.date', 'format' => 'd-m-Y', 'label' => $translator->trans('billDate')])
//            ->add('payDateText', DateTimeColumn::class, ['nullValue' => '–––', 'orderField' => 'bill.payDateText', 'format' => 'd-m-Y', 'label' => $translator->trans('payDate')])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Indication::class,

                'query' => function (QueryBuilder $builder) use ($meter) {
                    $builder
                        ->select('indication')
                        ->from(Indication::class, 'indication')
                        ->where('indication.meter = :id')
                        ->setParameter('id', $meter->getId())
                    ;
                },
            ])->addOrderBy('id', 'DESC')
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

//        return $this->render('place/show.html.twig', ['place' => $place, 'datatable' => $table]);


        return $this->render('meter/show.html.twig', ['meter' => $meter, 'datatable' => $table]);
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
