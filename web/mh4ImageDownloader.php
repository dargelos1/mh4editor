<?php

/*PHP Script to get all the images from MH4*/
ini_set("error_reporting",E_ALL);
error_reporting(E_ALL);
$titles = "";

$maxNum = 999;
$limitTitlesPerRequest = 50;
$maxBuffer = 18; //18 x 10 without crashing the request to the server
$letters = "abcdefghij";

$totalTitles = 0;
/* generate all possible icons*/

for($i=1;$i<=999;$i++){

	$num = $i;
	if($num<10){
		$num = "00".$num;
	}else if($num<100){
		$num = "0".$num;
	}

	for($j = 0;$j<10;$j++){
		$titles.="File:ItemIcon".$num.$letters[$j].".png|";
		$totalTitles++;
	}
}

/*Once all possible title icons are generated, send to wikia api to retrieve the images*/
/* WARNING: Max titles supported by the API is 50, send 5x10 by each time and then generate the images*/
/* monsterhunter.wikia.com/api.php?action=query&titles=$titles&prop=imageinfo&iiprop=url&format=json*/

$x = 0;
$titlesArray = explode("|",$titles);
while($x<$totalTitles){

	/*1rst Prepare the buffer*/
	$titlesToSend = "";
	for ($i=0; $i < 49; $i++) {

		$titlesToSend.=$titlesArray[$i]."|";
	}
	echo "=>Titles to send.... $titlesToSend<br>";
	/*2on Once the buffer is ready, send it*/
	$mh4web = fopen("http://monsterhunter.wikia.com/api.php?action=query&titles=".$titlesToSend."&prop=imageinfo&iiprop=url&format=json","r");

	/*3rst Now get the response of the server as JSON*/
	$JSON ="";
	$line = 0;
	while( ($l =(fgets($mh4web))) != NULL ){
		$JSON.= $l;
		++$line;
		echo "==>Reading from web.... LINE:$line<br>";
		echo "===><pre>".var_dump($l)."</pre>";
		flush();
		//ob_flush();
	}
	//die;
	//var_dump($JSON);die;
	/*4rth Once we get the JSON, now he have to parse and create the object*/

	$apiResponse = json_decode($JSON);
	var_dump($apiResponse->query->pages->{'-2'});die;

	/*5ith Now that we got the response, we must loop throught each page object wich is an array
	and get the image url to obtain the image*/

	$x++;
}

//echo $titles;die;
echo $JSON;