<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegisterController extends Controller
{
    public function showFormAction()
    {
        return $this->render('DesignBundle:Register:regform.html.twig');
    }
}
