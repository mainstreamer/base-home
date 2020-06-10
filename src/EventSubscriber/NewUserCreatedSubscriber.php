<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\TariffType;
use App\Event\NewUserCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewUserCreatedSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $registry)
    {

        $this->em = $registry;
    }

    public function onUserCreation(NewUserCreatedEvent $event): void
    {
        $tariffType = new TariffType();
        $tariffType->setName('денний');
        $event->getUser()->addTariffType($tariffType);
        $this->em->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
           'user.created' => 'onUserCreation',
        ];
    }
}
