<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Indication;
use App\Entity\Meter;
use App\Form\IndicationType;
use App\Repository\IndicationRepository;
use App\Services\FileUploader;
use App\Services\FileUploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndicationController extends AbstractController
{
    public function index(IndicationRepository $indicationRepository): Response
    {
        return $this->render('indication/index.html.twig', ['indications' => $indicationRepository->findAll()]);
    }

    public function new(Request $request, FileUploaderService $fileUploaderService): Response
    {
        $item = new Indication();
        $form = $this->createForm(IndicationType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $item->setFile( new File($this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($item->getFile())));

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('indication_index');
        }

        return $this->render('indication/new.html.twig', [
            'indication' => $item,
            'form' => $form->createView(),
        ]);
    }

    public function newIndicationForMeter(Request $request, Meter $meter, FileUploaderService $fileUploaderService): Response
    {
        $item = new Indication();
        $item->setMeter($meter);
        $form = $this->createForm(IndicationType::class, $item);
        $form->remove('meter');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($item->getFile()) {

                $item->setFile( new File($this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($item->getFile())));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('meter_show', ['id' => $meter->getId()]);
        }

        return $this->render('indication/new.html.twig', [
            'indication' => $item,
            'meter' => $meter,
            'form' => $form->createView(),
        ]);
    }

    public function show(Indication $indication): Response
    {
        return $this->render('indication/show.html.twig', ['indication' => $indication]);
    }

    public function edit(Request $request, Indication $indication, FileUploaderService $fileUploaderService): Response
    {
        if ($before = $indication->getFile()) {
            $indication->setFile( new File($indication->getFile()));
        }

        $form = $this->createForm(IndicationType::class, $indication);
        $form->remove('meter');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($indication->getFile() && !strpos($indication->getFile()->getPathName(), 'uploads_directory')) {
                $indication->setFile(new File($this->getParameter('uploads_directory').'/'.$fileUploaderService->upload($indication->getFile())));
            } elseif ($indication->getFile()) {
                $indication->setFile(new File($before));
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('indication_edit', ['id' => $indication->getId()]);
        }

        return $this->render('indication/edit.html.twig', [
            'indication' => $indication,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Indication $indication): Response
    {
        if ($this->isCsrfTokenValid('delete'.$indication->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($indication);
            $em->flush();
        }

        return $this->redirectToRoute('meter_show', ['id' => $indication->getMeter()->getId()]);
    }

    public function deleteFile(Indication $indication)
    {
        $indication->setFile(null);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse();
    }
}
