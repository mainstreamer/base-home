<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function index()
    {
        $vars = ['a'=>'EMOJII'];


        return $this->render('default.html.twig', $vars);
    }
}