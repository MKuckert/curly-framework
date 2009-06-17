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
	imagealphablending( $tmp, true );
	
	// ---

$x = 20;
$y = 30;

$box = imagettfbbox( 12, 0, 'C:/WINDOWS/Fonts/ARIAL.TTF', 'Der große Text' );
imagettftext( $img, 12, 0, $x, $y, imagecolorallocatealpha( $img, 255, 0, 0, 0 ), 'C:/WINDOWS/Fonts/ARIAL.TTF', 'Der große Text' );

$col = imagecolorallocate( $img, 0, 255, 0 );
imageline( $img, $x+$box[0], $y+$box[1], $x+$box[2], $y+$box[3], $col );
imageline( $img, $x+$box[2], $y+$box[3], $x+$box[4], $y+$box[5], $col );
imageline( $img, $x+$box[4], $y+$box[5], $x+$box[6], $y+$box[7], $col );
imageline( $img, $x+$box[6], $y+$box[7], $x+$box[0], $y+$box[1], $col );
	
/*
0  	lower left corner, X position
1 	lower left corner, Y position
2 	lower right corner, X position
3 	lower right corner, Y position
4 	upper right corner, X position
5 	upper right corner, Y position
6 	upper left corner, X position
7 	upper left corner, Y position
*/
	
	// ---
	
	header( "content-type: image/png" );
	imagepng( $img );
?>