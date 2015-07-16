<?php
namespace MH4Editor\MH4CipherBundle\Services;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MH4CipherService extends Controller{

	public function MH4Decrypt($file,$fileOut,$controller){

		$user = $controller->getUser();
		//ladybug_dump($user);die;
		$line =  exec("../tools/savedata.py d ".$file." ".$fileOut." 2>&1");
		//echo $line;
    	//system("tools/savedata.py d ".$file." ".$fileOut);

    }

    public function MH4Encrypt($file,$fileOut){

		$line =  exec("../tools/savedata.py e ../tools/".$file." ../tools/".$fileOut." 2>&1");
		//echo $line;
    	//system("tools/savedata.py d ".$file." ".$fileOut);

    }

    public function getHunterName($user){

    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0x00);
		$name = fread($decryptedFile,10);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $name;
    }

    public function getZenies($user){

    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0x34);
		$zenies = fread($decryptedFile,4);
		$zenies = unpack("V",$zenies);
		$zenies = $zenies[1];
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $zenies;
    }

    public function setZenies($zenies,$user){

		$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	$data1 = fread($decryptedFile, 0x34);
    	$readed+= strlen($data1);
    	fwrite($editFile, $data1);
    	
    	fwrite($editFile, pack("V",$zenies));

    	fseek($decryptedFile,(0x34+4));
    	$readed+= 4;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0x34+4) ) );
		fwrite($editFile, $data1);
		$readed+= strlen($data1);
		//$box = json_encode($box);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		fclose($editFile);
		unlink($user->getUploadDir()."/decrypted.bin");
		rename($user->getUploadDir()."/decrypted_edit.bin", $user->getUploadDir()."/decrypted.bin");

		return $readed;
    }

    public function getHunterRanking($user){

    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0x2C);
		$HR = fread($decryptedFile,2);
		$HR = unpack("v",$HR);
		$HR = $HR[1];
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $HR;
    }

    public function getRCTotalPoints($user){

    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0x30);
		$RCPoints = fread($decryptedFile,4);
		$RCPoints = unpack("V",$RCPoints);
		$RCPoints = $RCPoints[1];
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $RCPoints;
    }

    public function getItemBox($user){

    	$box = array();
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0x15E);

    	for($i=0;$i<14;$i++){


    		$page = array();
    		
    		for($j=0;$j<10;$j++){
    			$row = array();
    			for($k=0;$k<10;$k++){
    				$cell = array();
		    		$itemId = fread($decryptedFile,2);
					$itemId = unpack("v",$itemId);
					$itemId = $itemId[1];

					$itemUnits = fread($decryptedFile,2);
					$itemUnits = unpack("v",$itemUnits);
					$itemUnits = $itemUnits[1];

					$cell['itemId'] = $itemId;
					$cell['units'] = $itemUnits;
					$row['cell'.$k] = $cell;

    			}
    			$page['row'.$j] = $row;
			
    		}

    		$box['page'.$i] = $page;
    		
    	}
    	/*
    	for($i=0;$i<1400;$i++){

    		$cell = array();
    		$itemId = fread($decryptedFile,2);
			$itemId = unpack("v",$itemId);
			$itemId = $itemId[1];

			$itemUnits = fread($decryptedFile,2);
			$itemUnits = unpack("v",$itemUnits);
			$itemUnits = $itemUnits[1];

			$cell['itemId'] = $itemId;
			$cell['units'] = $itemUnits;
			$box[] = $cell;


    	}
		*/
		$box = json_encode($box);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $box;
    }

    public function cheatSetAllBoxItems($user){

    	$box = array();
    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	$data1 = fread($decryptedFile, 0x15E);
    	$readed+= strlen($data1);
    	fwrite($editFile, $data1);
    	fseek($decryptedFile,0x15E);
    	for($i=1;$i<=1400;$i++){

    		fwrite($editFile, pack("S",$i));
    		fwrite($editFile, pack("S",99));
    		$readed+= 4;
    		//$cell = array();
    		/*$itemId = fread($decryptedFile,2);
			$itemId = unpack("v",$itemId);
			$itemId = $itemId[1];

			$itemUnits = fread($decryptedFile,2);
			$itemUnits = unpack("v",$itemUnits);
			$itemUnits = $itemUnits[1];

			$cell['itemId'] = $itemId;
			$cell['units'] = $itemUnits;
			$box[] = $cell;*/


    	}
		fseek($decryptedFile,(0x15E+1400*4));
		$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0x15E+1400*4)) );
		fwrite($editFile, $data1);
		$readed+= strlen($data1);
		//$box = json_encode($box);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		fclose($editFile);
		//return $box;
		unlink($user->getUploadDir()."/decrypted.bin");
		rename($user->getUploadDir()."/decrypted_edit.bin", $user->getUploadDir()."/decrypted.bin");
		return $readed;
    }

    public function setItemBoxAtSlot($itemId,$uds,$slot,$user){

    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	$data1 = fread($decryptedFile, 0x15E+(4*$slot));
    	$readed+= strlen($data1);
    	fwrite($editFile, $data1);
    	
    	fwrite($editFile, pack("S",$itemId));
    	fwrite($editFile, pack("S",$uds));

    	fseek($decryptedFile,(0x15E+(4*$slot)+4));
    	$readed+= 4;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0x15E+(4*$slot)+4) ) );
		fwrite($editFile, $data1);
		$readed+= strlen($data1);
		//$box = json_encode($box);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		fclose($editFile);

		unlink($user->getUploadDir()."/decrypted.bin");
		rename($user->getUploadDir()."/decrypted_edit.bin", $user->getUploadDir()."/decrypted.bin");

		return $readed;
    }
}