<?php

if(!isset($testname)) {
	die('Don`t call this file directly');
}

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'On');

defined('BOOTPATH') or define('BOOTPATH', dirname(__FILE__));

// Includepfad erweitern
$file='..'.DIRECTORY_SEPARATOR.'INCLUDEPATH';
if(is_file($file)) {
	$rows=file($file);
	foreach($rows as $row) {
		list($key, $path)=explode(':', $row, 2);
		$path=trim($path);
		define('INCLUDEPATH_'.strtoupper($key), $path);
		set_include_path($path.PATH_SEPARATOR.get_include_path());
	}
}
set_include_path(get_include_path().PATH_SEPARATOR.BOOTPATH.PATH_SEPARATOR.dirname(BOOTPATH));

function __autoload($class) {
	$file=str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
	
	$includePath=explode(PATH_SEPARATOR, get_include_path());
	foreach($includePath as $token) {
		$path=$token.DIRECTORY_SEPARATOR.$file;
		if(is_file($path)) {
			require_once $path;
			return(true);
		}
	}
	
	return(false);
}

// Testname auflösen und ausführen
$testpath=str_replace('_', DIRECTORY_SEPARATOR, $testname).'.php';
$class='Test_'.$testname;

if(!is_file($testpath)) {
	die('Failed to find test '.$testname.' ('.$testpath.').');
}

require_once BOOTPATH.DIRECTORY_SEPARATOR.$testpath;

// Test ausführen
$test=new $class();
return $test->run();