<?php
	
	/**
	 * Gibt eine Nachricht zurück, bei der Platzhalter durch die übergebenen
	 * Variablen ersetzt werden.
	 * 
	 * @author Martin Kuckert
	 * @package GDImage
	 * @since 18.11.2007
	 * @copyright Copyright &copy; 2007 Martin Kuckert
	 * @license New BSD License
	 * @version 1.0
	 * 
	 * @return string
	 * @param string Nachrichten-ID
	 * @param mixed Platzhalterwerte
	 */
	function exceptionMessage( $msgid, $attrs=array() ) {
		static $messages = array(
			'type.string'						=> 'Parameter {0} has to be of type string',
			'type.integer'						=> 'Parameter {0} has to be of type integer',
			'type.float'						=> 'Parameter {0} has to be of type float',
			'image.not_initialized'				=> 'There was no resource created yet',
			'file.not_exist'					=> 'File "{0}" does not exist',
			'file.not_readable'					=> 'File "{0}" is not readable',
			'file.not_writable'					=> 'File "{0}" is not writable',
			'directory.not_writable'			=> 'Directory "{0}" is not writable',
			'file.not_readable_or_no_picture'	=> 'Unable to read from file "{0}" or it is no valid picture',
			'image.support.gif_read'			=> 'Gif read is not supported',
			'image.support.gif_write'			=> 'Gif write is not supported',
			'image.support.jpeg'				=> 'Jpeg is not supported',
			'image.support.png'					=> 'Png is not supported',
			'image.support.not_supported'		=> '{0} is not supported',
			'image.wrong_type'					=> 'File "{0}" is of wrong image type',
			'image.create.from_file'			=> 'Unable to create a gd image resource from file "{0}"',
			'image.create.from_giffile'			=> 'Unable to create a gd image resource from gif file "{0}"',
			'image.create.from_jpegfile'		=> 'Unable to create a gd image resource from jpeg file "{0}"',
			'image.create.from_pngfile'			=> 'Unable to create a gd image resource from png file "{0}"',
			'image.create.from_string'			=> 'Unable to create a gd image resource from string',
			'image.create.palette'				=> 'Unable to create a palette gd image resource!',
			'image.create.truecolor'			=> 'Unable to create a true color gd image resource!',
			'image.load.unable'					=> 'Unable to load image from file or string',
			'image.jpeg.quality'				=> 'The jpeg quality has to be a value between 0 and 100',
			'image.no_transparent_color'		=> 'There is no transparent color set for the image',
			'position.out_of_bounds'			=> 'The position {0},{1} is not inside the image',
			'palette.interval'					=> 'The number of colors in a palette image has to be a value between 1 and 255',
			'convolution.matrix.3x3'			=> 'The matrix for a convolution have to be of the size 3x3',
			'filter.not_valid'					=> 'The given filter is unknown',
			'image.copy.transparency_range'		=> 'The transparency for copy-functions have to be a value between 0 and 100'
		);
		
		if( !is_string( $msgid ) or !isset( $messages[$msgid] ) ) {
			return( $msgid );
		}
		
		if( !is_array( $attrs ) ) {
			$attrs = array( $attrs );
		}
		$msg = $messages[$msgid];
		$i = 0;
		foreach( $attrs as $attr ) {
			$msg = str_replace( '{'.$i.'}', $attr, $msg );
			$i++;
		}
		return( $msg );
	}
?>