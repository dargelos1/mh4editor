<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MH4Editor\MH4EditorBundle\Form\UserType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\FileBag;
use MH4Editor\MH4EditorBundle\Entity\User;

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
        $em = $this->getDoctrine()->getManager();
    	$user = $this->getUser();
    	$response = array();
    	$response["status"] = false;
        $translator = $this->get("translator");

    	if($user && $request->isXMLHttpRequest()){

    		$data = $this->getRequest()->request->all();
    		$file = $request->files->get("mh4-user-file");


    		if($file !== null){

    			if($file->isValid()){
                    
	    			$file->move($user->getUploadDir(),$file->getClientOriginalName());
	    			if(file_exists( ($user->getUploadDir().'/'.$file->getClientOriginalName() ) )){
	    				$response["user"] = $user->getUsername();
	    				$response["status"] = true;
	    				$response["fileMessage"] = $translator->trans("The save file has been stored sucessfully.");
	    				$mh4Cipher = $this->get("mh4_cipher");
        				$mh4Cipher->MH4Decrypt($user->getUploadDir().'/'.$file->getClientOriginalName() ,$user->getUploadDir()."/decrypted.bin",$this);

                        $HR = $mh4Cipher->getHunterRanking($user);
                        $HRUSer = $user->getHunterHR();

                        if($HR !== $HRUSer){
                            $user->setHunterHR($HR);
                            $user->setMaxTalismansQuota(intval(($HR * 0.1) + User::TALISMAN_QUOTA_BASE));
                            $user->setMaxItemsQuota(intval(($HR * 5) + User::ITEM_QUOTA_BASE));
                            $incTalismanQuota = $user->getMaxTalismansQuota() - $user->getTalismansQuota();
                            $incItemQuota = $user->getMaxItemsQuota() - $user->getItemsQuota();
                            $user->setTalismansQuota($user->getTalismansQuota() + $incTalismanQuota);
                            $user->setItemsQuota($user->getItemsQuota() + $incItemQuota);
                        }
                        
                        if($user->getMH4FilePath() === NULL || $user->getMH4FilePath() == ""){
                            $user->setMH4FilePath($user->getDefaultSavePath().'/'.$file->getClientOriginalName());
                        }

                        $em->persist($user);
                        $em->flush();
                        

	    			}else{
	    				$response["fileMessage"] = $translator->trans("The file could not be stored in server. Conntact with the administrator.");
	    			}
    			}else{
    				$response["fileMessage"] = $translator->trans("An error ocurred while uploading the file...");
    			}
    			
    		}else{
    			$response["fileMessage"] = $translator->trans("An error ocurred while uploading the file... Maybe the file is corrupted.");
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
            $em = $this->getDoctrine()->getManager();
            $translator = $this->get("translator");
    		if(file_exists($user->getAbsolutePath())){
                $user->setLastDownloadRequest(new \DateTime());
                $em->persist($user);
                $em->flush();
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
    			$response['fileMessage'] = $translator->trans("Sorry your userX file doesn't exists in our server. Upload it first.");
    		}
    		


    	}

    	return  $this->redirect($this->generateUrl('mh4_login_frontend'));

    }

    public function confirmTokenAction(Request $request, $token){

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("MH4EditorBundle:User")->findBy(array("confirmationToken" => $token));
        $response = array(
            "status" => false,
            "message"=> "Invalid token"
        );

        if(count($user) > 0 && $user[0] instanceof User){

            $user = $user[0];
            $user->setConfirmationToken(null);
            $em->persist($user);
            $em->flush();
            
            return $this->render(
                'DesignBundle:Email:email_confirmed.html.twig',
                array(
                    "status" => true,
                    "message" => "Confirmation success!",
                    "user" => $user->getUsername(),
                )
            );

        }else{

        }

        return new Response(json_encode($response),200);

    }

    public function saveCharacterInfoAction(Request $request)
    {

        $response = array("status" => false);

        if($request->isXMLHttpRequest()){

            try{

                $data = $request->request->all();

                $user = $this->getUser();
                $mh4Cipher = $this->get("mh4_cipher");

                $mh4Cipher->setHunterName($user,$data['charName']);
                $mh4Cipher->setSex($user,$data['charGen']);

                $response['status'] = true;
                $response['msg'] = 'Done';
                $response['newName'] = $data['charName'];
            }catch(\Exception $e){

                $response['msg'] = 'Internal Server Error';
            }
            
        }

        return new Response(json_encode($response),200);
    }
}