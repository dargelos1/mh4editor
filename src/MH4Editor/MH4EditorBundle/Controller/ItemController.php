<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use MH4Editor\MH4EditorBundle\Entity\Item;

class ItemController extends Controller
{
    public function listAction(Request $request)
    {
        if(!$request->isXMLHttpRequest()){
            return new Response(json_encode(array("status" => false)),200);
        }

        $vars  = $request->request->all();

        $maxShow    = (isset($vars['maxShow']) && !empty($vars['maxShow'])) ? $vars['maxShow'] : 25;
        $search     = (isset($vars['search']) && !empty($vars['search'])) ? $vars['search'] : '';
        $offset     = (isset($vars['offset']) && !empty($vars['offset'])) ? $vars['offset'] : 1;

        $response = array();
        $response['status'] = true;
    	$user = $this->getUser();
        $locale = $user->getLocale();
        $em = $this->getDoctrine()->getManager();

        $fName = ($locale == "es") ? "name" : "nameEn";

        $query = null;
        if(trim($search) !== ''){
            $query = $em->createQuery(
                "SELECT i
                FROM MH4EditorBundle:Item i
                WHERE i.".$fName." LIKE :search
                AND (
                    i.".$fName." != 'dummy' AND i.description != 'dummy' AND
                    i.".$fName." NOT LIKE '%Invalid Message%' AND i.description NOT LIKE '%Invalid Message%' AND
                    i.".$fName." NOT LIKE '%dummy%' AND i.description NOT LIKE '%dummy%'
                )
                "
            );
            
            $query->setParameter("search","%".$search."%");
        }else{
            $query = $em->createQuery(
                "SELECT i
                FROM MH4EditorBundle:Item i
                WHERE
                    i.".$fName." != 'dummy' AND i.description != 'dummy' AND
                    i.".$fName." NOT LIKE '%Invalid Message%' AND i.description NOT LIKE '%Invalid Message%' AND
                    i.".$fName." NOT LIKE '%dummy%' AND i.description NOT LIKE '%dummy%'
                "
            );
        }
            
        

        //$results = $query->getResult();
        $paginator = new Paginator($query);

        $totalItems = count($paginator);
        

        $paginator
        ->getQuery()
        ->setMaxResults($maxShow)
        ->setFirstResult($maxShow*($offset-1));

        $pagesCount = ceil($totalItems / $paginator->getQuery()->getMaxResults());
        $items = array();
        foreach ($paginator as $item) {
            $itm = array();
            //$itm['id'] = $item->getId();
            $itm['id'] =$item->getCanonicalName();
            $itm['name'] = ($locale == "es") ? $item->getName() : $item->getEnglishName();
            $itm['description'] = ($locale == "es") ? $item->getDescription() : $item->getEnglishDescription();
            $itm['img'] = $item->getUrlPath();
            $itm['buyIngamePrice'] = $item->getBuyPrice();
            $itm['sellIngamePrice'] = $item->getSellPrice();
            $itm['buyWebCaravanPoints'] = $item->getCaravanWebBuyValue();
            //$itm['carryMax'] = $item->getCarryCapacity();
            $itm['carryMax'] = $item->getBoxCapacity();

            $items[] = $itm;
        }

        $response['items'] = $items;
        $response['paginator'] = array(

            "total" => $totalItems,
            "pages" => $pagesCount
        );

        return new Response(json_encode($response),200);
    	
    }

    public function editAction(Request $request,$item_id)
    {
        if(!$request->isXMLHttpRequest() ||  ($request->isXMLHttpRequest() && !$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) ){
            return new Response(json_encode(array("status" => false)),200);
        }

        $response = array();
        $response['status'] = true;
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $item = $em->getRepository("MH4EditorBundle:Item")->findBy(array("canonicalName"=>$item_id));

        if(count($item) > 0 && $item[0] && $item[0] instanceof Item){

            $item = $item[0];
        }

        $vars  = $request->request->all();

        //ladybug_dump($vars);die;

        $itemName   = $vars['item-name'];
        $itemPrice  = $vars['item-buy'];
        $itemCp     = $vars['item-buy-cp'];


        $item->setBuyPrice($itemPrice);
        $item->setCaravanWebBuyValue($itemCp);

        $em->persist($item);
        $em->flush();

        $response['buyPrice'] = $itemPrice;
        $response['cPoints'] = $itemCp;


        return new Response(json_encode($response),200);

    }

