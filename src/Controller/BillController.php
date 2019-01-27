<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\FileUpload;
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

            $bill->setFile(null);
//            dump($bill);exit;
//            dump($form->all()['date']);exit;
            $files = array_values($request->files->all()[$form->getName()])[0];

            foreach ($files as $file)
            {
                $path = $this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($file);
                $bill->addFile( new FileUpload($path));

//                ( $this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($bill->getFile())))
            }
//            dump($form->getName());exit;
//            dump();exit;
//            dump($request->files->all()[$form->getName()]['file']);exit;
//            if ($bill->getFile()) {
//                $bill->setFile( new File($this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($bill->getFile())));
//            }

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

//        dump(
//            $request->files->all()['bill']['file']
//        );exit;

        /*if ($before = $bill->getFile()) {
            foreach ($before as $file) {
              $tmp[] = new File($file);
            }
            $bill->setFile($tmp);
//                new File($bill->getFile())
        }
        */

        $form = $this->createForm(BillType::class, $bill);
        $form->remove('place');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $bill->setFile(null);
//            dump($bill);exit;
//            dump($form->all()['date']);exit;
            $files = array_values($request->files->all()[$form->getName()])[0];

            foreach ($files as $file)
            {
                $path = $this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($file);

                $newFile = new FileUpload($path);
                $newFile->setOriginalName($file->getClientOriginalName());

                $bill->addFile($newFile);

//                ( $this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($bill->getFile())))
            }


//            $bill->setFile($request->files->all()['bill']['file']);

           /* if ($bill->getFile() && !strpos($bill->getFile()->getPathName(), 'uploads_directory')) {
                $bill->setFile(new File($this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($bill->getFile())));
            }  elseif ($bill->getFile()) {
                $bill->setFile(new File($before));
            }*/

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bill_edit', ['id' => $bill->getId()]);
        }

        return $this->render('bill/edit.html.twig', [
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

//    public function deleteFile(Bill $bill)
    public function deleteFile(FileUpload $file)
    {
        $bill = $file->getBill();
        $bill->removeFile($file);
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
//        $bill->setStatus($bill->getStatus() === Bill::PAID ? Bill::UNPAID : Bill::PAID);
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
}
