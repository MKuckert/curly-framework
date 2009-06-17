<?php
	
	$original = imagecreatefrompng ( 'test.png' );
	
	$origX = imagesx( $original );
	$origY = imagesy( $original );
	$newY = intval( $origY * 2 );
	
	$image = imagecreatetruecolor ( $origX, $newY );
	imagefill( $image, 0, 0, imagecolorallocate( $image, 255, 255, 255 ) );
	imagecopyresized ( $image, $original, 0, 0, 0, 0, $origX, $origY, $origX, $origY );
	
	$alpha = 50;

	$time = microtime ( true );
	
	for ( $y = 0; $alpha<127; $y++ ) {
		for ( $x = 0; $x < $origX; $x++ ) {
			
			$color = imagecolorat( $image, $x, $origY - $y );
			$newColor = imagecolorallocatealpha ( $image, ( $color & 0xFF0000 ) >> 16, ( $color & 0x00FF00 ) >> 8, ( $color & 0x0000FF ), $alpha );
			
			imagesetpixel ( $image, $x, $y+$origY, $newColor );
		}
		
		$alpha += 6;
	}
	
	$end = round( ( microtime ( true ) - $time ), 4 );
	
	imagettftext ( $image, 10, 0, 5, 15, imagecolorallocate ( $image, 255, 0, 0 ), 'arial.ttf', 'Zeit: ' . $end . 'ms' );
	
	header( 'Content-type: image/png' ); 
	imagepng ( $image );
	
?>