<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use MH4Editor\MH4EditorBundle\Form\UserRegistrationType;
use MH4Editor\MH4EditorBundle\Form\Model\UserForm;
use MH4Editor\MH4EditorBundle\Entity\User;
use MH4Editor\MH4EditorBundle\Form\UserType;

class RegisterController extends Controller
{
    public function showFormAction()
    {
    	$userForm = new UserForm();
    	$form = $this->createForm(new UserRegistrationType(),$userForm);
        
    	$f = $form->createView();
        return $this->render('DesignBundle:Register:regform.html.twig',
        	array(
        		"form" => $form->createView()
        	)
        );
    }

    public function registerAction(){
    	
    	$em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $userForm = new UserForm();
        $form = $this->createForm(new UserRegistrationType(),$userForm);
        $form->handleRequest($request);


        if($form->isValid()){

        	$data = $form->getData();


        	$user = $data->getUser();

        	$em = $this->getDoctrine()->getEntityManager();
        	$user2 = $em->getRepository("MH4EditorBundle:User")->findBy(array("email" => $user->getEmail()));

        	if(count($user2) == 0){
                $user->setRole(); //By default sets to 0. Change it in DDBB
        		$em->persist($user);
        		$em->flush();
        		$message = "user ".$user->getUsername()." registered!";
        		$status = true;
        		
        	}else{
        		$message = "The username or email is in use!";
        		$status = false;
        	}
        	
			return new RedirectResponse($this->generateUrl('mh4_login_frontend'));

        	return new Response(
        		json_encode(
	    			array(
	    				"message" 	=> $message,
	    				"status"	=> $status
	    			)
	    		)
    		,200);

        	$password_confirm = $data->getPasswordConfirm();

        }else{
        	//$validator = $this->get("validator");
        	//$metadata = $validator->getMetaDataFor(new User());
        	//print_r($metadata);
        	//echo "ERRROR; ";
        	//echo  $form->getErrorsAsString(); //deprecated. Not working on Symfony 3
        	
        	//echo (string )$form->getErrors(true); //if true, main and child errors

        	//var_dump($form->getErrors(true));
        	$f = $form->getData();
        	//echo $f->getUser()->getUsername();die;

        	$childs = $form->getErrors(true); //get all errors. Returns an array of Form/Form
        	/*foreach ($childs as $c) {
        		echo $c->getMessage()."<br>";
        	}
        	die;*/
        	$errorResponse = array();
        	$errorResponse['formErrorObject'] = $this->recursiveFormError($form);
        	$errorResponse['status'] = false;

        	
	        return new Response(json_encode($errorResponse),200);
        }



        $params = $request->request->all();

        var_dump($params);die;
    }

    private function recursiveFormError($form){

		$childs = $form->all();

		foreach($childs as $child){
			
			$cName = $child->getName();
			if($child->count()>0){
				$errorList[$cName] = $this->recursiveFormError($child);
			}else{
				$childErrors = array();
				$errors = $child->getErrors(true);
				foreach ($errors as $error) {
					$childErrors[] = $error->getMessage();
					
				}

				$errorList[$cName] = $childErrors;
			}
		}

		return $errorList;
	}

}