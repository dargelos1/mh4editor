<?php

namespace MH4Editor\Fort42Bundle\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function testAction(Request $request)
    {
    	$user_data = fort42_getUsername();
    	ladybug_dump($user_data);
        return $this->render('Fort42Bundle:Default:index.html.twig', array('name' => $user_data['user']));
    }
}
