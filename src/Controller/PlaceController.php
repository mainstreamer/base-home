<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\Place;
use App\Event\StartPlaceEditionEvent;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Event\StartPlaceCreationEvent;
use Omines\DataTablesBundle\Controller\DataTablesTrait;
use Symfony\Component\Translation\TranslatorInterface;

class PlaceController extends Controller
{
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
//        \setlocale(LC_ALL,'uk_UA');
//        dump( ucfirst(strftime("%b %Y")));exit;
//
//            $formatter = new \IntlDateFormatter('uk', \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
//            $formatter->setPattern('M Y');
//         $str =    new \DateTime('');
//
//
//        dump(
//            \IntlDateFormatter::formatObject($str, "MMMM y", 'uk')
//            );exit;
//
//
//            dump(\IntlDateFormatter::formatObject(new \DateTime(), \IntlDateFormatter::MEDIUM, 'uk'));
//            exit;
        $table = $this->createDataTable()
//            ->add('id', TwigColumn::class, [
//                'className' => 'd-flex flex-row comment-row',
//                'template' => 'tables/cell.html.twig',
//                'label' => $translator->trans('id'),
//            ])
            ->add('status', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell.html.twig',
                'label' => $translator->trans('status'),
            ])
            ->add('amount', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell.html.twig',
                'label' => $translator->trans('due'),
            ])
            ->add('actuallyPaid', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell.html.twig',
                'label' => $translator->trans('actuallyPaid'),
            ])

            ->add('date', DateTimeColumn::class, ['field' => 'bill.textDate', 'orderField' => 'bill.date', 'format' => 'd-m-Y', 'label' => $translator->trans('billDate')])
            ->add('payDate', DateTimeColumn::class, ['format' => 'd-m-Y', 'label' => $translator->trans('payDate')])
            ->add('period', TextColumn::class, ['field' => 'bill.textPeriod', 'orderField' => 'bill.period', 'label' => $translator->trans('billPeriod')])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Bill::class,

                'query' => function (QueryBuilder $builder) use ($place) {
                    $builder
                        ->select('bill')
                        ->from(Bill::class, 'bill')
                        ->where('bill.place = :id')
                        ->setParameter('id', $place->getId())
                    ;
                },
            ])->addOrderBy('date', 'DESC')
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
