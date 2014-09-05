<?php

require_once "mailme.php";
require_once "settings.php";


// Check if requested redirect url really is OK
function inputIsOK($url)
{
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
	// Look up in database
	$result = mysql_query("SELECT id from links WHERE url = '$url' AND is_online = 1");
	if (mysql_num_rows($result) == 1)
	{
		$row = mysql_fetch_row($result);
		$id = $row[0];
		$updates = mysql_query("UPDATE links SET hits = hits + 1 WHERE id = '$id'");
		if ($updates != 1)
		{
			return false;
		}
		else
		{
			$ra = $_SERVER['REMOTE_ADDR'];
			$rh = gethostbyaddr($ra);
			$query = "INSERT INTO links_log SET links_id = '$id', date = NOW(), remote_address = '$ra', remote_host = '$rh';";
			$result = mysql_query($query);
			if (!$result)
			{
				mailme($_SERVER['PHP_SELF'] . " -> database insert failed: " . mysql_error());
				return false;
			}
			else
			{
				return true;
			}
		}
	}
	else
	{
		return false;
	}
}


if (inputIsOK($_GET['url']))
{
	$url = $_GET['url'];
	header("Location: " . $url);
}
else
{
	mailme($_SERVER['PHP_SELF'] . " -> requested url is not valid: " . $_GET['url']);
	header("Location: " . root_url() . "/links/links.html");
	exit(1);
}
?>
