<?php

namespace App\Controller;

use App\Entity\Bill;
use App\Form\BillType;
use App\Repository\BillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bill")
 */
class BillController extends AbstractController
{
    /**
     * @Route("/", name="bill_index", methods="GET")
     */
    public function index(BillRepository $billRepository): Response
    {
        return $this->render('bill/index.html.twig', ['bills' => $billRepository->findAll()]);
    }

    /**
     * @Route("/new", name="bill_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $bill = new Bill();
        $form = $this->createForm(BillType::class, $bill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->flush();

            return $this->redirectToRoute('bill_index');
        }

        return $this->render('bill/new.html.twig', [
            'bill' => $bill,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bill_show", methods="GET")
     */
    public function show(Bill $bill): Response
    {
        return $this->render('bill/show.html.twig', ['bill' => $bill]);
    }

    /**
     * @Route("/{id}/edit", name="bill_edit", methods="GET|POST")
     */
    public function edit(Request $request, Bill $bill): Response
    {
        $form = $this->createForm(BillType::class, $bill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bill_edit', ['id' => $bill->getId()]);
        }

        return $this->render('bill/edit.html.twig', [
            'bill' => $bill,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bill_delete", methods="DELETE")
     */
    public function delete(Request $request, Bill $bill): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bill->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bill);
            $em->flush();
        }

        return $this->redirectToRoute('bill_index');
    }
}
