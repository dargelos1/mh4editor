<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function indexAction()
    {
    	//Check if user is logged. If not redirect lo login, else show home panel
    	//return new RedirectResponse($this->generateUrl('mh4_login_frontend'));
        //return $this->render('DesignBundle:Login:login.html.twig');
        return new Response("Protected Area!!!",200);
    }
}
