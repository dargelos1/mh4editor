<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function indexAction()
    {
    	$user = $this->getUser();

        $mh4Cipher = $this->get("mh4_cipher");
        $mh4Cipher->MH4Decrypt($user->getAbsolutePath() ,$user->getUploadDir()."/decrypted.bin",$this);
        $hunterName = $mh4Cipher->getHunterName($user);
        $zenies = $mh4Cipher->getZenies($user);
        $HR = $mh4Cipher->getHunterRanking($user);
        $itemBox = $mh4Cipher->getItemBox($user);
        //$mh4Cipher->cheatSetAllBoxItems($user);
        //$readed = $mh4Cipher->setItemBoxAtSlot(15,99,878,$user); //megazumos
        $readed = $mh4Cipher->setItemBoxAtSlot(1294,99,879,$user); //piel plateada garuga
        $readed = $mh4Cipher->setZenies(9999999,$user);
        //echo "Bytes readed: ".$readed;
        //die;
    	return $this->render(
	        	'DesignBundle:Frontend:home.html.twig',
	        	array(
	        		"hunterName"    => $hunterName,
                    "zenies"        => $zenies,
                    "hunterRank"    => $HR,
                    "itemBox"       => $itemBox,
	        	)
	        );
        //return new Response("Protected Area!!!".$user->getUsername(),200);
    }
}
