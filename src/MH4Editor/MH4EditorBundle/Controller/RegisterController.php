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
        $user = $this->getUser();
        if($user){
            return new RedirectResponse($this->generateUrl('mh4_login_frontend'));
        }
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

                $mh4Cipher = $this->get("mh4_cipher");

                $user->setRole(); //By default sets to 0. Change it in DDBB
                $user->setIsBanned(false);
                $user->setLocale("en");
                $UA = $request->headers->get('User-Agent');
                $IP = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
                $user->setLastIP($IP);
                $user->setRegisterDate(new \DateTime());
                $user->setLastUserAgent($UA);

                $user->setMaxTalismansQuota(0);
                $user->setMaxItemsQuota(0);
                $user->setTalismansQuota(0);
                $user->setItemsQuota(0);
                $user->setHunterHR(0);

                $em->persist($user);
                $em->flush();

                if($user->getMH4FilePath() !== null && file_exists($user->getUploadRootDir()."/".$user->getMH4FilePath())){

                    
                    if(!file_exists($user->getUploadRootDir()."/".$user->getUploadDir()."/decrypted.bin")){

                        $status = $mh4Cipher->MH4Decrypt($user->getAbsolutePath() ,$user->getUploadDir()."/decrypted.bin",$this);
                        if($status){

                            $HR = $mh4Cipher->getHunterRanking($user);
                            $user->setHunterHR($HR);
                            $user->setMaxTalismansQuota(intval(($HR * 0.1) + User::TALISMAN_QUOTA_BASE));
                            $user->setMaxItemsQuota(intval(($HR * 5) + User::ITEM_QUOTA_BASE));
                            $user->setTalismansQuota($user->getMaxTalismansQuota());
                            $user->setItemsQuota($user->getMaxItemsQuota());
                        }

                    }else{
                            $HR = $mh4Cipher->getHunterRanking($user);
                            $user->setHunterHR($HR);
                            $user->setMaxTalismansQuota(intval(($HR * 0.1) + User::TALISMAN_QUOTA_BASE));
                            $user->setMaxItemsQuota(intval(($HR * 5) + User::ITEM_QUOTA_BASE));
                            $user->setTalismansQuota($user->getMaxTalismansQuota());
                            $user->setItemsQuota($user->getMaxItemsQuota());
                    }
                }
                
                //$mailer = $this->get('mailer');
                //================================
                $user->setConfirmationToken($this->generateToken());
                $mail = new \PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'xo6.x10hosting.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'support@mh4editor.x10host.com';
                $mail->Password = 'support@mh4editor';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('support@mh4editor.x10host.com','MH4Editor');
                $mail->addAddress($user->getEmail());
                $mail->isHTML(true);

                $mail->Subject = "Register Confirmation";
                $mail->Body = $this->renderView(
                    'DesignBundle:Email:email_registration.html.twig',
                    array(
                        "user" => $user->getUsername(),
                        "link" => $this->get('router')->generate('mh4_confirm_token', array('token' => $user->getConfirmationToken() ),true)
                    )
                );

                if($mail->send()){

                    $em->persist($user);
                    $em->flush();
            		$message = "user ".$user->getUsername()." registered!";
            		$status = true;
                    $this->get("session")->getFlashBag()->set("reg_success","Your account has been created! A confirmation email has been sended to your email account.");
                }else{
                    $message = "Confirmation email could not be sended due an internal error. Please contact with the Administrator.";
                    $status = false;
                    $this->get("session")->getFlashBag()->set("reg_failure",$message);
                }
        		
        	}else{
        		$message = "The username or email is in use!";
        		$status = false;
        	}
        	
			//return new RedirectResponse($this->generateUrl('mh4_login_frontend'));

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

    private function generateToken($tokenLen = 30){

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#_";
        $len = strlen($chars);
        $token = '';
        for($i=0;$i<$tokenLen;$i++){

            $idx = mt_rand(0,$len-1);
            $token .= $chars[$idx];
        }

        return $token;
    }

}