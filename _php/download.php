<?php

require_once "mailme.php";
require_once "settings.php";


function inputIsOK($input_file)
{
	// Check if requested download file really is OK
	if (is_dir(download_dir()))
	{
		$handle = opendir(download_dir());
		while (($file = readdir($handle)) !== false)
		{
			if ($file == $input_file)
			{
				return true;
			}
		}
		return false;
	}
	else
	{
		mailme($_SERVER['PHP_SELF'] . " -> could not find download directory: " . download_dir());
		return false;
	}
}


if (inputIsOK($_GET['file']))
{
	// Download
	$fullPath = download_dir() . "/" . $_GET['file'];
	if ($fd = fopen($fullPath, "r"))
	{		
		$path_parts = pathinfo($fullPath);
		$fsize = filesize($fullPath);
		header("Content-type: application/octet-stream");
		header("Content-Disposition: filename=\"" . $path_parts["basename"] . "\"");
		header("Content-length: $fsize");
		header("Cache-control: private");
		while(!feof($fd))
		{
			$buffer = fread($fd, 2048);
			echo $buffer;
		}
	}
	fclose ($fd);
	
	// Update database
	$db_server = mysql_connect(mysql_host(), "silide5_php", "xxxxx");
	if (!$db_server)
	{
		mailme($_SERVER['PHP_SELF'] . " -> unable to connect to MySQL: " . mysql_error());
		exit(1);
	}
	mysql_select_db("silide5_downloads");
	if (!mysql_select_db("silide5_downloads"))
	{
		mailme($_SERVER['PHP_SELF'] . " -> unable to select database: " . mysql_error());
		exit(1);
	}
	$file = $_GET['file'];
	$program = substr($file, 0, strrpos($file, '-'));
	$version = substr($file, strrpos($file, '-') + 1);
	$version = substr($version, 0, strrpos($version, '.tar'));
	$ra = $_SERVER['REMOTE_ADDR'];
	$rh = gethostbyaddr($ra);
	$query = "INSERT INTO downloads SET file = '$file', program = '$program', version = '$version', remote_address = '$ra', remote_host = '$rh'";
	$result = mysql_query($query);
	if (!$result)
	{
		mailme($_SERVER['PHP_SELF'] . " -> database access failed: " . mysql_error());
		exit(1);
	}
	else
	{
		exit(0);
	}
}
else
{
	mailme($_SERVER['PHP_SELF'] . " -> could not find file: " . $fullPath);
	header("Location: " . root_url() . "/tools/tools.html");
	exit(1);
}
?>
