<?php

namespace MH4Editor\MH4API1Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use MH4Editor\MH4EditorBundle\Entity\ClientApp;

class UserController extends Controller
{
    public function uploadAction(Request $request)
    {
        $files = $request->files;
        $data = $request->request->all();
        $headers = $request->headers;
        $response = array("status" => false);
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
            $uploadedFiles = $files->get('files');

            foreach ($uploadedFiles as $file) {
                
                //ladybug_dump($file);

                $mimeType = $file->getMimeType();
                $size = $file->getSize();
                $name = $file->getClientOriginalName();
                $file->move(__DIR__."/../../../../web/uploads/clients/".$clientToken."/".$data['user'],$name);
            }
            $em->persist($client);
            $em->flush();
            $response["status"] = true;
        }else{
            return new Response(json_encode($response),200);
        }

        return new Response(json_encode($response),200);
        
    }

     public function downloadAction(Request $request)
    {
        $response = array("status" => false);
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
            if($client instanceof ClientApp ){

                $DIR = __DIR__."/../../../../web/uploads/clients/".$clientToken."/".$data['user'];
                if(file_exists($DIR."/user1") || file_exists($DIR."/user2") || file_exists($DIR."/user3")){
                    $mh4Cipher = $this->get("mh4_cipher");
                    if(file_exists($DIR."/decrypted.bin")){
                        $mh4Cipher->MH4Encrypt($DIR.'/decrypted.bin',$DIR.'/user1');
                    }

                    $headers = array(
                        'Content-Type:' => 'application/octet-stream',
                        'Content-Transfer-Encoding' => 'Binary',
                        'Content-disposition' => 'attachment; filename="user1"'
                    );
                    $file = fopen($DIR.'/user1',"rb");
                    $content = fread($file,filesize($DIR.'/user1'));
                    fclose($file);
                    $response = new Response('',200,$headers);

                    $response->setContent($content);
                    return $response;
                }
            }
        }

    	
        return new Response(json_encode($response),200);
    }
}
