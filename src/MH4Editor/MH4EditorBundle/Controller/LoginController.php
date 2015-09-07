<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller
{
    public function loginAction(Request $request)
    {

    	$em = $this->getDoctrine()->getManager();
    	$authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();
	    $security = $this->container->get('security.context');
	    $mh4Cipher = $this->get("mh4_cipher");

	    //$this->denyAccessUnlessGranted('ROLE_USER',null,'You must be logged to access this area.');

	    $user = $this->getUser();
	    $isBanned = ($user !== null ) ? $user->getIsBanned() : false;

	    if($security->isGranted('ROLE_USER')) {

	    	$UA = $request->headers->get('User-Agent');
	    	$IP = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
	    	$user->setLastIP($IP);
	    	$user->setLastLogin(new \DateTime());
	    	$user->setLastUserAgent($UA);

	    	if($user->getMH4FilePath() !== null && file_exists($user->getUploadRootDir()."/".$user->getMH4FilePath())){

                
                if(!file_exists($user->getUploadRootDir()."/".$user->getUploadDir()."/decrypted.bin")){

                    $status = $mh4Cipher->MH4Decrypt($user->getAbsolutePath() ,$user->getUploadDir()."/decrypted.bin",$this);
                    if($status){

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

                        
                    }

                }else{
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
                }
            }
            
	    	$em->persist($user);
        	$em->flush();
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
