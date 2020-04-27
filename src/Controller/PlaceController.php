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
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Omines\DataTablesBundle\Controller\DataTablesTrait;
use Symfony\Component\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PlaceController extends AbstractController
{
    use DataTablesTrait;

    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param PlaceRepository $placeRepository
     * @return Response
     * TODO remove
     */
    public function index(PlaceRepository $placeRepository): Response
    {
        return $this->render('place/index.html.twig', ['places' => $placeRepository->findAll()]);
    }

    /**
     * @param Request $request
     * @return Response
     * TODO remove
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(PlaceType::class, $place = (new Place())->setUser($this->getUser()));
//        $form->remove('user');
//        $this->dispatcher->dispatch(StartPlaceCreationEvent::NAME, new StartPlaceCreationEvent($form, $this->getUser(), $place));
//        dd($form->get);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();
            $this->addFlash('message', 'place.created');

            return $this->redirectToRoute('place_show', ['id' => $place->getId()]);
        }

        return $this->render('place/new.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Place $place
     * @param TranslatorInterface $translator
     * @return Response
     * @Security("place.getUsers().contains(user)")
     */
    public function show(Request $request, Place $place, TranslatorInterface $translator, DataTableFactory $tableFactory): Response
    {
        $table = $tableFactory->create()
            ->add('status', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/switch.html.twig',
                'label' => $translator->trans('PAID'),
            ])
            ->add('period', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell-date-m-Y.html.twig',
                'label' => $translator->trans('billPeriod'),
            ])
            ->add('type', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell.html.twig',
                'label' => $translator->trans('service.title'),
            ])
            ->add('amount', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell-amount.html.twig',
                'label' => $translator->trans('due'),
            ])
            ->add('actuallyPaid', TwigColumn::class, [
                'className' => 'text-center',
                'template' => 'tables/cell-amount.html.twig',
                'label' => $translator->trans('actuallyPaid'),
            ])
            ->add('date', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell-date.html.twig',
                'label' => $translator->trans('billDate'),
            ])
            ->add('payDateText', TwigColumn::class, [
                'className' => '',
                'template' => 'tables/cell-date.html.twig',
                'label' => $translator->trans('payDate'),
            ])
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

        //temp solution for version display
        preg_match('/\d+/', getcwd(), $matches);

        return $this->render('place/show.html.twig', ['place' => $place, 'datatable' => $table, 'release' => $matches[0] ?? '']);
    }


    /**
     * @param Request $request
     * @param Place $place
     * @return Response
     * @Security("place.getUsers().contains(user)")
     */
    public function edit(Request $request, Place $place): Response
    {
        $form = $this->createForm(PlaceType::class, $place);
        $this->dispatcher->dispatch(StartPlaceEditionEvent::NAME, new StartPlaceEditionEvent($form, $this->getUser(), $place));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('message', 'changes_saved');

            return $this->redirectToRoute('place_edit', ['id' => $place->getId()]);
        }

        return $this->render('place/edit.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Place $place
     * @return Response
     * @Security("place.getUsers().contains(user)")
     */
    public function delete(Request $request, Place $place): Response
    {
        if ($this->isCsrfTokenValid('delete'.$place->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($place);
            $em->flush();
            $this->addFlash('message', 'place.deleted');
        }

        return $this->redirectToRoute('my_places');
    }

    /**
     * @return iterable
     * @Template()
     */
    public function myPlaces(): iterable
    {
        return ['places' => $this->getUser()->getPlaces()];
    }

    /**
     * @param Request $request
     * @return Response
     */
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
            $this->addFlash('message', 'place.created');

            return $this->redirectToRoute('place_show',  ['id' => $place->getId()]);
        }

        return $this->render('place/new.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }
}
