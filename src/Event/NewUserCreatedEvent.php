<?php

namespace App\Event;

use App\Entity\Place;
use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NewUserCreatedEvent extends Event
{
    const NAME = 'user.created';

    protected $form;

    protected $user;

    protected $place;

    public function __construct(User $user)
    {
//        $this->form = $form;
        $this->user = $user;
//        $this->place = $place;
    }
//
//    public function getForm()
//    {
//        return $this->form;
//    }
//
//    public function getPlace()
//    {
//        return $this->place;
//    }

    public function getUser()
    {
        return $this->user;
    }
}
