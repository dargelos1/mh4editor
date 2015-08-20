<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TalismanController extends Controller
{
    public function listAction(Request $request)
    {

    	if(!$request->isXMLHttpRequest()){
            return new Response(json_encode(array("status" => false)),200);
        }

        $vars  = $request->request->all();

        $response = array();
        $response['status'] = true;
    	$user = $this->getUser();
        $locale = $user->getLocale();
        $em = $this->getDoctrine()->getManager();

        $query = null;

        $query = $em->createQuery(
            "SELECT t
            FROM MH4EditorBundle:Talisman t
            "
        );
        
        $results = $query->getResult();
        $talismans = array();
        foreach ($results as $talisman) {
            $itm = array();
            //$itm['id'] = $item->getId();
            $itm['id'] = $talisman->getId();
            $itm['name'] = ($locale == "es") ? $talisman->getName() : $talisman->getEnglishName();
            $itm['img'] = $talisman->getUrlPath();
            $itm['ph'] = $talisman->getRarity();

            $talismans[] = $itm;
        }

        $response['talismans'] = $talismans;

        return new Response(json_encode($response),200);
    	
    }

    public function abilityListAction(Request $request)
    {

    	if(!$request->isXMLHttpRequest()){
            return new Response(json_encode(array("status" => false)),200);
        }

        $vars  = $request->request->all();

        $response = array();
        $response['status'] = true;
    	$user = $this->getUser();
        $locale = $user->getLocale();
        $em = $this->getDoctrine()->getManager();

        $query = null;

        $query = $em->createQuery(
            "SELECT a
            FROM MH4EditorBundle:Ability a
            "
        );
        
        $results = $query->getResult();
        $abilities = array();
        foreach ($results as $ability) {
            $itm = array();
            //$itm['id'] = $item->getId();
            $itm['id'] = $ability->getId();
            $itm['name'] = ($locale == "es") ? $ability->getName() : $ability->getEnglishName();
            $itm['description'] = $ability->getEnglishDescription();
            $activeAbs = $ability->getAbilitiesActive();
            $abs = array();
            $x = 0;
            foreach ($activeAbs as $a) {
                
                $abs[$x]["name"] = $a->getName();
                $abs[$x]["points"] = $a->getPoints();
                $abs[$x]["descrip"] = $a->getDescription();
                $x++;
            }
            $itm["activeAbs"] = $abs;
            $abilities[] = $itm;
            
        }

        $response['abilities'] = $abilities;

        return new Response(json_encode($response),200);
    	
    }

    public function genTalismanAction(Request $request){

        if(!$request->isXMLHttpRequest()){
            return new Response(json_encode(array("status" => false)),200);
        }

        $vars  = $request->request->all();

        $talisman   = (isset($vars['talisman']) && !empty($vars['talisman'])) ? $vars['talisman'] : 1;
        $slots      = (isset($vars['slots']) && !empty($vars['slots'])) ? $vars['slots'] : 0;
        $ab1        = (isset($vars['ab1']) && !empty($vars['ab1'])) ? $vars['ab1'] : 0;
        $ab2        = (isset($vars['ab2']) && !empty($vars['ab2'])) ? $vars['ab2'] : 0;
        $ab1Points  = (isset($vars['ab1Points']) && !empty($vars['ab1Points'])) ? $vars['ab1Points'] : 0;
        $ab2Points  = (isset($vars['ab2Points']) && !empty($vars['ab2Points'])) ? $vars['ab2Points'] : 0;

        $response = array($vars);
        
        $user = $this->getUser();
        $locale = $user->getLocale();
        $em = $this->getDoctrine()->getManager();

        $query = null;

        $response['status'] = true;

        $mh4Cipher = $this->get("mh4_cipher");
        $box = $mh4Cipher->getEquipmentBox($user);

        $equipment = array(
            "equipId"       => $talisman,
            "slots"         => $slots,
            "itemType"      => $mh4Cipher::EQUIP_TALISMAN,
            "skill1Id"      => $ab1,
            "skill1Points"  => $ab1Points,
            "skill2Id"      => $ab2,
            "skill2Points"  => $ab2Points
        );

        $equipmentList = array(

            $equipment
        );

        $this->distributeEquipmentInBox($equipmentList);

        return new Response($box,200);

    }

    private function distributeEquipmentInBox($itemList = array(),$toJSON = true){

        $mh4Cipher = $this->get("mh4_cipher");
        $user = $this->getUser();
        $box = $mh4Cipher->getEquipmentBox($user);
        $box = json_decode($box);
        
        foreach($box as $pageIndex => $page){

            foreach ($page as $rowIndex => $row) {
               
               foreach ($row as $cell => $item) {
                    
                    $iPage = str_replace("page","",$pageIndex);
                    $iRow = str_replace("row","",$rowIndex);
                    $iCell = str_replace("col","",$cell);
                    $slot = ($iPage*100)+($iRow*10)+($iCell%10); //From table format to int
                    /*if($currItem)
                        var_dump("KEY: ".$currItem);
                    reset($itemList);*/
                    if($item->equipId === 0 && $item->itemType === 0 && count($itemList) > 0 ){
                       
                        //var_dump($box->{$pageIndex}->{$rowIndex}->{$cell});
                        //$equip = key($itemList);
                        $equip = array_shift($itemList);
                        $box->{$pageIndex}->{$rowIndex}->{$cell}->equipId = $equip["equipId"];
                        $box->{$pageIndex}->{$rowIndex}->{$cell}->slots = $equip["slots"];
                        $box->{$pageIndex}->{$rowIndex}->{$cell}->itemType = $equip["itemType"];
                        $box->{$pageIndex}->{$rowIndex}->{$cell}->skill1Id = $equip["skill1Id"];
                        $box->{$pageIndex}->{$rowIndex}->{$cell}->skill1Points = $equip["skill1Points"];
                        $box->{$pageIndex}->{$rowIndex}->{$cell}->skill2Id = $equip["skill2Id"];
                        $box->{$pageIndex}->{$rowIndex}->{$cell}->skill2Points = $equip["skill2Points"];

                        $mh4Cipher->setEquipmentBoxAtSlot($equip,$slot,$user);
                        $len = count($itemList);
                        if($len === 0) break 3;
                    }else {
                        //break 3;
                    }
               }
            }
        }
        return ($toJSON) ? json_encode($box) : $box;
    }

}
