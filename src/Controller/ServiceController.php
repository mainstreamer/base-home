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
        if (!$rates) {
            $v = 1;
        }
            $ratesNbu = ['EUR' => $v ?? $rates[2]->getSellRate(), 'USD' => $v ?? $rates[3]->getSellRate(), 'UAH' => 1];
            $rates2['NBU']['BUY'] = $ratesNbu;
            $rates2['NBU']['SELL'] = $ratesNbu;
//        $response = $client->request('GET', 'http://api.privatbank.ua/p24api/pubinfo?exchange&json&coursid=11');
//        $result = $response->toArray();

            $privatRates['USD'] = $v ??  round($rates[4]->getBuyRate(), 2).' / '.round($rates[4]->getSellRate(), 2);
            $privatRates['EUR'] = $v ??  round($rates[5]->getBuyRate(), 2).' / '.round($rates[5]->getSellRate(), 2);
//        $privatRates['EUR'] = round($result[1]['buy'], 2).' / '.round($result[1]['sale'], 2);

//        $response = $client->request('GET', 'https://api.monobank.ua/bank/currency');
//        $result = $response->toArray();
            $monoRates['USD'] = $v ?? round($rates[1]->getBuyRate(), 2).' / '.round($rates[1]->getSellRate(), 2);
            $monoRates['EUR'] = $v ?? round($rates[0]->getBuyRate(), 2).' / '.round($rates[0]->getSellRate(), 2);
//        $monoRates['USD'] = round($result[0]['rateBuy'], 2).' / '.round($result[0]['rateSell'], 2);
//        $monoRates['EUR'] = round($result[1]['rateBuy'], 2).' / '.round($result[1]['rateSell'], 2);
            $rates2['PRIVATBANK']['BUY']['USD'] = $v ??  round($rates[4]->getBuyRate(), 2);
            $rates2['PRIVATBANK']['SELL']['USD'] = $v ??  round($rates[4]->getSellRate(), 2);
            $rates2['PRIVATBANK']['BUY']['EUR'] = $v ?? round($rates[5]->getBuyRate(), 2);
            $rates2['PRIVATBANK']['SELL']['EUR'] = $v ?? round($rates[5]->getSellRate(), 2);
            $rates2['MONOBANK']['BUY']['USD'] = $v ?? round($rates[1]->getBuyRate(), 2);
            $rates2['MONOBANK']['SELL']['USD'] = $v ?? round($rates[1]->getSellRate(), 2);
            $rates2['MONOBANK']['BUY']['EUR'] = $v ?? round($rates[0]->getBuyRate(), 2);
            $rates2['MONOBANK']['SELL']['EUR'] = $v ?? round($rates[0]->getSellRate(), 2);

        $all = $repository->orderByNextBilling($this->getUser());
        $due = $repository->orderByNextBillingDue($this->getUser());

        foreach ($due as $service) {
            unset($all[array_search($service, $all)]);
        }

        return [
            'services' => $all,
    //            'services' => $repository->orderByNextBilling($this->getUser()),
//            'services_due' => $repository->orderByNextBillingDue($this->getUser()),
            'services_due' => $due,
            'rates' => $ratesNbu ?? null,
            'rates2' => $rates2 ?? null,
//            'privatRates' => $rates->getPayload()['privat'],
            'privatRates' => $privatRates ?? null,
//            'monoRates' => $rates->getPayload()['mono'],
            'monoRates' => $monoRates ?? null,
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
