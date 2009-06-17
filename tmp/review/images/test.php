<?php
	error_reporting( E_ALL );
	
	$img = imagecreatefromgif( 'image.gif' );
	
	// ---
	
	$w = imagesx( $img );
	$h = imagesy( $img );
	
	$tmp = imagecreatetruecolor( $w, $h );
	imagesavealpha( $tmp, true );
	imagealphablending( $tmp, false );
	
	for( $x=0; $x<$w; $x++ ) {
		for( $y=0; $y<$h; $y++ ) {
			$color = imagecolorat( $img, $x, $y );
			$color = imagecolorsforindex( $img, $color );
			imagesetpixel( $tmp, $x, $y, imagecolorallocatealpha( $tmp, $color['red'], $color['green'], $color['blue'], $color['alpha'] ) );
		}
	}
	
	imagedestroy( $img );
	$img = $tmp;
	
	// ---
	
	header( "content-type: image/png" );
	imagepng( $img );
?>