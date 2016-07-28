<?php
namespace MH4Editor\MH4CipherBundle\Services;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MH4Editor\MH4CipherBundle\Utils\Blowfish;

class MH4CipherService extends Controller{

	const EQUIP_BOX_OFFSET		= 0x173E;
	const ITEM_POUCH_OFFSET		= 0xCB14; //Offset de la bolsa 4 páginas de 8 items
	const EQUIP_ARMOR			= 1;
	const EQUIP_BRACELET		= 2;
	const EQUIP_PANTS			= 3;
	const EQUIP_BOOTS			= 4;
	const EQUIP_HELM			= 5;
	const EQUIP_TALISMAN		= 6;
	const EQUIP_GREATSWORD 		= 7; //Las armas reliquias con igual nombre, tienen diferentes texturas ( diff id)
	const EQUIP_S_AND_S			= 8;
	const EQUIP_HAMMER			= 9;
	const EQUIP_SPEAR			= 10;
	const EQUIP_LIGHT_XBOW		= 11;
	const EQUIP_HEAVY_XBOW		= 12;
	const EQUIP_LONG_SWORD		= 13;
	const EQUIP_AXE				= 14; //hacha espada
	const EQUIP_SPEAR_GUN		= 15;
	const EQUIP_BOW				= 16;
	const EQUIP_DUAL_SWORD		= 17;
	const EQUIP_CORNAMUSA		= 18;
	const EQUIP_GLAIVE			= 19;
	const EQUIP_SWORD_AXE		= 20; //hacha cargada

	const EQUIP_ARMOR_IDS		= 887; //Total differents Ids
	const EQUIP_BRACELET_IDS	= 871; //Total differents Ids
	const EQUIP_PANTS_IDS		= 871; //Total differents Ids
	const EQUIP_BOOTS_IDS		= 879; //Total differents Ids
	const EQUIP_HELMS_IDS		= 902; //Total differents Ids
	const EQUIP_TALISMAN_IDS	= 14;  //Total differents Ids
	const EQUIP_GREATSWORD_IDS	= 245; //Total differents Ids
	const EQUIP_S_AND_S_IDS		= 246; //Total differents Ids
	const EQUIP_HAMMER_IDS		= 246; //Total differents Ids
	const EQUIP_SPEAR_IDS		= 215; //Total differents Ids
	const EQUIP_LIGHT_XBOW_IDS	= 184; //Total differents Ids
	const EQUIP_HEAVY_XBOW_IDS	= 156; //Total differents Ids
	const EQUIP_LONG_SWORD_IDS	= 226; //Total differents Ids
	const EQUIP_AXE_IDS			= 197; //Total differents Ids
	const EQUIP_SPEAR_GUN_IDS	= 220; //Total differents Ids
	const EQUIP_BOW_IDS 		= 205; //Total differents Ids
	const EQUIP_DUAL_SWORD_IDS	= 228; //Total differents Ids
	const EQUIP_CORNAMUSA_IDS	= 184; //Total differents Ids
	const EQUIP_GLAIVE_IDS 		= 159; //Total differents Ids
	const EQUIP_SWORD_AXE_IDS	= 124; //Total differents Ids


	
	//28bytes per equipment


	/*public function MH4Decrypt($file,$fileOut,$controller){

		$user = $controller->getUser();
		//ladybug_dump($user);die;
		$line =  exec("../tools/savedata.py d ".$file." ".$fileOut." 2>&1");
		//echo $line;
    	//system("tools/savedata.py d ".$file." ".$fileOut);

    }*/

    /*public function MH4Encrypt($file,$fileOut){

		$line =  exec("../tools/savedata.py e ".$file." ".$fileOut." 2>&1");
		//echo $line;
    	//system("tools/savedata.py d ".$file." ".$fileOut);

    }*/


