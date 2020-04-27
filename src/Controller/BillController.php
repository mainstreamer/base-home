<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\FileUpload;
use App\Entity\Place;
use App\Entity\Subscription;
use App\Form\BillType;
use App\Repository\BillRepository;
use App\Services\FileUploaderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BillController extends AbstractController
{
    /**
     * @param BillRepository $billRepository
     * @return Response
     * TODO do I need it at all?
     */
    public function index(BillRepository $billRepository): Response
    {
        return $this->render('bill/index.html.twig', ['bills' => $billRepository->findAll()]);
    }

    /**
     * @param Request $request
     * @return Response
     * TODO do I need it at all?
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
     * @param Request $request
     * @param Place $place
     * @param FileUploaderService $fileUploaderService
     * @return Response
     * @Security("place.getUsers().contains(user)")
     */
    public function newBillForPlace(Request $request, Place $place, FileUploaderService $fileUploaderService): Response
    {
        $bill = new Bill();
        $bill->setPlace($place);
        $form = $this->createForm(BillType::class, $bill);
        $form->remove('place');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $bill->setFile(null);
            $files = array_values($request->files->all()[$form->getName()])[0];

            foreach ($files as $file)
            {
                $path = $this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($file);
                $bill->addFile( new FileUpload($path));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->flush();
            $this->addFlash('message', 'bill.created');

            return $this->redirectToRoute('place_show', ['id' => $place->getId()]);
        }

        return $this->render('bill/new.html.twig', [
            'bill' => $bill,
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Bill $bill
     * @return Response
     * @Security("bill.getPlace() !== null and bill.getPlace().getUsers().contains(user) or bill.getSubscription()")
     */
    public function show(Bill $bill): Response
    {
        return $this->render('bill/show.html.twig', ['bill' => $bill]);
    }

    /**
     * @param Request $request
     * @param Bill $bill
     * @param FileUploaderService $fileUploaderService
     * @return Response
     * @Security(" bill.getPlace() !== null and bill.getPlace().getUsers().contains(user) or bill.getSubscription()")
     */
    public function edit(Request $request, Bill $bill, FileUploaderService $fileUploaderService): Response
    {
        $form = $this->createForm(BillType::class, $bill);
        $form->remove('place');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $bill->setFile(null);
            $files = array_values($request->files->all()[$form->getName()])[0];

            foreach ($files as $file) {
                $path = $this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($file);
                $newFile = new FileUpload($path);
                $newFile->setOriginalName($file->getClientOriginalName());
                $bill->addFile($newFile);
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('message', 'changes_saved');

            return $this->redirectToRoute('bill_edit', ['id' => $bill->getId()]);
        }

        return $this->render('bill/edit.html.twig', [
            'bill' => $bill,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param Bill $bill
     * @return Response
     * @Security(" (bill.getPlace() != fnull and bill.getPlace().getUsers().contains(user)) or (bill.getSubscription()!= null and user === bill.getSubscription().getService().getUser())")
     */
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

    /**
     * @param FileUpload $file
     * @return JsonResponse
     * @Security(" (file.getBIll().getPlace() != null and file.getBill().getPlace().getUsers().contains(user)) or (file.getBill().getSubscription()!= null and user === file.getBill().getSubscription().getService().getUser())")
     */
    public function deleteFile(FileUpload $file)
    {
        $bill = $file->getBill();
        $bill->removeFile($file);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse();
    }

    /**
     * @param Bill $bill
     * @return JsonResponse
     * @Security(" (bill.getPlace() != null and bill.getPlace().getUsers().contains(user)) or (bill.getSubscription()!= null and user === bill.getSubscription().getService().getUser())")
     */
    public function togglePayment(Bill $bill)
    {
        $bill->setIsPaid(!$bill->getisPaid());
        if ($bill->getIsPaid()) {
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

    /**
     * @param Request $request
     * @param Place $place
     * @param FileUploaderService $fileUploaderService
     * @return Response
     */
    public function newBillForSubscription(Request $request, Subscription $subscription, FileUploaderService $fileUploaderService): Response
    {
        $place = $subscription;
        $bill = new Bill();
        $bill->setSubscription($place);
        $form = $this->createForm(BillType::class, $bill);
        $form->remove('place');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $bill->setFile(null);
            $files = array_values($request->files->all()[$form->getName()])[0];

            foreach ($files as $file)
            {
                $path = $this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($file);
                $bill->addFile( new FileUpload($path));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->flush();
            $this->addFlash('message', 'bill.created');

            return $this->redirectToRoute('subscription_show', ['id' => $place->getId()]);
        }

        return $this->render('bill/new_for_subscription.html.twig', [
            'bill' => $bill,
//            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

}
