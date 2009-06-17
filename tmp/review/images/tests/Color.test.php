<?php
	error_reporting( E_ALL );
	function __autoload( $class ) {
		include dirname( __FILE__ ) . '/../' . $class . '.php';
	}
	
	echo "<pre>";
	
	$assert = '#Color[R255 G255 B255 A0]';
	$color = new Color();
	$color->Red = 0xFF;
	$color->Green = 0xFF;
	$color->Blue = 0xFF;
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Color[R255 G255 B255 A127]';
	$color = new Color();
	$color->Red = 0xFF;
	$color->Green = 0xFF;
	$color->Blue = 0xFF;
	$color->Alpha = 127;
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Color[R255 G255 B255 A127]';
	$color = new Color();
	$color->Red = 'FF';
	$color->Green = 'FF';
	$color->Blue = 'FF';
	$color->Alpha = '7F';
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Color[R255 G0 B0 A0]';
	$color = new Color( 255, 0, 0 );
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Color[R255 G255 B255 A127]';
	$color = new Color( 255, 255, 255, 127 );
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Color[R255 G255 B255 A0]';
	$color = new Color( 'FFFFFF' );
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Color[R255 G255 B255 A127]';
	$color = new Color( '7FFFFFFF' );
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Color[R255 G255 B255 A127]';
	$color = new Color( 'FFFFFF', '7F' );
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Color[R255 G255 B255 A127]';
	$color = new Color( 'FFFFFF', 127 );
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Color[R255 G255 B255 A127]';
	$color = new Color( 'FFFFFF', 'FFFFF' );
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert = '#Color[R0 G0 B0 A0]';
	$color = new Color();
	echo $color;
	echo ' -> '.( $color->__toString()==$assert ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert1 = '#Color[R255 G127 B0 A50]';
	$color1 = new Color( 255, 127, 0, 50 );
	echo $color1;
	echo ' -> '.( $color1->__toString()==$assert1 ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert2 = '#Color[R0 G127 B255 A50]';
	$color2 = new Color( 0, 127, 255, 50 );
	echo $color2;
	echo ' -> '.( $color2->__toString()==$assert2 ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$color1->add( $color2 );
	$assert3 = '#Color[R255 G254 B255 A0]';
	echo $color1;
	echo ' -> '.( $color1->__toString()==$assert3 ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert1 = '#Color[R255 G127 B0 A50]';
	$color1 = new Color( 255, 127, 0, 50 );
	echo $color1;
	echo ' -> '.( $color1->__toString()==$assert1 ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$assert2 = '#Color[R0 G127 B255 A50]';
	$color2 = new Color( 0, 127, 255, 50 );
	echo $color2;
	echo ' -> '.( $color2->__toString()==$assert2 ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	// ---
	
	$color1->sub( $color2 );
	$assert3 = '#Color[R255 G0 B0 A100]';
	echo $color1;
	echo ' -> '.( $color1->__toString()==$assert3 ? 'korrekt' : 'fehlerhaft' );
	echo "\n\n";
	
	echo "</pre>";
?>