<?php

if (!function_exists('root_dir'))
{
	function root_dir()
	{

		if (($_SERVER['SERVER_NAME'] == 'www.silicos-it.com') or ($_SERVER['SERVER_NAME'] == 'silicos-it.com'))
		{
			return $_SERVER['DOCUMENT_ROOT'];
		}
		else if ($_SERVER['SERVER_NAME'] == 'localhost')
		{
			return $_SERVER['DOCUMENT_ROOT'] . "/Silicos-it";
		}
	}
}

if (!function_exists('static_dir'))
{
	function static_dir()
	{

		if (($_SERVER['SERVER_NAME'] == 'www.silicos-it.com') or ($_SERVER['SERVER_NAME'] == 'silicos-it.com'))
		{
			return root_dir() . "/_static";
		}
		else if ($_SERVER['SERVER_NAME'] == 'localhost')
		{
			return root_dir() . "/_static";
		}
	}
}

if (!function_exists('download_dir'))
{
	function download_dir()
	{

		if (($_SERVER['SERVER_NAME'] == 'www.silicos-it.com') or ($_SERVER['SERVER_NAME'] == 'silicos-it.com'))
		{
			return root_dir() . "/_downloads";
		}
		else if ($_SERVER['SERVER_NAME'] == 'localhost')
		{
			return root_dir() . "/_downloads";
		}
	}
}

if (!function_exists('php_dir'))
{
	function php_dir()
	{

		if (($_SERVER['SERVER_NAME'] == 'www.silicos-it.com') or ($_SERVER['SERVER_NAME'] == 'silicos-it.com'))
		{
			return root_dir() . "/_php";
		}
		else if ($_SERVER['SERVER_NAME'] == 'localhost')
		{
			return root_dir() . "/_php";
		}
	}
}

if (!function_exists('root_url'))
{
	function root_url()
	{

		if (($_SERVER['SERVER_NAME'] == 'www.silicos-it.com') or ($_SERVER['SERVER_NAME'] == 'silicos-it.com'))
		{
			return "http://" . $_SERVER['SERVER_NAME'];
		}
		else if ($_SERVER['SERVER_NAME'] == 'localhost')
		{
			return "http://" . $_SERVER['SERVER_NAME'] . "/Silicos-it";
		}
	}
}

if (!function_exists('download_url'))
{
	function download_url()
	{

		if (($_SERVER['SERVER_NAME'] == 'www.silicos-it.com') or ($_SERVER['SERVER_NAME'] == 'silicos-it.com'))
		{
			return root_url() . "/_downloads";
		}
		else if ($_SERVER['SERVER_NAME'] == 'localhost')
		{
			return root_url() . "/_downloads";
		}
	}
}

if (!function_exists('php_url'))
{
	function php_url()
	{

		if (($_SERVER['SERVER_NAME'] == 'www.silicos-it.com') or ($_SERVER['SERVER_NAME'] == 'silicos-it.com'))
		{
			return root_url() . "/_php";
		}
		else if ($_SERVER['SERVER_NAME'] == 'localhost')
		{
			return root_url() . "/_php";
		}
	}
}

if (!function_exists('mysql_host'))
{
	function mysql_host()
	{

		if (($_SERVER['SERVER_NAME'] == 'www.silicos-it.com') or ($_SERVER['SERVER_NAME'] == 'silicos-it.com'))
		{
			return "localhost";
		}
		else if ($_SERVER['SERVER_NAME'] == 'localhost')
		{
			return "www.silicos-it.com";
		}
	}
}

?>
