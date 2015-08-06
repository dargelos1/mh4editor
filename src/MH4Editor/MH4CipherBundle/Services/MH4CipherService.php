<?php
namespace MH4Editor\MH4CipherBundle\Services;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MH4CipherService extends Controller{

	const EQUIP_BOX_OFFSET		= 0x173E;
	const EQUIP_ARMOR			= 1;
	const EQUIP_BRACELET		= 2;
	const EQUIP_PANTS			= 3;
	const EQUIP_BOOTS			= 4;
	const EQUIP_HELM			= 5;
	const EQUIP_TALISMAN		= 6;
	const EQUIP_GREATSWORD 		= 7;
	const EQUIP_S_AND_S			= 8;
	const EQUIP_HAMMER			= 9;
	const EQUIP_SPEAR			= 10;
	const EQUIP_LIGHT_XBOW		= 11;
	const EQUIP_HEAVY_XBOW		= 12;
	const EQUIP_LONG_SWORD		= 13;
	const EQUIP_AXE				= 14;
	const EQUIP_SPEAR_GUN		= 15;
	const EQUIP_BOW				= 16;
	const EQUIP_DUAL_SWORD		= 17;
	const EQUIP_CORNAMUSA		= 18;
	const EQUIP_GLAIVE			= 19;
	const EQUIP_SWORD_AXE		= 20;

	const EQUIP_ARMOR_IDS		= 887; //Total differents Ids
	const EQUIP_BRACELET_IDS	= 871; //Total differents Ids
	const EQUIP_PANTS_IDS		= 871; //Total differents Ids
	const EQUIP_BOOTS_IDS		= 879; //Total differents Ids

	
	//28bytes per equipment


	public function MH4Decrypt($file,$fileOut,$controller){

		$user = $controller->getUser();
		//ladybug_dump($user);die;
		$line =  exec("../tools/savedata.py d ".$file." ".$fileOut." 2>&1");
		//echo $line;
    	//system("tools/savedata.py d ".$file." ".$fileOut);

    }

    public function MH4Encrypt($file,$fileOut){

		$line =  exec("../tools/savedata.py e ".$file." ".$fileOut." 2>&1");
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

    public function getSex($user){

    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0x18);
		$sex = fread($decryptedFile,1);
		$sex = unpack("C",$sex);
		$sex = $sex[1];
		fclose($decryptedFile);
		return $sex === 1 ? "F" : "M";
    }

