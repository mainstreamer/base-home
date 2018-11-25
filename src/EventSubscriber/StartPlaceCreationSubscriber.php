<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StartPlaceCreationSubscriber implements EventSubscriberInterface
{
    public function onStartPlaceCreation($event): void
    {
        if (!$event->getForm()->isSubmitted() && !$event->getUser()->hasRole('ROLE_ADMIN')) {
            $event->getPlace()->setUser($event->getUser());
            $event->getForm()->remove('user');
        }
    }

    public static function getSubscribedEvents()
    {
        return [
           'place.creation.start' => 'onStartPlaceCreation',
        ];
    }
}
