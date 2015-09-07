<?php

namespace MH4Editor\MH4API1Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use MH4Editor\MH4EditorBundle\Entity\ClientApp;

class ItemController extends Controller
{
    public function buyAction(Request $request)
    {
        $user = null;
        $response = array();
        $response["status"] = false;

        $data = $request->request->all();
        $headers = $request->headers;


        $valid = $headers->get('X-App-Token') && $headers->get('X-App-Username') && $headers->get('X-App-Serial');
        if($valid){

            $em = $this->getDoctrine()->getManager();
            $clientToken = $data['clientToken'];
            $serial = $data['appSerial'];
            $client = $em->getRepository("MH4EditorBundle:ClientApp")->findBy(
                array(
                    "token"     => $clientToken,
                    "appSerial" => $serial
                )
            );

            $client = $client[0];

            $client->setLastApiAccess(new \DateTime());

            $translator = $this->get('translator');

            $itemQuota = $user->getItemsQuota();
            $response['maxQuota'] = $user->getMaxItemsQuota();
            if($itemQuota <= 0){

                $response["errMsg"] = $translator->trans("You have reach your daily item quota!");
                return new Response(json_encode($response),200);
            }

            $item = $em->getRepository("MH4EditorBundle:Item")->findBy(array("canonicalName" => $item_name));
            
            if(count($item) > 0 && $item[0] && $item[0] instanceof Item){
                
                $item = $item[0];

                $vars  = $request->request->all();

                $itemPriceZ = isset($vars["item-price-zenies"]) ? $vars["item-price-zenies"] : 0;
                $itemPriceCP = isset($vars["item-price-cp"]) ? $vars["item-price-cp"] : 0;
                $itemUnits = isset($vars["item-units"]) ? $vars["item-units"] : 0;
                //First, check if there are some client price manipulation. If there are, then bann the user.
                $isLocked = $item->getIsLocked();

                if( !$isLocked && $item->getBuyPrice() != $itemPriceZ){
                    $user->setIsBanned(true);
                    $em->persist($user);
                    $em->flush();
                    $response["banned"] = true;
                    return new Response(json_encode($response),200);
                }else if($isLocked){
                    $item = $em->getRepository("MH4EditorBundle:Item")->findBy(array("canonicalName" => $item_name));
                    $item = $item[0];
                    $itemPriceZ = $item->getBuyPrice();
                }

                $mh4Cipher = $this->get("mh4_cipher");
                if(!file_exists($user->getUploadDir()."/decrypted.bin")){
                    $mh4Cipher->MH4Decrypt($user->getAbsolutePath() ,$user->getUploadDir()."/decrypted.bin",$this);
                }

                $zenies = $mh4Cipher->getZenies($user);
                $CP = $mh4Cipher->getCaravanPoints($user);

                //Recalc item units able to buy
                if($itemUnits > $itemQuota){

                    $itemUnits = $itemQuota;
                }

                $newZenies = $zenies - ($itemPriceZ*$itemUnits); //Save into an other var for future bugs setting the items
                //and recover the transaction

                //If the user havn't enough money, do nothing.

                if($newZenies< 0){

                    $response["errMsg"] = $translator->trans("You have not enought money to buy this");
                    return new Response(json_encode($response),200);
                }

                $item->setTimesBought($item->getTimesBought()+$itemUnits);
                $em->persist($item);

                $newQuota = $itemQuota - $itemUnits;
                $user->setItemsQuota($newQuota);
                $response['quota'] = $newQuota;
                $em->persist($user);
                $em->flush();

                $ib = new ItemBought();
                $ib->setPurchaseDate(new \DateTime());
                $ib->setItem($item);
                $ib->setUnits($itemUnits);
                $ib->setUser($user);
                $ib->setMoneyWasted($zenies-$newZenies);

                $em->persist($ib);
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
}
