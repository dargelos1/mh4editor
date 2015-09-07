<?php

namespace MH4Editor\MH4APIConnectorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ConnectorController extends Controller
{
    public function uploadAction(Request $request)
    {
        
     	$post_vars = [
		    'userSave' 		=> 'UserX save file',
		    'user' 			=> 'didix16',
		    'clientToken'	=> '_Fort42_',
		    'appUsername'	=> 'fort42',
		    'appSerial'		=> 'XF45-8954-GHIO-LXCP-TQFD'
		];



		$files = [
		    __DIR__."/../../../../web/uploads/save_file/".$post_vars['user']."/user1"
		];

		try{
		    $boundary = sha1(1);
		    $crlf = "\r\n";
		    $body = '';

		    foreach($post_vars as $key => $value){
		        $body .= '--'.$boundary.$crlf
		            .'Content-Disposition: form-data; name="'.$key.'"'.$crlf
		            .'Content-Length: '.strlen($value).$crlf.$crlf
		            .$value.$crlf;
		    }

		    foreach($files as $file){
		        $finfo = new \finfo(FILEINFO_MIME);
		        $mimetype = $finfo->file($file);

		        $file_contents = file_get_contents($file);

		        $body .= '--'.$boundary.$crlf
		            .'Content-Disposition: form-data; name="files[]"; filename="'.basename($file).'"'.$crlf
		            .'Content-Type: '.$mimetype.$crlf
		            .'Content-Length: '.strlen($file_contents).$crlf
		            .'Content-Type: application/octet-stream'.$crlf.$crlf
		            .$file_contents.$crlf;
		    }

		    $body .= '--'.$boundary.'--';

		    //$post_data = http_build_query($post_vars);

		    $response = '';
		    if($fp = fsockopen('mh4editor.app', 80, $errno, $errstr, 20)){
		        $write = "POST /api/v1/user/uploadsave HTTP/1.1\r\n"
		            ."Host: mh4editor.app\r\n"
		            ."Content-type: multipart/form-data; boundary=".$boundary."\r\n"
		            ."Content-Length: ".strlen($body)."\r\n"
		            .'X-App-Token: '.$post_vars['clientToken']."\r\n"
		            .'X-App-Username: '.$post_vars['appUsername']."\r\n"
		            .'X-App-Serial: '.$post_vars['appSerial']."\r\n"
		            ."Connection: Close\r\n\r\n"
		            .$body;
		        fwrite($fp, $write);

		        //echo "-------------------- REQUEST START --------------------\n".$write."\n-------------------- REQUEST END --------------------\n\n\n";
		        //echo "Request sended...\n";

		        while($line = fgets($fp)){
		            if($line !== false){
		                $response .= $line;
		            }
		        }

		        fclose($fp);

		        $headers = explode("\r\n",$response);

		        foreach ($headers as $key => $value) {
		        	$response = $value;
		        }

		        //echo "-------------------- RESPONSE START --------------------\n".$response."\n-------------------- RESPONSE END --------------------\n\n";
		    }
		    else{
		        throw new \Exception("$errstr ($errno)");
		    }

		    return new Response($response,200);
		}
		catch(\Exception $e){
		    echo 'Error: '.$e->getMessage();
		}
    }

