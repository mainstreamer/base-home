<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CardController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('card/index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(CardType::class, $card = (new Card())->setUser($this->getUser()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($card);
            $em->flush();

            return $this->redirectToRoute('card_index');
        }

        return $this->render('card/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Card $card
     * @return Response
     */
    public function edit(Request $request, Card $card): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('message', 'changes_saved');

            return $this->redirectToRoute('card_index');
        }

        return $this->render('card/edit.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Card $card
     * @return Response
     */
    public function delete(Request $request, Card $card): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($card);
            $em->flush();
            $this->addFlash('message', 'item_deleted');
        }

        return $this->redirectToRoute('card_index');
    }

//
//    /**
//     * @param FileUpload $file
//     * @return JsonResponse
//     * @Security(" (file.getBIll().getPlace() != null and file.getBill().getPlace().getUsers().contains(user)) or (file.getBill().getSubscription()!= null and user === file.getBill().getSubscription().getService().getUser())")
//     */
//    public function deleteFile(FileUpload $file)
//    {
//        $bill = $file->getBill();
//        $bill->removeFile($file);
//        $this->getDoctrine()->getManager()->flush();
//
//        return new JsonResponse();
//    }
//
//    /**
//     * @param Bill $bill
//     * @return JsonResponse
//     * @Security(" (bill.getPlace() != null and bill.getPlace().getUsers().contains(user)) or (bill.getSubscription()!= null and user === bill.getSubscription().getService().getUser())")
//     */
//    public function togglePayment(Bill $bill)
//    {
//        $bill->setIsPaid(!$bill->getisPaid());
//        if ($bill->getIsPaid()) {
//            $bill->setActuallyPaid($bill->getAmount());
//            $bill->setPayDate(new \DateTime());
//            $response = $bill->getPayDateText();
//        }
//        else {
//            $bill->setPayDate(null);
//            $bill->setActuallyPaid(null);
//            $response = '–––'; //TODO think about moving this away
//        }
//        $this->getDoctrine()->getManager()->flush();
//
//        return new JsonResponse( $response, Response::HTTP_OK);
//    }
//
//    /**
//     * @param Request $request
//     * @param Place $place
//     * @param FileUploaderService $fileUploaderService
//     * @return Response
//     */
//    public function newBillForSubscription(Request $request, Subscription $subscription, FileUploaderService $fileUploaderService): Response
//    {
//        $place = $subscription;
//        $bill = new Bill();
//        $bill->setSubscription($place);
//        $form = $this->createForm(BillType::class, $bill);
//        $form->remove('place');
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $bill->setFile(null);
//            $files = array_values($request->files->all()[$form->getName()])[0];
//
//            foreach ($files as $file)
//            {
//                $path = $this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($file);
//                $bill->addFile( new FileUpload($path));
//            }
//
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($bill);
//            $em->flush();
//            $this->addFlash('message', 'bill.created');
//
//            return $this->redirectToRoute('subscription_show', ['id' => $place->getId()]);
//        }
//
//        return $this->render('bill/new_for_subscription.html.twig', [
//            'bill' => $bill,
////            'place' => $place,
//            'form' => $form->createView(),
//        ]);
//    }

}
