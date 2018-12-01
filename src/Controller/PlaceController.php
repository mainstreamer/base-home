<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\Place;
use App\Event\StartPlaceEditionEvent;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Event\StartPlaceCreationEvent;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Controller\DataTablesTrait;
use Symfony\Component\Translation\TranslatorInterface;

class PlaceController extends Controller
{
//    use DataTablesTrait;

    use DataTablesTrait;

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

    public function show(Request $request, Place $place, TranslatorInterface $translator): Response
    {
        $table = $this->createDataTable()
//            ->add('id', TextColumn::class, ['field' => 'bill.id', 'label' => ''])
//            ->add('id', TextColumn::class, ['field' => 'bill.id', 'label' => ''])
            ->add('id', TwigColumn::class, [
                'className' => 'd-flex flex-row comment-row',
                'template' => 'tables/cell.html.twig',
                'label' => $translator->trans('id'),
            ])
            ->add('amount', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell.html.twig',
                'label' => $translator->trans('due'),
            ])
            ->add('status', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell.html.twig',
                'label' => $translator->trans('status'),
            ])

//            ->add('amount', TextColumn::class, ['field' => 'bill.amount'])

//            ->add('status', TextColumn::class, ['field' => 'bill.status', 'render' => function ($value, $entity) {
//                $url = $this->generateUrl('bill_show', ['id' => $entity->getId()]);
//
//                return "<a href=$url>$value</a>";
//            }])

            /*->add('textDate', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell.html.twig',
                'label' => $translator->trans('date'),
            ])*/
//            ->add('date', TextColumn::class, ['field' => 'bill.textDate'])
            ->add('date', DateTimeColumn::class, ['format' => 'Y-m-d', 'field' => 'bill.date', 'label' => $translator->trans('date')])
//            ->add('date', DateTimeColumn::class, ['format' => 'd-m-Y'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Bill::class,
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('place/show.html.twig', ['place' => $place, 'datatable' => $table]);
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
