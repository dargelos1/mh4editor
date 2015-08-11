<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
    public function loginAction()
    {

    	$authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();
	    $security = $this->container->get('security.context');

	    //$this->denyAccessUnlessGranted('ROLE_USER',null,'You must be logged to access this area.');

	    if($security->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->get('router')->generate('mh4_editor_homepage'));
        }else{
	        return $this->render(
	        	'DesignBundle:Login:login.html.twig',
	        	array(
	        		'last_username' => $lastUsername,
	        		'error'			=> $error
	        	)
	        );
	    }
    }

    public function checkLoginAction()
    {

    }
}
