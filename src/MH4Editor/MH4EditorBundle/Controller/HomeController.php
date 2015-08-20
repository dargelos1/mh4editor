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
        //wvar_dump($this->get('request')->getSession()->get('_locale'));die;

        $hunterName = "NO_NAME";
        $sex = "NO_GENDER";

        $featuresColor = "000000";
        $hairColor = "000000";
        $clothColor = "000000";
        $skinColor = "000000";

        $zenies = 0;
        $HR = 0;
        $CP = 0;
        $itemBox = null;

        $mh4Cipher = $this->get("mh4_cipher");
        if(!file_exists($user->getUploadDir()."/decrypted.bin")){
            //ladybug_dump($user->getAbsolutePath());die;
            $status = $mh4Cipher->MH4Decrypt($user->getAbsolutePath() ,$user->getUploadDir()."/decrypted.bin",$this);
            if($status){
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
            }
        }else{
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
        }
        //
        $RCTotal = $mh4Cipher->getRCTotalPoints($user);
        //$mh4Cipher->setRCTotalPoints(227450,$user);
        //$mh4Cipher->setHunterRanking(75,$user);
        //USAR CANONICAL NAME ITEM PARA REFRENCIAR EL ID DEL OBJETO YA QUE EL NOMBRE PUEDE ESTAR REPETIDO!
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
        //$mh4Cipher->setAllArmors($user,$mh4Cipher::EQUIP_TALISMAN);
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

        $translator = $this->get("translator");

    	return $this->render(
	        	'DesignBundle:Frontend:home.html.twig',
	        	array(
	        		"hunterName"    => $hunterName,
                    "zenies"        => $zenies,
                    "hunterRank"    => $HR,
                    "itemBox"       => $itemBox,
                    "caravanPoints" => $CP,
                    "sex"           => $sex == "M" ? $translator->trans("Male") : $translator->trans("Female"),
                    "FeaturesColor" => $featuresColor,
                    "HairColor"     => $hairColor,
                    "ClothColor"    => $clothColor,
                    "SkinColor"     => $skinColor
	        	)
	        );
        //return new Response("Protected Area!!!".$user->getUsername(),200);
    }

    public function showTalismanCreatorAction()
    {

        $user = $this->getUser();
        $translator = $this->get("translator");
        //wvar_dump($this->get('request')->getSession()->get('_locale'));die;

        $hunterName = "NO_NAME";
        $sex = "NO_GENDER";

        $featuresColor = "000000";
        $hairColor = "000000";
        $clothColor = "000000";
        $skinColor = "000000";

        $zenies = 0;
        $HR = 0;
        $CP = 0;
        $itemBox = null;

        $mh4Cipher = $this->get("mh4_cipher");
        if(!file_exists($user->getUploadDir()."/decrypted.bin")){
            $status = $mh4Cipher->MH4Decrypt($user->getAbsolutePath() ,$user->getUploadDir()."/decrypted.bin",$this);
            if($status){
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
            }
        }else{
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
        }
        
        $RCTotal = $mh4Cipher->getRCTotalPoints($user);

        return $this->render(
                'DesignBundle:Frontend:talisman_creator.html.twig',
                array(
                    "hunterName"    => $hunterName,
                    "zenies"        => $zenies,
                    "hunterRank"    => $HR,
                    "itemBox"       => $itemBox,
                    "caravanPoints" => $CP,
                    "sex"           => $sex == "M" ? $translator->trans("Male") : $translator->trans("Female"),
                    "FeaturesColor" => $featuresColor,
                    "HairColor"     => $hairColor,
                    "ClothColor"    => $clothColor,
                    "SkinColor"     => $skinColor
                )
            );
    }

    public function changeLanguageAction()
    {
        $request = $this->getRequest();
        if($request->request->has('lang') && in_array($request->request->get('lang'), array("es", "en"))){
            $request->getSession()->set('_locale', $request->request->get('lang'));
            $request->setLocale($request->getSession()->get('_locale', "en"));
            $loggeduser = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $loggeduser->setLanguage($request->getLocale());
            $em->persist($loggeduser);
            $em->flush();
            if($request->request->has('referer')){
                return $this->redirect($request->request->get('referer'));
            }
        }
        return $this->redirect($this->generateUrl('mh4_editor_homepage'));
    }    

    public function changeLanguageByUrlAction($lang)
    {
        $request = $this->getRequest();

        if(in_array($lang, array("es", "en"))){
            $request->getSession()->set('_locale', $lang);

            $request->setLocale($request->getSession()->get('_locale', "en"));

            $loggeduser = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $loggeduser->setLocale($request->getLocale());
            $em->persist($loggeduser);
            $em->flush();

            /*if($request->request->has('referer')){
                return $this->redirect($request->request->get('referer'));
            }*/
        }
        return $this->redirect($this->generateUrl('mh4_editor_homepage'));
    }
}