    public function getHunterName($user){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return "NO_NAME";
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0x00);
		$name = fread($decryptedFile,0x18);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);

		return trim($this->fixNick($name));
    }

    public function setHunterName($user,$charName){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");
    	
    	//Write the CharName in UTF8. Max 12 (24 bytes)

    	$charName = mb_convert_encoding($charName,"UTF-8","auto");
		//ladybug_dump(strlen($charName));die;

    	$charLen = strlen($charName);
    	for($i = 0;$i<12;$i++){ //Assuming that charName has maximum 12 chars in ASCII

    		if($i<$charLen){
    			fwrite($editFile, pack("C",ord($charName[$i])));
    			fwrite($editFile, pack("C",0));
    		}else{
    			fwrite($editFile, pack("C2",0,0));
    		}
    		
    	}
    	

    	fseek($decryptedFile,0x18);
    	$readed+= 24;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0x18) ) );
		fwrite($editFile, $data1);
		$readed+= strlen($data1);
		fclose($decryptedFile);
		fclose($editFile);
		unlink($user->getUploadDir()."/decrypted.bin");
		rename($user->getUploadDir()."/decrypted_edit.bin", $user->getUploadDir()."/decrypted.bin");

		//return $readed;
		return $this;
    }

    private function fixNick($nickname){
		if(strlen($nickname)>24) //Si la cantidad de caracteres es mayor de 10 significa que el nick no viene del juego
			return $nickname;
		$fixNick ="";
		$i=0;
		$strlen = strlen($nickname);
		$unicode = (ord($nickname[$i+1]) << 8)+ord($nickname[$i]);
		while($i<$strlen and  $unicode>0){
			$i=$i+2;
			$fixNick.=mb_convert_encoding('&#'.$unicode, 'UTF-8', 'HTML-ENTITIES');
			$unicode = (ord($nickname[$i+1]) << 8)+ord($nickname[$i]);
		}
		
		return $fixNick;
	}

	private function isInteger($input){
	    return(ctype_digit(strval($input)));
	}

    public function getSex($user){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return "F";
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0x18);
		$sex = fread($decryptedFile,1);
		$sex = unpack("C",$sex);
		$sex = $sex[1];
		fclose($decryptedFile);
		return $sex === 1 ? "F" : "M";
    }

    public function setSex($user,$sex){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;

    	switch ($sex) {
    		case 'm':
    			$sex = 0;
    			break;
    		case 'f':
    			$sex = 1;
    			break;
    	}

    	if(!($this->isInteger($sex) && ($sex === 0 || $sex===1)) ){

    		$sex = 1; //Force default women
    	}
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
    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return "#000000";
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

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return 0;
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

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
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

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0x2C);
		$HR = fread($decryptedFile,2);
		$HR = unpack("v",$HR);
		$HR = $HR[1];
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $HR;
    }

    public function setHunterRanking($HR,$user){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	$data1 = fread($decryptedFile, 0x2C);
    	$readed+= strlen($data1);
    	fwrite($editFile, $data1);
    	
    	fwrite($editFile, pack("v",$HR));

    	fseek($decryptedFile,(0x2C+2));
    	$readed+= 2;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0x2C+2) ) );
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

    public function getRCTotalPoints($user){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,0x30);
		$RCPoints = fread($decryptedFile,4);
		$RCPoints = unpack("V",$RCPoints);
		$RCPoints = $RCPoints[1];
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $RCPoints;
    }

    public function setRCTotalPoints($RCPoints,$user){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	$data1 = fread($decryptedFile, 0x30);
    	$readed+= strlen($data1);
    	fwrite($editFile, $data1);
    	
    	fwrite($editFile, pack("V",$RCPoints));

    	fseek($decryptedFile,(0x30+4));
    	$readed+= 4;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(0x30+4) ) );
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

    public function getCaravanPoints($user){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return 0;

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

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;

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
    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $box;
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
					$row['col'.$k] = $cell;

    			}
    			$page['row'.$j] = $row;
			
    		}

    		$box['page'.$i] = $page;
    		
    	}

		$box = json_encode($box);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $box;
    }

    public function cheatSetAllBoxItems($user){

    	$box = array();
    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $box;
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

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
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
    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
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

    public function getItemBoxAtSlot($slot,$user,$toJSON=false){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return ($toJSON) ? json_encode(array()) : 0;
    	$box = $this->getItemBox($user);
    	$box =json_decode($box);
    	$page = floor($slot/100);
    	$row = floor($slot/10);
    	$col = $slot%10;

    	$i = $box->{"page".$page}->{"row".$row}->{"col".$col};

    	$item = array();
    	$item['id'] = $i->itemId;
    	$item['uds'] = $i->units;

		return ($toJSON) ? json_encode($item) : $i;
    }

    public function setItemBoxAtSlot($itemId,$uds,$slot,$user){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
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

    public function getEquipmentBox($user){

    	$box = array();
    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $box;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,self::EQUIP_BOX_OFFSET);

    	for($i=0;$i<15;$i++){


    		$page = array();
    		
    		for($j=0;$j<10;$j++){
    			$row = array();
    			for($k=0;$k<10;$k++){
    				$cell = array();

		    		$itemType = fread($decryptedFile,1);
					$itemType = unpack("C",$itemType);
					$itemType = $itemType[1];

					$slots = fread($decryptedFile,1);
					$slots = unpack("C",$slots);
					$slots = $slots[1];

					

					$equipId = fread($decryptedFile,2);
					$equipId = unpack("S",$equipId);
					$equipId = $equipId[1];

					$unk1 = fread($decryptedFile,1);
					$unk1 = unpack("C",$unk1);
					$unk1 = $unk1[1];

					$unk2 = fread($decryptedFile,1);
					$unk2 = unpack("C",$unk2);
					$unk2 = $unk2[1];

					$jewel1 = fread($decryptedFile,2);
					$jewel1 = unpack("S",$jewel1);
					$jewel1 = $jewel1[1];

					$jewel2 = fread($decryptedFile,2);
					$jewel2 = unpack("S",$jewel2);
					$jewel2 = $jewel2[1];


					$jewel3 = fread($decryptedFile,1);
					$jewel3 = unpack("S",$jewel3);
					$jewel3 = $jewel3[1];


					$skill1Id = fread($decryptedFile,2);
					$skill1Id = unpack("S",$skill1Id);
					$skill1Id = $skill1Id[1];

					$skill1Points = fread($decryptedFile,2);
					$skill1Points = unpack("S",$skill1Points);
					$skill1Points = $skill1Points[1];

					$skill2Id = fread($decryptedFile,2);
					$skill2Id = unpack("S",$skill2Id);
					$skill2Id = $skill2Id[1];

					$skill2Points = fread($decryptedFile,2);
					$skill2Points = unpack("S",$skill2Points);
					$skill2Points = $skill2Points[1];

					$unk9 = fread($decryptedFile,4);
					$unk9 = unpack("C4",$unk9);
					$unk9 = $unk9[1];

					$unk10 = fread($decryptedFile,4);
					$unk10 = unpack("C4",$unk10);
					$unk10 = $unk10[1];


					$cell['itemType'] = $itemType; 				//1 byte
					$cell['slots'] = $slots; 					//1 byte
					$cell['equipId'] = $equipId;				//2 byte

					$cell['unk1'] = $unk1;						//1 byte }
																//       } Id de subset de arma legendaria ???
					$cell['unk2'] = $unk2;						//1 byte }
																//Subset: Estadísticas preparadas para las
																// armas y armaduras oxidadas
																//Cada subset puede variar en afilado, PH, elemento,etc...

					$cell['jewel1'] = $jewel1;					//2 byte
					$cell['jewel2'] = $jewel2;					//2 byte
					$cell['jewel3'] = $jewel3;					//2 byte

					$cell['skill1Id'] = $skill1Id;				//2 byte
					$cell['skill1Points'] = $skill1Points;		//2 byte

					$cell['skill2Id'] = $skill2Id;				//2 byte
					$cell['skill2Points'] = $skill2Points;		//2 byte

					$cell['unk9'] = $unk9;						//4 byte => byte 0x20 Mejora? 0x0C => Salud
																//							  0x04 => Ataque
					$cell['unk10'] = $unk10;					//4 byte

					$row['col'.$k] = $cell;

    			}
    			$page['row'.$j] = $row;
			
    		}

    		$box['page'.$i] = $page;
    		
    	}

		$box = json_encode($box);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $box;
    }

    public function setEquipmentBoxAtSlot($equipment,$slot,$user){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	$jewels = array(

    		"jewel1" => 0,
    		"jewel2" => 0,
    		"jewel3" => 0,
    	);

    	$data1 = fread($decryptedFile, self::EQUIP_BOX_OFFSET+(28*$slot));
    	$readed+= strlen($data1);
    	fwrite($editFile, $data1);
    	
    	fwrite($editFile, pack("C",$equipment['itemType']));
    	fwrite($editFile, pack("C",$equipment['slots']));
    	fwrite($editFile, pack("S",$equipment['equipId']));
    	fwrite($editFile, pack("C2",0,0));
    	foreach ($jewels as $jewel => $id) {
    		fwrite($editFile, pack("S",$id));
    	}
    	fwrite($editFile, pack("S",$equipment['skill1Id']));
    	fwrite($editFile, pack("S",$equipment['skill1Points']));
    	fwrite($editFile, pack("S",$equipment['skill2Id']));
    	fwrite($editFile, pack("S",$equipment['skill2Points']));
    	fwrite($editFile, pack("C8",0,0,0,0,0,0,0,0));

    	fseek($decryptedFile,(self::EQUIP_BOX_OFFSET+(28*$slot)+28));
    	$readed+= 28;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(self::EQUIP_BOX_OFFSET+(28*$slot)+28) ) );
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

    public function getItemPouch(){

    	$puch = array();
    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $puch;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	fseek($decryptedFile,self::ITEM_POUCH_OFFSET);

    	for($i=0;$i<4;$i++){


    		$page = array();
    		
    		for($j=0;$j<8;$j++){
    			
				$cell = array();
	    		$itemId = fread($decryptedFile,2);
				$itemId = unpack("v",$itemId);
				$itemId = $itemId[1];

				$itemUnits = fread($decryptedFile,2);
				$itemUnits = unpack("v",$itemUnits);
				$itemUnits = $itemUnits[1];

				$cell['itemId'] = $itemId;
				$cell['units'] = $itemUnits;
				$row['cell'.$j] = $cell;

			
    		}

    		$puch['page'.$i] = $page;
    		
    	}

		$puch = json_encode($puch);
		//echo "HunterName: ".$name;
		fclose($decryptedFile);
		return $puch;
    }

    public function getItemPouchAtSlot($slot,$user,$toJSON=false){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return ($toJSON) ? json_encode(array()) : 0;
    	$puch = $this->getItemPuch($user);
    	$puch =json_decode($box);
    	$page = floor($slot/4);
    	$cell = $slot%8;

    	$i = $puch->{"page".$page}->{"cell".$cell};

    	$item = array();
    	$item['id'] = $i->itemId;
    	$item['uds'] = $i->units;

		return ($toJSON) ? json_encode($item) : $i;
    }

    public function setItemPouchAtSlot($itemId,$uds,$slot,$user){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
    	$readed = 0;
    	$decryptedFile = fopen($user->getUploadDir()."/decrypted.bin", "rb");
    	$editFile = fopen($user->getUploadDir()."/decrypted_edit.bin", "w+b");

    	$data1 = fread($decryptedFile, self::ITEM_POUCH_OFFSET+(4*$slot));
    	$readed+= strlen($data1);
    	fwrite($editFile, $data1);
    	
    	fwrite($editFile, pack("S",$itemId));
    	fwrite($editFile, pack("S",$uds));

    	fseek($decryptedFile,(self::ITEM_POUCH_OFFSET+(4*$slot)+4));
    	$readed+= 4;

    	$data1 = fread($decryptedFile, (filesize($user->getUploadDir()."/decrypted.bin")-(self::ITEM_POUCH_OFFSET+(4*$slot)+4) ) );
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

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
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

    	$slots = 3;

		for($j=1;$j<=15;$j++){

			for ($k=1; $k <=100 ; $k++) { 
				$equipType = $j;
				$equipId = $k;
    			fwrite($editFile, pack("C",$equipType));
    			fwrite($editFile, pack("C",$slots));
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

    public function setAllArmors($user,$type){

    	if(!file_exists($user->getUploadDir()."/decrypted.bin"))
    		return $this;
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
    		$equipType = $type;
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


///??========================




	private function bytesSwap($string){
		
		$bytes = unpack("C*",$string);
		
		$byte0 = chr($bytes[1]);
		$byte1 = chr($bytes[2]);
		$byte2 = chr($bytes[3]);
		$byte3 = chr($bytes[4]);
		return $byte3.$byte2.$byte1.$byte0;
	}

	private function _xor($buff, $key){
	    $buff2 = "";
		$len = strlen($buff);
		for($i=0;$i<$len;$i=$i+2){
			if($key == 0)
				$key = 1;
			$key = $key * 0xb0 % 0xff53;
			
			$bShort = unpack("v",$buff[$i].$buff[$i+1]);
			
			$bShort = $bShort[1] & 0xFFFF;
			$bShort ^= $key;
			$chars = unpack("C*", pack("v", $bShort));
			$buff2.= chr($chars[1]);
			$buff2.= chr($chars[2]);
			
		}

		return $buff2;
	}

	public function MH4Decrypt($file,$fileOut,$controller){

		if(file_exists($file)){
			$user = $controller->getUser();
			$f = fopen($file,"rb");
			$buffer = fread($f,filesize($file));
		}else{
			return false;
		}

		$len = strlen($buffer);
		$x=0;
		$buff2 = "";
		while($x<$len){
			
			$buff2.= $this->bytesSwap($buffer[$x].$buffer[$x+1].$buffer[$x+2].$buffer[$x+3]);
			$x+=4;
		}

		$fish = new Blowfish('blowfish key iorajegqmrna4itjeangmb agmwgtobjteowhv9mope',Blowfish::BLOWFISH_MODE_EBC,Blowfish::BLOWFISH_PADDING_NONE);
		$dec = $fish->decrypt($buff2);

		$buffer = "";
		$x = 0;
		while($x<$len){
			
			$buffer.= $this->bytesSwap($dec[$x].$dec[$x+1].$dec[$x+2].$dec[$x+3]);
			$x+=4;
		}

		$seed = unpack("L",$buffer);
		//var_dump($seed[1] >> 16);die;
		$seed = (($seed[1] >> 16) & 0xFFFF);
		//remove first 4 bytes;
		$buffer = substr($buffer,4);
		$buff2 = "";
		//var_dump($buffer);die;
		$buff2 = $this->_xor($buffer,$seed);
		$csum = unpack("L",$buff2);
		$csum = $csum[1];
		$buff2 = substr($buff2,4);
		$bytes = unpack("C*",$buff2);
		$SUM = 0;
		$bLen = count($bytes);
		for($i=1;$i<=$bLen;$i++){
			$SUM += $bytes[$i];
		}
		if($csum != $SUM & 0xffffffff)
		    echo 'Invalid checksum in header.';

		$fd = fopen($fileOut,"wb");

		//echo $buff2;
		fwrite($fd,$buff2);
		fclose($fd);
		fclose($f);
		return true;
	}

	public function MH4Encrypt($file,$fileOut){

		$f = null;
		$buffer ="";
		if(file_exists($file)){
			$f = fopen($file,"rb");
			$buffer = fread($f,filesize($file));
		}else{
			return false;
		}

		$len = strlen($buffer);

		$bytes = unpack("C*",$buffer);
		$cSUM = 0;
		$bLen = count($bytes);
		for($i=1;$i<=$bLen;$i++){
			$cSUM += $bytes[$i];
		}
		$fish = new Blowfish('blowfish key iorajegqmrna4itjeangmb agmwgtobjteowhv9mope',Blowfish::BLOWFISH_MODE_EBC,Blowfish::BLOWFISH_PADDING_NONE);

		$buff2 = "";
		$cSUM = unpack("C*", pack("L", $cSUM));
		$buff2.= chr($cSUM[1]).chr($cSUM[2]).chr($cSUM[3]).chr($cSUM[4]);
		$buff2.= $buffer;
		$seed = (mt_rand(0,(2^16-1)) & 0xFFFF);
		$buffer = "";
		$fSeed = (($seed << 16) + 0x10);
		$fSeed = unpack("C*", pack("L", $fSeed));
		$buffer .= chr($fSeed[1]).chr($fSeed[2]).chr($fSeed[3]).chr($fSeed[4]);


		$buffer .= $this->_xor($buff2,$seed);
		$buff2 = "";
		$x=0;
		$len = $len+8;
		while($x<$len){
			
			$buff2.= $this->bytesSwap($buffer[$x].$buffer[$x+1].$buffer[$x+2].$buffer[$x+3]);
			$x+=4;
		}
		$enc = $fish->encrypt($buff2);
		$buffer = "";
		$x = 0;
		while($x<$len){
			
			$buffer.= $this->bytesSwap($enc[$x].$enc[$x+1].$enc[$x+2].$enc[$x+3]);
			$x+=4;
		}

		$fd = fopen($fileOut,"wb");

		//echo $buff2;
		fwrite($fd,$buffer);
		fclose($fd);
		fclose($f);
		return true;


	}
}