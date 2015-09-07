<?php
echo phpinfo();die;
include("mega.class.php");
ini_set('max_execution_time', 300);
ini_set('safe_mode', 'Off');
ini_set('open_basedir', 'none');

$mega = new MEGA();
//$mega->user_login_session("drodriguez816@gmail.com", "didacyeva2304");


$file_info = $mega->public_file_info_from_link("https://mega.nz/#F!tN8DmKZK!MMURoVAkhjgeLqAXAHANXQ");

$filepath = $mega->public_file_save($file_info['ph'], $file_info['key']);
echo 'File saved in ' . $filepath;