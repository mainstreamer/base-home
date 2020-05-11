<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use App\Services\RatesFetcherService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(ServiceRepository $repository, RatesFetcherService $service): array
    {
        $rates = $service->execute();
        $ratesNbu = ['EUR' => $rates[2]->getSellRate(), 'USD' => $rates[3]->getSellRate(), 'UAH' => 1];
        $rates2['NBU']['BUY'] = $ratesNbu;
        $rates2['NBU']['SELL'] = $ratesNbu;
//        $response = $client->request('GET', 'http://api.privatbank.ua/p24api/pubinfo?exchange&json&coursid=11');
//        $result = $response->toArray();

        $privatRates['USD'] = round($rates[4]->getBuyRate(), 2).' / '.round($rates[4]->getSellRate(), 2);
        $privatRates['EUR'] = round($rates[5]->getBuyRate(), 2).' / '.round($rates[5]->getSellRate(), 2);
//        $privatRates['EUR'] = round($result[1]['buy'], 2).' / '.round($result[1]['sale'], 2);

//        $response = $client->request('GET', 'https://api.monobank.ua/bank/currency');
//        $result = $response->toArray();
        $monoRates['USD'] = round($rates[1]->getBuyRate(), 2).' / '.round($rates[1]->getSellRate(), 2);
        $monoRates['EUR'] = round($rates[0]->getBuyRate(), 2).' / '.round($rates[0]->getSellRate(), 2);
//        $monoRates['USD'] = round($result[0]['rateBuy'], 2).' / '.round($result[0]['rateSell'], 2);
//        $monoRates['EUR'] = round($result[1]['rateBuy'], 2).' / '.round($result[1]['rateSell'], 2);
        $rates2['PRIVATBANK']['BUY']['USD'] = round($rates[4]->getBuyRate(), 2);
        $rates2['PRIVATBANK']['SELL']['USD'] = round($rates[4]->getSellRate(), 2);
        $rates2['PRIVATBANK']['BUY']['EUR'] = round($rates[5]->getBuyRate(), 2);
        $rates2['PRIVATBANK']['SELL']['EUR'] = round($rates[5]->getSellRate(), 2);
        $rates2['MONOBANK']['BUY']['USD'] = round($rates[1]->getBuyRate(), 2);
        $rates2['MONOBANK']['SELL']['USD'] = round($rates[1]->getSellRate(), 2);
        $rates2['MONOBANK']['BUY']['EUR'] = round($rates[0]->getBuyRate(), 2);
        $rates2['MONOBANK']['SELL']['EUR'] = round($rates[0]->getSellRate(), 2);


        return [
            'services' => $repository->orderByNextBilling($this->getUser()),
            'rates' => $ratesNbu,
            'rates2' => $rates2,
//            'privatRates' => $rates->getPayload()['privat'],
            'privatRates' => $privatRates,
//            'monoRates' => $rates->getPayload()['mono'],
            'monoRates' => $monoRates,
        ];
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
