<?php
	
	include dirname( __FILE__ )."/config.php";
	
	$frontController = Zend_Controller_Front::getInstance();
	$frontController->setControllerDirectory( Stats::configs()->application->controllers );
	$frontController->setParam( 'env', 'dev' );
	$frontController->dispatch();
?>