<?php

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\Service;
use App\Entity\Subscription;
use App\Form\SubscriptionType;
use App\Repository\BillRepository;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SubscriptionController
 * @package App\Controller
 */
class SubscriptionController extends AbstractController
{
    /**
     * @return array
     * @Template("subscription/index.html.twig")
     */
    public function index()
    {
        return ['user' => $this->getUser()];
    }

    /**
     * @param Request $request
     * @param Service $service
     * @return Response
     */
    public function new(Request $request, Service $service): Response
    {
        $form = $this->createForm(SubscriptionType::class, $subscription = new Subscription());
        $subscription->setService($service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscription);
            $em->flush();
            $this->addFlash('message', 'subscription.created');

            return $this->redirectToRoute('service_show', ['id' => $service->getId()]);
        }

        return $this->render('subscription/new.html.twig', [
            'subscription' => $subscription,
            'form' => $form->createView(),
        ]);
    }

    /** @param Subscription $subscription
     * @Template("subscription/show.html.twig")
     * @return array
     */
    public function show(Request $request, Subscription $subscription, BillRepository $repo, DataTableFactory $tableFactory, TranslatorInterface $translator)
    {
//        $bills = $repo->findBySubscription($subscription);
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

                'query' => function (QueryBuilder $builder) use ($subscription) {
                    $builder
                        ->select('bill')
                        ->from(Bill::class, 'bill')
                        ->where('bill.subscription = :id')
                        ->setParameter('id', $subscription->getId())
                    ;
                },
            ])->addOrderBy('date', 'DESC')
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }



        return ['subscription' => $subscription, 'datatable' => $table];
    }

    /**
     * @param Request $request
     * @param Subscription $subscription
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(Request $request, Subscription $subscription)
    {
        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscription);
            $em->flush();
            $this->addFlash('message', 'subscription.changed');

            return $this->redirectToRoute('service_show', ['id' => $subscription->getService()->getId()]);
        }

        return $this->render('subscription/edit.html.twig', [
            'subscription' => $subscription,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Subscription $subscription)
    {
        $this->getDoctrine()->getManager()->remove($subscription);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('message', 'subscription.deleted');

        return $this->redirectToRoute('service_index');
    }
}
