<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MH4Editor\MH4EditorBundle\Form\UserType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\FileBag;

class UserController extends Controller
{
    public function registerAction()
    {
    	$form = $this->createForm(new UserType());
        return $this->render('DesignBundle:Register:regform.html.twig',
        	array("form" => $form->createView())
        );
    }

    public function fileUploadAction(Request $request)
    {
    	$user = $this->getUser();
    	$response = array();
    	$response["status"] = false;

    	if($user && $request->isXMLHttpRequest()){

    		$data = $this->getRequest()->request->all();
    		$file = $request->files->get("mh4-user-file");

    		if($file !== null){

    			if($file->isValid()){
	    			$file->move($user->getUploadDir(),$file->getClientOriginalName());
	    			if(file_exists( ($user->getUploadDir().'/'.$file->getClientOriginalName() ) )){
	    				$response["user"] = $user->getUsername();
	    				$response["status"] = true;
	    				$response["fileMessage"] = "The save file has been stored sucessfully.";
	    				$mh4Cipher = $this->get("mh4_cipher");
        				$mh4Cipher->MH4Decrypt($user->getUploadDir().'/'.$file->getClientOriginalName() ,$user->getUploadDir()."/decrypted.bin",$this);
	    			}else{
	    				$response["fileMessage"] = "The file could not be stored in server. Conntact with the administrator.";
	    			}
    			}else{
    				$response["fileMessage"] = "An error ocurred while uploading the file...";
    			}
    			
    		}else{
    			$response["fileMessage"] = "An error ocurred while uploading the file... Maybe the file is corrupted.";
    		}

	    }
	    return new Response(json_encode($response),200);
    }

    public function fileDownloadAction(Request $request){

    	/* Improve returning a flashPArameterBag!!*/
    	$user = $this->getUser();
    	$response = array();
    	$response["status"] = false;

    	if($user){

    		if(file_exists($user->getAbsolutePath())){

    			$mh4Cipher = $this->get("mh4_cipher");
				$mh4Cipher->MH4Encrypt($user->getUploadDir().'/decrypted.bin',$user->getUploadDir().'/'.basename($user->getAbsolutePath()) );

    			$headers = array(
	    			'Content-Type:' => 'application/octet-stream',
	    			'Content-Transfer-Encoding' => 'Binary',
	    			'Content-disposition' => 'attachment; filename="'.basename($user->getAbsolutePath()).'"'
	    		);
	    		$file = fopen($user->getAbsolutePath(),"rb");
	    		$content = fread($file,filesize($user->getAbsolutePath()));
	    		fclose($file);
	    		$response = new Response('',200,$headers);

	    		$response->setContent($content);
	    		return $response;
    		}else{
    			$response['fileMessage'] = "Sorry your userX file doesn't exists in our server. Upload first.";
    		}
    		


    	}

    	return  $this->redirect($this->generateUrl('mh4_login_frontend'));

    }
}