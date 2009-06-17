<?php
	error_reporting( E_ALL );
	function __autoload( $class ) {
		include dirname( __FILE__ ) . '/../' . $class . '.php';
	}
	
	echo "<pre>";
	
	$assert = '#Brush[]';
	$brush = new Brush();
	echo $brush;
	echo ' -> '.( $brush->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Brush[Color #Color[R255 G255 B255 A0]]';
	$brush = new Brush();
	$brush->Color = new Color( 255, 255, 255 );
	echo $brush;
	echo ' -> '.( $brush->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	echo "</pre>";
?>