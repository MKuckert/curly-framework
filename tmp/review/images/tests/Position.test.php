<?php
	error_reporting( E_ALL );
	function __autoload( $class ) {
		include dirname( __FILE__ ) . '/../' . $class . '.php';
	}
	
	echo "<pre>";
	
	// ---
	
	$assert = '#Position[X200 Y100]';
	$position = new Position();
	$position->X = 200;
	$position->Y = 100;
	echo $position;
	echo ' -> '.( $position->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Position[X200 Y100]';
	$position = new Position();
	$position->X = '200asdasd';
	$position->Y = '100asd';
	echo $position;
	echo ' -> '.( $position->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	echo "</pre>";
?>