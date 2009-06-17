<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'On');
$testname='Test_Tokens';


defined('BOOTPATH') or define('BOOTPATH', dirname(__FILE__));

// Includepfad erweitern
set_include_path(get_include_path().PATH_SEPARATOR.BOOTPATH);

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
$class=$testname;

if(!is_file($testpath)) {
	die('Failed to find test '.$testname.' ('.$testpath.').');
}

require_once BOOTPATH.DIRECTORY_SEPARATOR.$testpath;

// Test ausführen
$test=new $class(array_slice($argv, 2));
echo $test->run();