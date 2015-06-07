<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function loginAction()
    {
        return $this->render('DesignBundle:Login:login.html.twig');
    }
}
