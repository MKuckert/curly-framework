<?php
	
	include dirname( __FILE__ )."/config.php";
	
	Doctrine::loadModels( Stats::configs()->models->path );
	
	$item = new StatisticItem();
	$item->userAgent = $_SERVER['HTTP_USER_AGENT'];
	$item->requestUri = $_SERVER['REQUEST_URI'];
	$item->requestTime = date( 'Y.m.d H:i:s', intval( $_SERVER['REQUEST_TIME'] ) );
	$item->save();
	
?>