    public function buyAction(Request $request,$item_name){

        $user = $this->getUser();
        $response = array();
        $response["status"] = false;
        if($user && $request->isXMLHttpRequest()){
            
            $em = $this->getDoctrine()->getManager();

            $item = $em->getRepository("MH4EditorBundle:Item")->findBy(array("name" => $item_name));
            
            if(count($item) > 0 && $item[0] && $item[0] instanceof Item){
                
                $item = $item[0];

                $vars  = $request->request->all();

                $itemPriceZ = isset($vars["item-price-zenies"]) ? $vars["item-price-zenies"] : 0;
                $itemPriceCP = isset($vars["item-price-cp"]) ? $vars["item-price-cp"] : 0;
                $itemUnits = isset($vars["item-units"]) ? $vars["item-units"] : 0;
                //First, check if there are some client price manipulation. If there are, then bann the user.

                if($item->getBuyPrice() != $itemPriceZ){
                    $user->setIsBanned(true);
                    $em->persist($user);
                    $em->flush();
                    $response["banned"] = true;
                    return new Response(json_encode($response),200);
                }

                $mh4Cipher = $this->get("mh4_cipher");
                if(!file_exists($user->getUploadDir()."/decrypted.bin")){
                    $mh4Cipher->MH4Decrypt($user->getAbsolutePath() ,$user->getUploadDir()."/decrypted.bin",$this);
                }

                $zenies = $mh4Cipher->getZenies($user);
                $CP = $mh4Cipher->getCaravanPoints($user);

                $newZenies = $zenies - ($itemPriceZ*$itemUnits); //Save into an other var for future bugs setting the items
                //and recover the transaction

                //If the user havn't enough money, do nothing.

                if($newZenies< 0){

                    $response["errMsg"] = $translator->trans("You have not enought money to buy this");
                    return new Response(json_encode($response),200);
                }

                $item->setTimesBought($item->getTimesBought()+$itemUnits);
                $em->persist($item);
                $em->flush();

                $itemList = array();
                $itemList[$item->getId()] = $itemUnits;
                $mh4Cipher->setZenies($newZenies,$user);
                $box = $this->distributeItemsInBox($itemList);
                $response["status"] = true;
                $response["zenies"] = $newZenies;
                $response["caravanPoints"] = $CP;

            }
        }

        return new Response(json_encode($response),200);

    }

    public function sellAction(Request $request,$item_id){

        $user = $this->getUser();
        $response = array();
        $response["status"] = false;
        if($user && $request->isXMLHttpRequest()){
        }

        return new Response(json_encode($response),200);

    }

    /*Distribuye en modulo 99, las cantidades de los items a guardar en la caja. Busca en cada slot del mismo item
    que no tenga 99uds para setearlo a 99 hasta que ya no queden más y así por cada item distinto*/
    //$itemList = array("id" => $uds)
    //Return: by default, the Box status in JSON. If toJSON false, return the BOX Object
    private function distributeItemsInBox($itemList = array(),$toJSON = true){

        $mh4Cipher = $this->get("mh4_cipher");
        $user = $this->getUser();
        $box = $mh4Cipher->getItemBox($user);
        $box = json_decode($box);
        
        foreach($box as $pageIndex => $page){

            foreach ($page as $rowIndex => $row) {
               
               foreach ($row as $cell => $item) {
                    
                    $iPage = str_replace("page","",$pageIndex);
                    $iRow = str_replace("row","",$rowIndex);
                    $iCell = str_replace("col","",$cell);
                    $slot = ($iPage*100)+($iRow*10)+($iCell%10); //From table format to int
                    $currItem = array_key_exists($item->itemId ,$itemList) ? key($itemList) : FALSE;
                    /*if($currItem)
                        var_dump("KEY: ".$currItem);
                    reset($itemList);*/
                    
                    if( $currItem !== FALSE){

                        //Check each cell. If found an item, get that item and check if the slot has 99 units.
                        $units = $item->units;
                        //var_dump("ITEM=>".$item->itemId);
                        //var_dump("ITEM_UNITS: ".$item->units);
                        //var_dump(" AT SLOT:".$slot);
                        
                        if ($units< 99) {
                            
                            $item->units += $itemList[$currItem];
                             
                            if($item->units > 99){
                                
                                $itemList[$currItem] = $item->units%99;
                                $item->units = 99;
                                $units = $item->units;
                                
                            }else{
                                $itemList[$currItem] = 0;
                                array_shift($itemList);
                                $units = $item->units;
                                
                            }

                            //Now set item back to box jsonyfied and binary
                            //var_dump($itemList[$currItem]);
                            $box->{$pageIndex}->{$rowIndex}->{$cell} = $item;
                            $mh4Cipher->setItemBoxAtSlot($item->itemId,$item->units,$slot,$user);
                            $len = count($itemList);
                            if($len === 0) break 3;
                        }

                    }else if($item->itemId === 0 && count($itemList) > 0 ){
                       
                        //var_dump($box->{$pageIndex}->{$rowIndex}->{$cell});
                        $itemId = key($itemList);
                        $units =  array_shift($itemList);
                        $box->{$pageIndex}->{$rowIndex}->{$cell}->itemId = $itemId;
                        $box->{$pageIndex}->{$rowIndex}->{$cell}->units = $units;
                        $mh4Cipher->setItemBoxAtSlot($itemId,$units,$slot,$user);
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
