<?php
define('DOCROOT_PATH', dirname(__FILE__));

$bootstrapFile='../bootstrap.php';
if(isset($_REQUEST['app']) and is_string($_REQUEST['app'])){
	$tmp='../bootstrap.'.basename($_REQUEST['app']).'.php';
	if(is_file($tmp)){
		$bootstrapFile=$tmp;
	}
}
if(!is_file($bootstrapFile)) die('No bootstrap file found');
require_once $bootstrapFile;


// TEST:
/*foreach(Zend_Registry::getInstance() as $key=>$value){
	Zend_Debug::dump($value, $key.":");
}*/

Zend_Controller_Front::getInstance()->dispatch();