<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\Place;
use App\Form\BillType;
use App\Repository\BillRepository;
use App\Services\FileUploaderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BillController extends AbstractController
{
    public function index(BillRepository $billRepository): Response
    {
        return $this->render('bill/index.html.twig', ['bills' => $billRepository->findAll()]);
    }

    public function new(Request $request): Response
    {
        $bill = new Bill();
        $form = $this->createForm(BillType::class, $bill);
//        dump($bill);exit;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
//            dump($bill);exit;
            $em->persist($bill);
            $em->flush();

            return $this->redirectToRoute('bill_index');
        }

        return $this->render('bill/new.html.twig', [
            'bill' => $bill,
            'form' => $form->createView(),
        ]);
    }

    public function newBillForPlace(Request $request, Place $place, FileUploaderService $fileUploaderService): Response
    {
        $bill = new Bill();
        $bill->setPlace($place);
        $form = $this->createForm(BillType::class, $bill);
        $form->remove('place');
//        dump($bill);exit;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($bill->getFile()) {
                $bill->setFile( new File($this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($bill->getFile())));
            }

            $em = $this->getDoctrine()->getManager();
//            dump($bill);exit;
            $em->persist($bill);
            $em->flush();

            return $this->redirectToRoute('place_show', ['id' => $place->getId()]);
        }

        return $this->render('bill/new.html.twig', [
            'bill' => $bill,
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    public function show(Bill $bill): Response
    {
        return $this->render('bill/show.html.twig', ['bill' => $bill]);
    }

    public function edit(Request $request, Bill $bill, FileUploaderService $fileUploaderService): Response
    {

        if ($before = $bill->getFile()) {
            $bill->setFile( new File($bill->getFile()));
        }
        $form = $this->createForm(BillType::class, $bill);
        $form->remove('place');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//dump($bill->getFile());exit;
            if ($bill->getFile() && !strpos($bill->getFile()->getPathName(), 'uploads_directory')) {
//            if ($bill->getFile()) {

                $bill->setFile(new File($this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($bill->getFile())));
            }  elseif ($bill->getFile()) {
                $bill->setFile(new File($before));
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bill_edit', ['id' => $bill->getId()]);
        }

        return $this->render('bill/edit.html.twig', [
//        return $this->render('bill/new.html.twig', [
            'bill' => $bill,
            'form' => $form->createView()
        ]);
    }

    public function delete(Request $request, Bill $bill): Response
    {
        $place = $bill->getPlace()->getId();
        if ($this->isCsrfTokenValid('delete'.$bill->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bill);
            $em->flush();
        }

        return $this->redirectToRoute('place_show', ['id' => $place]);
    }

    public function deleteFile(Bill $bill)
    {
        $bill->setFile(null);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse();
    }

    /**
     * @param Request $request
     * @param Bill $bill
     * @return JsonResponse
     * @Security("user === bill.getPlace().getUser()")
     */

    public function togglePayment(Bill $bill)
    {
        $bill->setStatus($bill->getStatus() === Bill::PAID ? Bill::UNPAID : Bill::PAID);
        if ($bill->getStatus() === Bill::PAID) {
            $bill->setActuallyPaid($bill->getAmount());
            $bill->setPayDate(new \DateTime());
            $response = $bill->getPayDateText();
        }
        else {
            $bill->setPayDate(null);
            $bill->setActuallyPaid(null);
            $response = '–––'; //TODO think about moving this away
        }
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse( $response, Response::HTTP_OK);
    }
}
