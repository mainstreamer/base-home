<?php

namespace App\Controller;

use App\Entity\Place;
use App\Entity\Bill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller
{

    public function index()
    {
//        exit;
        $vars = ['a'=>'EMOJII'];



        return $this->render('content.html.twig', $vars);
    }
}
