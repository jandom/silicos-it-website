<?php

if (!function_exists('mailme'))
{
	function mailme($msg)
	{
		require_once "Mail.php";
	
		$fr = "Silicos-it website <website@silicos-it.com>";
		$to = "Hans De Winter <hans@silicos-it.com>";
		$ho = "mail.silicos-it.com";
		$us = "website+silicos-it.com";
		$pw = "xxxxxx";
		$su = "Message from www.silicos-it.com";
		$he = array('From' => $fr, 'To' => $to, 'Subject' => $su);
		$sm = Mail::factory('smtp', array('host' => $ho, 'auth' => true, 'port' => 2626, 'username' => $us, 'password' => $pw));
	
		date_default_timezone_set("Europe/Brussels");
	
		$bo  = "INFO\n";
		$bo .= "----\n";
		$bo .= "TIME:............" . date("D, M j, Y, G:i:s T", time())."\n";
		$bo .= "PHP_SELF:........" . $_SERVER['PHP_SELF']."\n";
		$bo .= "SCRIPT_NAME:....." . $_SERVER['SCRIPT_NAME']."\n";
		$bo .= "SCRIPT_FILENAME:." . $_SERVER['SCRIPT_FILENAME']."\n";
		$bo .= "REQUEST_TIME:...." . date("D, M j, Y, G:i:s T", $_SERVER['REQUEST_TIME'])."\n";
		$bo .= "REQUEST_URI:....." . $_SERVER['REQUEST_URI']."\n";
		$bo .= "DOCUMENT_ROOT:..." . $_SERVER['DOCUMENT_ROOT']."\n";
		$bo .= "HTTP_REFERER:...." . $_SERVER['HTTP_REFERER']."\n";
		$bo .= "HTTP_HOST:......." . $_SERVER['HTTP_HOST']."\n";
		$bo .= "HTPP_USER_AGENT:." . $_SERVER['HTPP_USER_AGENT']."\n";
		$bo .= "REMOTE_ADDR:....." . $_SERVER['REMOTE_ADDR']."\n";
		$bo .= "REMOTE_PORT:....." . $_SERVER['REMOTE_PORT']."\n";
		$bo .= "REMOTE_HOST:....." . gethostbyaddr($_SERVER['REMOTE_ADDR'])."\n";
		$bo .= "SERVER_PORT:....." . $_SERVER['SERVER_PORT']."\n";
		$bo .= "REMOTE_USER:....." . $_SERVER['REMOTE_USER']."\n";
		$bo .= "QUERY_STRING:...." . $_SERVER['QUERY_STRING']."\n";
		$bo .= "PATH_INFO:......." . $_SERVER['PATH_INFO']."\n";
		$bo .= "ORIG_PATH_INFO:.." . $_SERVER['ORIG_PATH_INFO']."\n";
		$bo .= "\n";
		$bo .= "MESSAGE\n";
		$bo .= "-------\n";
		$bo .= $msg . "\n";
		$bo .= "\n";
	
		$mail = $sm->send($to, $he, $bo);
	}
}
?>
