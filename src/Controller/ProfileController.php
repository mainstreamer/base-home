<?php

namespace App\Controller;

use App\Entity\Place;
use App\Entity\Bill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller
{

    public function index()
    {
        return $this->redirectToRoute('my_places');

    }

    public function analytics()
    {
        return $this->render('content.html.twig');
    }
}
