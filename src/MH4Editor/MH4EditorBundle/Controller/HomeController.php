<?php

namespace MH4Editor\MH4EditorBundle\Controller;
set_time_limit (0 );
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function indexAction()
    {
    	$user = $this->getUser();

        $mh4Cipher = $this->get("mh4_cipher");
        if(!file_exists($user->getUploadDir()."/decrypted.bin")){
            $mh4Cipher->MH4Decrypt($user->getAbsolutePath() ,$user->getUploadDir()."/decrypted.bin",$this);
        }
        $hunterName = $mh4Cipher->getHunterName($user);
        $sex = $mh4Cipher->getSex($user);

        $featuresColor = $mh4Cipher->getColor($user,"features");
        $hairColor = $mh4Cipher->getColor($user,"hair");
        $clothColor = $mh4Cipher->getColor($user,"cloth");
        $skinColor = $mh4Cipher->getColor($user,"skin");

        $zenies = $mh4Cipher->getZenies($user);
        $HR = $mh4Cipher->getHunterRanking($user);
        $CP = $mh4Cipher->getCaravanPoints($user);
        $itemBox = null;
        //$itemBox = $mh4Cipher->getItemBox($user);
        //$itemBox = $mh4Cipher->getItemBoxAtSlot(9,$user);
        //$mh4Cipher->cheatSetAllBoxItems($user);
        //$mh4Cipher->setRangeItems($user,1401,1914);
        /*$mh4Cipher->setItemList(
            $user,
            array(
                744,
                743,
                632,
                633,
                1790,
                1789,
                1455,
                1456,
                1647,

            ),
            true,
            980
        );*/
        //$mh4Cipher->setCaravanPoints(1000000,$user);
        //$mh4Cipher->cheatSetAllEquipment($user);
        //$mh4Cipher->setAllArmors($user);
        //$readed = $mh4Cipher->setItemBoxAtSlot(15,99,878,$user); //megazumos
        /*$readed = $mh4Cipher->setItemBoxAtSlot(1318,99,900,$user); //garra d. velocidrome*/
        /*$mh4Cipher->setItemBoxAtSlot(1643,99,910,$user); //esf. armadura fuerte
        $mh4Cipher->setItemBoxAtSlot(1643,99,911,$user); //esf. armadura fuerte
        $mh4Cipher->setItemBoxAtSlot(1643,99,912,$user); //esf. armadura fuerte

        $mh4Cipher->setItemBoxAtSlot(1644,99,913,$user); //esf. armadura divina
        $mh4Cipher->setItemBoxAtSlot(1644,99,914,$user); //esf. armadura divina
        $mh4Cipher->setItemBoxAtSlot(1644,99,915,$user); //esf. armadura divina
        
        $mh4Cipher->setItemBoxAtSlot(1291,99,916,$user); //piedra garuga
        $mh4Cipher->setItemBoxAtSlot(1646,99,917,$user);
        $mh4Cipher->setItemBoxAtSlot(1715,99,918,$user);
        $mh4Cipher->setItemBoxAtSlot(1715,99,919,$user);

        $readed = $mh4Cipher->setZenies(9999999,$user);*/
        //echo "Bytes readed: ".$readed;
        //die;


    	return $this->render(
	        	'DesignBundle:Frontend:home.html.twig',
	        	array(
	        		"hunterName"    => $hunterName,
                    "zenies"        => $zenies,
                    "hunterRank"    => $HR,
                    "itemBox"       => $itemBox,
                    "caravanPoints" => $CP,
                    "sex"           => $sex == "M" ? "Male" : "Female",
                    "FeaturesColor" => $featuresColor,
                    "HairColor"     => $hairColor,
                    "ClothColor"    => $clothColor,
                    "SkinColor"     => $skinColor
	        	)
	        );
        //return new Response("Protected Area!!!".$user->getUsername(),200);
    }
}
