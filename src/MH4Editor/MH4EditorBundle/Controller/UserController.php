<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MH4Editor\MH4EditorBundle\Form\UserType;

class UserController extends Controller
{
    public function registerAction()
    {
    	$form = $this->createForm(new UserType());
        return $this->render('DesignBundle:Register:regform.html.twig',
        	array("form" => $form->createView());
        );
    }
}