<?php

namespace App\Event;

use App\Entity\Place;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class StartPlaceCreationEvent extends Event
{
    const NAME = 'place.creation.start';

    protected $form;

    protected $user;

    protected $place;

    public function __construct(FormInterface $form, UserInterface $user, Place $place)
    {
        $this->form = $form;
        $this->user = $user;
        $this->place = $place;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function getUser()
    {
        return $this->user;
    }
}