    public function downloadAction()
    {
    	$post_vars = [
		    'userSave' 		=> 'UserX save file',
		    'user' 			=> 'didix16',
		    'clientToken'	=> '_Fort42_',
		    'appUsername'	=> 'fort42',
		    'appSerial'		=> 'XF45-8954-GHIO-LXCP-TQFD'
		];

		$response = '';
		$body = '';
		$len = count($post_vars);
		$x = 0;
		foreach ($post_vars as $var => $value) {
			
			$body.= $var.'='.urlencode($value);
			if($x< $len-1)
				$body.= '&';
			$x++;

		}
		//echo $body;die;
	    if($fp = fsockopen('mh4editor.app', 80, $errno, $errstr, 20)){
	        $write = "POST /api/v1/user/download HTTP/1.1\r\n"
	            ."Host: mh4editor.app\r\n"
	            ."Content-type: application/x-www-form-urlencoded; charset=UTF-8\r\n"
	            ."Content-Length: ".strlen($body)."\r\n"
	            .'X-App-Token: '.$post_vars['clientToken']."\r\n"
	            .'X-App-Username: '.$post_vars['appUsername']."\r\n"
	            .'X-App-Serial: '.$post_vars['appSerial']."\r\n"
	            ."Connection: Close\r\n\r\n"
	            .$body;
	        fwrite($fp, $write);

	        //echo "-------------------- REQUEST START --------------------\n".$write."\n-------------------- REQUEST END --------------------\n\n\n";
	        //echo "Request sended...\n";

	        while($line = fgets($fp)){
	            if($line !== false){
	                $response .= $line;
	            }
	        }

	        fclose($fp);
	    	$response = explode("\r\n\r\n",$response);
	    	$headers = array_shift($response);

	    	if(strpos($headers, 'Content-Transfer-Encoding: Binary') === FALSE){
	    		return new Response(json_encode(array("status" => false)),200);
	    	}
	        $response = implode($response);
	        $body = $this->decode_chunked($response);

	        $headers = array(
    			'Content-Type:' => 'application/octet-stream',
    			'Content-Transfer-Encoding' => 'Binary',
    			'Content-disposition' => 'attachment; filename="user1"'
    		);
    		$content = $body;
    		$response = new Response('',200,$headers);

    		$response->setContent($body);

    		return $response;
	        //echo "-------------------- RESPONSE START --------------------\n".$response."\n-------------------- RESPONSE END --------------------\n\n";
	    }
	    else{
	        throw new \Exception("$errstr ($errno)");
	    }

	    return new Response($response,200);
    }

    public function buyAction(){

    	$post_vars = [
		    'userSave' 		=> 'UserX save file',
		    'user' 			=> 'didix16',
		    'clientToken'	=> '_Fort42_',
		    'appUsername'	=> 'fort42',
		    'appSerial'		=> 'XF45-8954-GHIO-LXCP-TQFD'
		];

		$response = '';
		$body = '';
		$len = count($post_vars);
		$x = 0;
		foreach ($post_vars as $var => $value) {
			
			$body.= $var.'='.urlencode($value);
			if($x< $len-1)
				$body.= '&';
			$x++;

		}
		//echo $body;die;
	    if($fp = fsockopen('mh4editor.app', 80, $errno, $errstr, 20)){
	        $write = "POST /api/v1/item/buy HTTP/1.1\r\n"
	            ."Host: mh4editor.app\r\n"
	            ."Content-type: application/x-www-form-urlencoded; charset=UTF-8\r\n"
	            ."Content-Length: ".strlen($body)."\r\n"
	            .'X-App-Token: '.$post_vars['clientToken']."\r\n"
	            .'X-App-Username: '.$post_vars['appUsername']."\r\n"
	            .'X-App-Serial: '.$post_vars['appSerial']."\r\n"
	            ."Connection: Close\r\n\r\n"
	            .$body;
	        fwrite($fp, $write);

	        //echo "-------------------- REQUEST START --------------------\n".$write."\n-------------------- REQUEST END --------------------\n\n\n";
	        //echo "Request sended...\n";

	        while($line = fgets($fp)){
	            if($line !== false){
	                $response .= $line;
	            }
	        }

	        fclose($fp);
	    	$response = explode("\r\n\r\n",$response);
	    	$headers = array_shift($response);

	    	if(strpos($headers, 'Content-Transfer-Encoding: Binary') === FALSE){
	    		return new Response(json_encode(array("status" => false)),200);
	    	}
	        $response = implode($response);
	        $body = $this->decode_chunked($response);

	        $headers = array(
    			'Content-Type:' => 'application/octet-stream',
    			'Content-Transfer-Encoding' => 'Binary',
    			'Content-disposition' => 'attachment; filename="user1"'
    		);
    		$content = $body;
    		$response = new Response('',200,$headers);

    		$response->setContent($body);

    		return $response;
	        //echo "-------------------- RESPONSE START --------------------\n".$response."\n-------------------- RESPONSE END --------------------\n\n";
	    }
	    else{
	        throw new \Exception("$errstr ($errno)");
	    }

	    return new Response($response,200);
    }

    private function decode_chunked($str) {
	  for ($res = ''; !empty($str); $str = trim($str)) {
	    $pos = strpos($str, "\r\n");
	    $len = hexdec(substr($str, 0, $pos));
	    $res.= substr($str, $pos + 2, $len);
	    $str = substr($str, $pos + 2 + $len);
	  }
	  return $res;
	}
}
