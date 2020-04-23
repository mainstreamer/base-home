<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ServiceController
 * @package App\Controller
 */
class ServiceController extends AbstractController
{
    /**
     * @param ServiceRepository $repository
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @Template("service/index.html.twig")
     */
    public function index(ServiceRepository $repository): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'http://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json');
        $result = $response->toArray();
        $eur = $result[33];
        $usd = $result[26];
        $rates = ['EUR' => $eur['rate'], 'USD' => $usd['rate'], 'UAH' => 1];
        $response = $client->request('GET', 'http://api.privatbank.ua/p24api/pubinfo?exchange&json&coursid=11');
        $result = $response->toArray();
        $privatRates['USD'] = round($result[0]['buy'], 2).' / '.round($result[0]['sale'], 2);
        $privatRates['EUR'] = round($result[1]['buy'], 2).' / '.round($result[1]['sale'], 2);

        return ['services' => $repository->orderByNextBilling($this->getUser()), 'rates' => $rates, 'privatRates' => $privatRates];
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(ServiceType::class, $service = new Service());
        $service->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();
            $this->addFlash('message', 'subscription.created');

            return $this->redirectToRoute('service_show', ['id' => $service->getId()]);
        }

        return $this->render('service/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Service $service
     * @return array
     * @Template("service/show.html.twig")
     */
    public function show(Service $service)
    {
        return ['service' => $service];
    }

    public function delete(Service $service)
    {
        $this->getDoctrine()->getManager()->remove($service);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('message', 'service.deleted');

        return $this->redirectToRoute('service_index');
    }

    /**
     * @param Request $request
     * @param Service $service
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(Request $request, Service $service)
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();
            $this->addFlash('message', 'subscription.changed');

            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/edit.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }
}
