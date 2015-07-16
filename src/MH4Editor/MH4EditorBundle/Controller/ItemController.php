<?php

namespace MH4Editor\MH4EditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ItemController extends Controller
{
    public function listAction(Request $request)
    {
        if(!$request->isXMLHttpRequest()){
            return new Response(json_encode(array("status" => false)),200);
        }

        $vars  = $request->request->all();

        $maxShow    = (isset($vars['maxShow']) && !empty($vars['maxShow'])) ? $vars['maxShow'] : 100;
        $search     = (isset($vars['search']) && !empty($vars['search'])) ? $vars['search'] : '';
        $offset     = (isset($vars['offset']) && !empty($vars['offset'])) ? $vars['offset'] : 1;

        $response = array();
        $response['status'] = true;
    	$user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            "SELECT i
            FROM MH4EditorBundle:Item i
            "
        )
        ->setMaxResults(100)
        ->setFirstResult($offset);

        $results = $query->getResult();

        $items = array();
        foreach ($results as $item) {
            $itm = array();
            $itm['id'] = $item->getId();
            $itm['name'] = $item->getName();
            $itm['description'] = $item->getDescription();
            $itm['img'] = $item->getUrlPath();
            $itm['buyIngamePrice'] = $item->getBuyPrice();
            $itm['sellIngamePrice'] = $item->getSellPrice();
            $itm['buyWebCaravanPoints'] = $item->getCaravanWebBuyValue();
            $itm['carryMax'] = $item->getCarryCapacity();

            $items[] = $itm;
        }

        $response['items'] = $items;

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

        $item = $em->getRepository("MH4EditorBundle:Item")->find($item_id);

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
}