    public function setSex($user,$sex){

    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	$data1 = fread($decryptedFile, 0x18);
    	$readed+= strlen($data1);
    	fwrite($editFile, $data1);
    	
    	fwrite($editFile, pack("C",$sex));

    	fseek($decryptedFile,(0x18+1));
    	$readed+= 1;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0x18+1) ) );
		fwrite($editFile, $data1);
		$readed+= strlen($data1);
		//$box = json_encode($box);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		fclose($editFile);
		unlink($user->getUploadDir()."/decrypted.bin");
		rename($user->getUploadDir()."/decrypted_edit.bin", $user->getUploadDir()."/decrypted.bin");

		//return $readed;
		return $this;
    }

    public function getColor($user,$section){
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");

    	switch ($section) {
    		case 'features':
    			fseek($decryptedFile,0x1F);
    			break;
    		case 'hair':
    			fseek($decryptedFile,0x22);
    			break;
    		case 'cloth':
    			fseek($decryptedFile,0x25);
    			break;
    		case 'skin':
    			fseek($decryptedFile,0x28);
    			break;
    		default:
    			return "#000000";
    			break;
    	}

		$R = fread($decryptedFile,1);
		$R = unpack("C",$R);
		$R = $R[1];

		$R = strlen(dechex($R)) > 1 ? dechex($R) : "0".dechex($R);

		$G = fread($decryptedFile,1);
		$G = unpack("C",$G);
		$G = $G[1];

		$G = strlen(dechex($G)) > 1 ? dechex($G) : "0".dechex($G);

		$B = fread($decryptedFile,1);
		$B = unpack("C",$B);
		$B = $B[1];

		$B = strlen(dechex($B)) > 1 ? dechex($B) : "0".dechex($B);



		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return "#".$R.$G.$B;
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

		//return $readed;
		return $this;
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

    public function getCaravanPoints($user){

    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0xE8A0);
		$CPoints = fread($decryptedFile,4);
		$CPoints = unpack("V",$CPoints);
		$CPoints = $CPoints[1];
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $CPoints;
    }

    public function setCaravanPoints($CP,$user){

    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	$data1 = fread($decryptedFile, 0xE8A0);
    	$readed+= strlen($data1);
    	fwrite($editFile, $data1);
    	
    	fwrite($editFile, pack("V",$CP));

    	fseek($decryptedFile,(0xE8A0+4));
    	$readed+= 4;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0xE8A0+4) ) );
		fwrite($editFile, $data1);
		$readed+= strlen($data1);
		//$box = json_encode($box);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		fclose($editFile);
		unlink($user->getUploadDir()."/decrypted.bin");
		rename($user->getUploadDir()."/decrypted_edit.bin", $user->getUploadDir()."/decrypted.bin");

		//return $readed;
		return $this;
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

    public function setItemList($user,$itemList,$startAtSlot=false,$startSlot=null){

    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	if(!$startAtSlot){
    		$data1 = fread($decryptedFile, 0x15E);
	    	$readed+= strlen($data1);
	    	fwrite($editFile, $data1);
	    	
	    	$len = count($itemList);
	    	$itemPointer = 0;

	    	for($i=0;$i<1400;$i++){

	    		$itemId = fread($decryptedFile,2);
				$itemId = unpack("v",$itemId);
				$itemId = $itemId[1];

				$itemUnits = fread($decryptedFile,2);
				$itemUnits = unpack("v",$itemUnits);
				$itemUnits = $itemUnits[1];

				if($itemId == 0){

		    		fwrite($editFile, pack("S",$itemList[$itemPointer]));
		    		fwrite($editFile, pack("S",99));
		    		
			    	$itemPointer++;

				}else{
					fwrite($editFile, pack("S",$itemId));
					fwrite($editFile, pack("S",$itemUnits));
				}
				$readed+= 4;

	    	}

	    	fseek($decryptedFile,(0x15E+(1400*4) ));
			$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0x15E+(1400*4) )) );
			fwrite($editFile, $data1);
	    }else{
	    	$data1 = fread($decryptedFile, 0x15E+(4*$startSlot));
	    	$readed+= strlen($data1);
	    	fwrite($editFile, $data1);
	    	
	    	$len = count($itemList);
	    	for ($i=0; $i < $len; $i++) {

	    		fwrite($editFile, pack("S",$itemList[$i]));
				fwrite($editFile, pack("S",99));

	    	}

	    	fseek($decryptedFile,(0x15E+(4*$startSlot)+($len*4) ));
	    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0x15E+(4*$startSlot)+($len*4)) ) );
	    	fwrite($editFile, $data1);

	    }
    	
		$readed+= strlen($data1);
		//$box = json_encode($box);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		fclose($editFile);

		unlink($user->getUploadDir()."/decrypted.bin");
		rename($user->getUploadDir()."/decrypted_edit.bin", $user->getUploadDir()."/decrypted.bin");

		return $readed;

    }

    public function setRangeItems($user,$itemIdFrom,$itemIdTo){

    	$box = array();
    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	$data1 = fread($decryptedFile, 0x15E);
    	$readed+= strlen($data1);
    	fwrite($editFile, $data1);
    	fseek($decryptedFile,0x15E);
    	for($i=$itemIdFrom;$i<=$itemIdTo;$i++){

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
    	$range = ($itemIdTo - $itemIdFrom)+1;
		fseek($decryptedFile,(0x15E+$range*4));
		$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0x15E+$range*4)) );
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

    public function cheatSetAllEquipment($user){

    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	//$data1 = fread($decryptedFile, self::EQUIP_BOX_OFFSET+(28*$slot));
    	$data1 = fread($decryptedFile, self::EQUIP_BOX_OFFSET);
    	$readed+= strlen($data1);

    	$bytes = array(
    		0x00,0x00,0x00,0x00,0x00,0x00,
    		0x00,0x00,0x00,0x00,0x00,0x00,
    		0x00,0x00,0x00,0x00,0x00,0x00,
    		0x00,0x00,0x00,0x00,0x00,0x00
    	);
    	fwrite($editFile, $data1);

		for($j=1;$j<=15;$j++){

			for ($k=1; $k <=100 ; $k++) { 
				$equipType = $j;
				$equipId = $k;
    			fwrite($editFile, pack("S",$equipType));
    			fwrite($editFile, pack("S",$equipId));
    			$bytesLen = count($bytes);
    			for($b = 0;$b<$bytesLen;$b++){
    				fwrite($editFile, pack("C",$bytes[$b]));
    			}
    			
    			$readed += 28;
			}


		}
    	
    	

    	fseek($decryptedFile,(self::EQUIP_BOX_OFFSET+(28*1500)));
    	$readed+= 28;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(self::EQUIP_BOX_OFFSET+(28*1500)) ) );
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

    public function setAllArmors($user){

    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	//$data1 = fread($decryptedFile, self::EQUIP_BOX_OFFSET+(28*$slot));
    	$data1 = fread($decryptedFile, self::EQUIP_BOX_OFFSET);
    	$readed+= strlen($data1);

    	$bytes = array(
    		0x00,0x00,0x00,0x00,0x00,0x00,
    		0x00,0x00,0x00,0x00,0x00,0x00,
    		0x00,0x00,0x00,0x00,0x00,0x00,
    		0x00,0x00,0x00,0x00,0x00,0x00
    	);
    	fwrite($editFile, $data1);

    	for($i=1;$i<=1500;$i++){
    		$equipType = self::EQUIP_BOOTS;
			$equipId = $i;
			fwrite($editFile, pack("S",$equipType));
			fwrite($editFile, pack("S",$equipId));
			$bytesLen = count($bytes);
			for($b = 0;$b<$bytesLen;$b++){
				fwrite($editFile, pack("C",$bytes[$b]));
			}
			$readed += 28;
    	}

    	fseek($decryptedFile,(self::EQUIP_BOX_OFFSET+(28*1500)));
    	$readed+= 28;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(self::EQUIP_BOX_OFFSET+(28*1500)) ) );
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