<?php
//API connector between Fort42 API and app symfony2
//They can be accessed globally
/*
	USEFUL FUNCTIONS AND VARIABLES:
	Functions:
		randomToken($length=10) - Generate random token of length 10
		randomUniqueToken($min_length=10) - Generate a random unique token of length 10
		getClientIP() - Find client's real IP (not $_SERVER['REMOTE_ADDR'])
		isEmailValid($email) - check email validity
		encrypt($q) - encrypt a string in a special way
		display_date($format,$time,$gmt) - Display date with gmdate($format) with input $time in "Y-m-d H:i:s" format, the date shown is adjusted according to the user's GMT offset
		format_text($string,$allowedbb=array()) - returns string in plain text format, enter allowed bbcodes it can display, or leave it if none
		linktologin($return) - send user to login page and return to $return after success ($return is usually "http://fort42.cu.cc".$_SERVER["REQUEST_URI"])
		link_username($uid,$unm) - returns '<a href="http://fort42.cu.cc/users/'.$uid.'.'.$unm.'/">'.$unm.'</a>'
		hasPerm($__perm) - checks if the user has $__perm permission
		permValue($__perm) - returns permission value of the given permission id if hasPerm($__perm) returns true
	Global Variables:
		$_GMT - GMT offset
		$_USER_DATA - returns "none" in string if the user is not logged in
		$_USER_DATA['id'] - User's ID
		$_USER_DATA['user'] - Username of the user
		$_USER_DATA['rank'] - Rank ID of the user in string
		$_USER_DATA['nextpost'] - Timestamp of last post
		$_USER_DATA['regis_ip'] - User's registerred IP
		$_USER_DATA['last_ip'] - User's last online IP
		$_USER_DATA['lastlogin'] - User's last online in "Y-m-d H:i:s" format
		$_USER_DATA['reg_date'] - User's registered date-time in "Y-m-d H:i:s" format
*/

function fort42_getUsername(){

	return $GLOBALS['_USER_DATA']['user'];
}

function fort42_getUserData(){

	return $GLOBALS['_USER_DATA'];
}

function fort42_getGMT(){

	return $GLOBALS['_GMT'];
}

function fort42_getUserId(){

	return $GLOBALS['_USER_DATA']['id'];
}

function fort42_getRank(){

	return $GLOBALS['_USER_DATA']['rank'];
}

function fort42_getUserNextPost(){

	return $GLOBALS['_USER_DATA']['nextpost'];
}

function fort42_getUserRegisterIP(){

	return $GLOBALS['_USER_DATA']['regis_ip'];
}

function fort42_getUserLastIP(){

	return $GLOBALS['_USER_DATA']['last_ip'];
}

function fort42_getUserLastLogin(){

	return $GLOBALS['_USER_DATA']['lastlogin'];
}

function fort42_getUserRegistrationDate(){

	return $GLOBALS['_USER_DATA']['reg_date'];
}