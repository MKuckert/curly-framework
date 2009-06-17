<?php
	
	$original = imagecreatefrompng( 'image.png' );
	$image = imagecreatetruecolor ( imagesx ( $original ), imagesy ( $original ) * 1.3 );
	
	imagecopyresized ( $image, $original, 0, 0, 0, 0, imagesx ( $original ), imagesy ( $original ), imagesx ( $original ), imagesy ( $original ) );
	
	$alpha = 50;
	
	$time = microtime ( true );
	
	for ( $y = imagesy ( $original ); $y < imagesy ( $image ); $y ++ ) {
		for ( $x = 0; $x < imagesx ( $image ); $x ++ ) {
			
			$color = imagecolorat ( $image, imagesx ( $image ) - $x - 1, ( imagesy ( $image ) - $y ) + ( 0.7 * imagesy ( $original ) ) );
			$newColor = imagecolorallocatealpha ( $image, ( $color & 0xFF0000 ) >> 16, ( $color & 0x00FF00 ) >> 8, ( $color & 0x0000FF ), $alpha );
			
			imagesetpixel ( $image, $x, $y, $newColor );
		}
		
		$alpha += 3;
	}
	
	$end = round( ( microtime ( true ) - $time ), 4 );
	
	imagettftext ( $image, 10, 0, 5, 15, imagecolorallocate ( $image, 255, 0, 0 ), 'arial.ttf', 'Zeit: ' . $end . 'ms' );
	
	header( 'Content-type: image/png' ); 
	imagepng ( $image );
	
?>