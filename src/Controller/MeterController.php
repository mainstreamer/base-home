<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Indication;
use App\Entity\Meter;
use App\Entity\Place;
use App\Form\MeterType;
use App\Repository\MeterRepository;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\Controller\DataTablesTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class MeterController extends Controller
{
    use DataTablesTrait;

    /**
     * @param MeterRepository $meterRepository
     * @return Response
     * TODO remove
     */
    public function index(MeterRepository $meterRepository): Response
    {
        return $this->render('meter/index.html.twig', ['meters' => $meterRepository->findAll()]);
    }

    /**
     * @param Request $request
     * @return Response
     * TODO remove
     */
    public function new(Request $request): Response
    {
        $item = new Meter();
        $form = $this->createForm(MeterType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            $this->addFlash('message', 'meter.created');

            return $this->redirectToRoute('meter_index');
        }

        return $this->render('meter/new.html.twig', [
            'meter' => $item,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Place $place
     * @return Response
     * @Security("user === place.getUser()")
     */
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
            $this->addFlash('message', 'meter.created');

            return $this->redirectToRoute('place_show', ['id' => $place->getId()]);
        }

        return $this->render('meter/new.html.twig', [
            'meter' => $item,
            'form' => $form->createView(),
            'place' => $place
        ]);
    }

    /**
     * @param Request $request
     * @param Meter $meter
     * @param TranslatorInterface $translator
     * @return Response
     * @Security("user === meter.getPlace().getUser()")
     */
    public function show(Request $request, Meter $meter, TranslatorInterface $translator): Response
    {
        $table = $this->createDataTable()
            ->add('value', TwigColumn::class, [
                'className' => '',
                'template' => 'indication/cell.html.twig',
                'label' => $translator->trans('indication.value'),
            ])
            ->add('unit', TwigColumn::class, [
                'className' => '',
                'template' => 'indication/cell.html.twig',
                'label' => $translator->trans('indication.unit.title'),
            ])
            ->add('textDate', TwigColumn::class, [
                'label' => $translator->trans('indication.date'),
                'orderField' => 'indication.date',
                'template' => 'indication/cell.html.twig',
            ])
            ->add('tariff', TwigColumn::class, [
                'className' => '',
                'field' => 'indication.tariff',
                'template' => 'indication/cell.html.twig',
                'label' => $translator->trans('indication.tariff'),
            ])
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
            ])->addOrderBy('textDate', 'DESC')
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('meter/show.html.twig', ['meter' => $meter, 'datatable' => $table]);
    }

    /**
     * @param Request $request
     * @param Meter $meter
     * @return Response
     * @Security("user === meter.getPlace().getUser()")
     */
    public function edit(Request $request, Meter $meter): Response
    {
        $form = $this->createForm(MeterType::class, $meter);
        $form->remove('place');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('message', 'changes_saved');

            return $this->redirectToRoute('meter_edit', ['id' => $meter->getId()]);
        }

        return $this->render('meter/edit.html.twig', [
            'meter' => $meter,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Meter $meter
     * @return Response
     * @Security("user === meter.getPlace().getUser()")
     */
    public function delete(Request $request, Meter $meter): Response
    {
        if ($this->isCsrfTokenValid('delete'.$meter->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($meter);
            $em->flush();

            $this->addFlash('message', 'meter.deleted');
        }

        return $this->redirectToRoute('place_show', ['id' => $meter->getPlace()->getId()]);
    }
}
