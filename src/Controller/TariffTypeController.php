<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Meter;
use App\Entity\Tariff;
use App\Entity\TariffType;
use App\Entity\User;
use App\Form\TariffTypeType;
use App\Repository\TariffTypeRepository;
use Omines\DataTablesBundle\Controller\DataTablesTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TariffTypeController extends Controller
{
    use DataTablesTrait;

    public function index(TariffTypeRepository $repo): Response
    {
        return $this->render('tariff_type/index.html.twig', ['tariffTypes' => $repo->findAll()]);
    }

    /**
     * @param Request $request
     * @return Response
     * TODO remove
     */
    public function new(Request $request): Response
    {
        $item = new TariffType();
        $form = $this->createForm(TariffTypeType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            $this->addFlash('message', 'tariff_types.created');

            return $this->redirectToRoute('tariff_type_index');
        }

        return $this->render('tariff_type/new.html.twig', [
            'tariff' => $item,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param User $userObject
     * @return Response
     * @Security("user === userObject")
     */
    public function newForUser(Request $request, User $userObject): Response
    {
        $item = new TariffType();
        $item->setUser($userObject);
        $form = $this->createForm(TariffTypeType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            $this->addFlash('message', 'tariff_types.created');

            return $this->redirectToRoute('tariff_index');
        }

        return $this->render('tariff_type/new.html.twig', [
            'tariff' => $item,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Meter $meter
     * @return Response
     * @Security("meter.getPlace().getUsers().contains(user)")
     */
    public function newTariffTypeAndTariffForMeter(Request $request, Meter $meter): Response
    {
//        $user = $meter->getPlace()->getUser();
        $user = $this->getUser();
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

    /**
     * @param TariffType $tariff
     * @return Response
     * @Security("user === tariff.getUser()")
     */
    public function show(TariffType $tariff): Response
    {
        return $this->render('tariff/show.html.twig', ['tariff' => $tariff]);
    }

    /**
     * @param Request $request
     * @param TariffType $tariff
     * @return Response
     * @Security("user === tariff.getUser()")
     */
    public function edit(Request $request, TariffType $tariff): Response
    {
        $form = $this->createForm(TariffTypeType::class, $tariff);
        $form->remove('place');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('message', 'changes_saved');

            return $this->redirectToRoute('tariff_type_edit', ['id' => $tariff->getId()]);
        }

        return $this->render('tariff_type/edit.html.twig', [
            'tariff' => $tariff,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param TariffType $tariffType
     * @return Response
     * @Security("user === tariffType.getUser()")
     */
    public function delete(Request $request, TariffType $tariffType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tariffType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tariffType);
            $em->flush();
            $this->addFlash('message', 'tariff_types.deleted');
        }

        return $this->redirectToRoute('tariff_index');
    }
}
