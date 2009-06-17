<?php
	
	/**
	 * Canvas
	 */
	class Canvas {
		
		/**
		 * @var resource
		 */
		private $handle = null;
		
		public function getResource() {
			return( $this->handle );
		}
		
		public function __construct( $res ) {
			if( !is_resource( $res ) or get_resource_type( $res )!='gd' ) {
				throw new ImageException( 'The given argument is no valid image resource' );
			}
			$this->handle = $res;
		}
		
		/**
		 * Konvertiert dieses Bild in ein Palettenbild.
		 * Es muss bereits ein Bild erstellt oder geladen worden sein.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return Canvas
		 * @param boolean true, wenn ein Dithering auf dem Bild angewendert werden
		 * soll.
		 * @param integer maximale Anzahl Farben in der Palette
		 * @see http://php.net/imagetruecolortopalette
		 */
		public function toPalette( $dither=true, $colors=256 ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			if( !$this->isTrueColor() ) {
				return( true );
			}
			
			if( !is_int( $colors ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'colors' ) );
			}
			
			if( $colors<1 or $colors>255 ) {
				throw new InvalidArgumentException( exceptionMessage( 'palette.interval' ) );
			}
			
			imageTrueColorToPalette( $this->handle, (bool)$dither, $colors );
			
			return $this;
		}
		
		/**
		 * Konvertiert dieses Bild in ein TrueColor-Bild.
		 * Durch diese Methode wird das Alphablending auf false und SaveAlpha auf
		 * true gesetzt.
		 * 
		 * @return Canvas
		 * @see http://php.net/imagecreatetruecolor
		 */
		public function toTrueColor() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			if( !$this->isTrueColor() ) {
				return( true );
			}
			
			$w = imageSX( $this->handle );
			$h = imageSY( $this->handle );
			
			$tmp = imageCreateTrueColor( $w, $h );
			imageSaveAlpha( $tmp, true );
			imageAlphaBlending( $tmp, false );
			
			for( $x=0; $x<$w; $x++ ) {
				for( $y=0; $y<$h; $y++ ) {
					imageSetPixel( $tmp, $x, $y, imageColorAt( $this->handle, $x, $y ) );
				}
			}
			
			imageDestroy( $this->handle );
			$this->handle = $tmp;
			
			return $this;
		}
		
		/**
		 * Überprüft, ob dieses Bild ein Palettenbild ist
		 * 
		 * @return boolean true, wenn es sich um ein Palettenbild handelt
		 */
		public function isPalette() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( !imageIsTrueColor( $this->handle ) );
		}
		
		/**
		 * Überprüft, ob dieses Bild ein TrueColor-Bild ist
		 * 
		 * @return boolean true, wenn es sich um ein TrueColor-Bild handelt
		 * @see http://php.net/imageistruecolor
		 */
		public function isTrueColor() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( imageIsTrueColor( $this->handle ) );
		}
		
		/**
		 * Ermittelt die Anzahl der Farben im Bild. Handelt es sich bei diesem Bild
		 * um ein TrueColor-Bild, wird immer 0 zurückgegeben, da alle möglichen
		 * Farben im Bild vorhanden sein können.
		 * 
		 * @return integer
		 * @see http://php.net/imagecolorstotal
		 */
		public function getTotalColors() {
			return( imageColorsTotal( $this->handle ) );
		}
		
		/**
		 * Gibt die Farbe im Bild an der übergebenen Position zurück.
		 * Wirft OutOfBoundsException, wenn die Position nicht im Bild enthalten ist.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException, OutOfBoundsException
		 * @return Color
		 * @param integer/Position Die X-Position als Integer oder Position-Objekt
		 * @param integer Die Y-Position
		 * @see http://php.net/imagecolorat
		 */
		public function getColorAt( $x, $y=null ) {
			if( $x instanceof Point ) {
				$x = $x->getX();
				$y = $x->getY();
			}
			
			if( !is_int( $x ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'x' ) );
			}
			if( !is_int( $y ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'y' ) );
			}
			
			$res = @imageColorAt( $this->handle, $x, $y );
			if( $res===false ) {
				throw new OutOfBoundsException( 'Point.out_of_bounds', array( $x, $y ) );
			}
			
			return( $this->getColorForIndex( $res ) );
		}
		
		/**
		 * Setzt die Farbe im Bild an der übergebenen Point.
		 * Wirft ImageException, wenn die Point nicht im Bild enthalten ist.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return void
		 * @param Color
		 * @param integer/Point Die X-Point als Integer oder Point-Objekt
		 * @param integer Die Y-Point
		 * @see http://php.net/imagesetpixel
		 */
		public function setColorAt( Color $color, $x, $y=null ) {
			if( $x instanceof Point ) {
				$x = $x->getX();
				$y = $x->getY();
			}
			
			if( !is_int( $x ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'x' ) );
			}
			if( !is_int( $y ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'y' ) );
			}
			
			imageSetPixel( $this->handle, $x, $y, $this->getIndexForColor( $color ) );
		}
		
		/**
		 * Gibt die transparente Farbe des Bildes zurück.
		 * Wirft ImageException, wenn keine transparente Farbe definiert ist
		 * 
		 * @throws ImageException
		 * @return Color
		 * @see http://php.net/imagecolortransparent
		 */
		public function getTransparentColor() {
			$res = imageColorTransparent( $this->handle );
			if( $res<=0 ) {
				throw new ImageException( 'image.no_transparent_color' );
			}
			return( $this->getColorsForIndex( $res ) );
		}
		
		/**
		 * Legt die transparente Farbe des Bildes fest.
		 * 
		 * @return boolean
		 * @param Color
		 * @see http://php.net/imagecolortransparent
		 */
		public function setTransparentColor( Color $color ) {
			imageColorTransparent( $this->handle, $this->getIndexForColor( $color ) );
		}
		
		
		/**
		 * Gibt die Farbe zu einem Farbhandle zurück.
		 * 
		 * @return Color
		 * @param integer
		 * @see http://php.net/imagecolorsforindex
		 */
		private function getColorForIndex( $index ) {
			return( imageColorsForIndex( $this->handle, $index ) );
		}
		
		/**
		 * Gibt den Farbhandle zu einer Farbe zurück. Ist diese Farbe noch nicht im
		 * Bild enthalten, wird diese alloziiert.
		 * 
		 * @return integer
		 * @param Color
		 * @see http://php.net/imagecolorallocatealpha
		 */
		private function getIndexForColor( Color $color ) {
			return( imageColorAllocateAlpha( $this->handle, $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha() ) );
		}
		
		/**
		 * Gibt den exakten oder nächsten möglichen Farbhandle zu einer Farbe zurück.
		 * 
		 * @return integer
		 * @param Color
		 * @see http://php.net/imagecolorresolvealpha
		 */
		private function getClosestIndexForColor( Color $color ) {
			return( imageColorResolveAlpha( $this->handle, $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha() ) );
		}
		
		/**
		 * Dealloziiert eine Farbe in diesem Bild
		 * 
		 * @return boolean
		 * @param integer Farbindex
		 * @see http://php.net/imagecolordeallocate
		 */
		private function deallocateColor( $color ) {
			return( imageColorDeallocate( $color ) );
		}
		
		/**
		 * Wendet einen Faltungsfilter auf das Bild an.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return boolean
		 * @param array 3x3-Matrix, welche den Faltungsfilter beschreibt
		 * @param float Normalisierungswert
		 * @param float Offsetwert
		 * @see http://php.net/imageconvolution
		 */
		public function convolve( array $matrix, $div, $offset ) {
			// Die Matrix überprüfen
			if( count( $matrix )!=3 ) {
				throw new InvalidArgumentException( exceptionMessage( 'convolution.matrix.3x3' ) );
			}
			
			foreach( $matrix as $firstDimension ) {
				if( count( $firstDimension )!=3 ) {
					throw new InvalidArgumentException( exceptionMessage( 'convolution.matrix.3x3' ) );
				}
				foreach( $firstDimension as $secondDimension ) {
					if( !is_int( $secondDimension ) ) {
						throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'matrix' ) );
					}
				}
			}
			
			// Normalisierungswert überprüfen
			if( !is_float( $div ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.float', 'div' ) );
			}
			
			// Offsetwert überprüfen
			if( !is_float( $offset ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.float', 'offset' ) );
			}
			
			return( imageConvolution( $this->handle, $matrix, $div, $offset ) );
		}
		
		/**
		 * Wendet einen Filter auf das Bild an. Mögliche Filter sind:
		 *  IMG_FILTER_NEGATE, IMG_FILTER_GRAYSCALE, IMG_FILTER_BRIGHTNESS,
		 *  IMG_FILTER_CONTRAST, IMG_FILTER_COLORIZE, IMG_FILTER_EDGEDETECT,
		 *  IMG_FILTER_EMBOSS, IMG_FILTER_GAUSSIAN_BLUR, IMG_FILTER_SELECTIVE_BLUR,
		 *  IMG_FILTER_MEAN_REMOVAL, IMG_FILTER_SMOOTH
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return boolean
		 * @param integer Zu verwendender Filter
		 * @param integer Optionaler Parameter für den Filter
		 * @param integer Optionaler Parameter für den Filter
		 * @param integer Optionaler Parameter für den Filter
		 * @param integer Optionaler Parameter für den Filter
		 * @see http://php.net/imagefilter
		 */
		public function filter( $filter, $arg1=0, $arg2=0, $arg3=0, $arg4=0 ) {
			if( !is_int( $filter ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'filter' ) );
			}
			
			switch( $filter ) {
				// Filter ohne Parameter
				case IMG_FILTER_NEGATE:
				case IMG_FILTER_GRAYSCALE:
				case IMG_FILTER_BRIGHTNESS:
				case IMG_FILTER_EDGEDETECT:
				case IMG_FILTER_EMBOSS:
				case IMG_FILTER_GAUSSIAN_BLUR:
				case IMG_FILTER_SELECTIVE_BLUR:
				case IMG_FILTER_MEAN_REMOVAL:
					return( imageFilter( $this->handle, $filter ) );
				// Filter mit einem Parameter
				case IMG_FILTER_CONTRAST:
				case IMG_FILTER_SMOOTH:
					return( imageFilter( $this->handle, $filter, $arg1 ) );
				// Filter mit 3 oder 4 Parameter
				case IMG_FILTER_COLORIZE:
					return( imageFilter( $this->handle, $filter, $arg1, $arg2, $arg3, $arg4 ) );
				default:
					throw new InvalidArgumentException( exceptionMessage( 'filter.not_valid' ) );
			};
		}
		
		/**
		 * Kopiert das übergebene Bild in dieses Bild hinein.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @todo
		 * @throws InvalidArgumentException
		 * @return boolean
		 * @param GDImage Quellbild
		 * @param Point ZielPoint
		 * @param Point QuellPoint
		 * @param integer Breite des Ausschnittes aus dem Quellbild
		 * @param integer Höhe des Ausschnittes aus dem Quellbild
		 * @param integer Prozentuale Transparenz des Quellbildes. Erlaubt sind Werte
		 * zwischen 0 und 100, wobei 0 für komplette Transparenz steht.
		 * @see http://php.net/imagecopy
		 * @see http://php.net/imagecopymerge
		 */
		public function copy( GDImage $sourceImg, Point $targetPos, Point $sourcePos, $sourceW=null, $sourceH=null, $transparency=100 ) {
			// Quellbild mit kompletter Transparenz?
			if( $transparency==0 ) {
				return( true );
			}
			
			// Wurden keine Quellbildmaße übergeben, volle Maße des Quellbildes verwenden
			if( is_null( $sourceW ) ) {
				$sourceW = $sourceImg->getWidth();
			}
			if( is_null( $sourceH ) ) {
				$sourceH = $sourceImg->getHeight();
			}
			
			// Quellbildmaße auf Integer überprüfen
			if( !is_int( $sourceW ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'sourceW' ) );
			}
			if( !is_int( $sourceH ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'sourceH' ) );
			}
			
			// Transparenzwert überprüfen
			if( !is_int( $transparency ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'transparency' ) );
			}
			else if( $transparency<0 or $transparency>100 ) {
				throw new InvalidArgumentException( exceptionMessage( 'image.copy.transparency_range' ) );
			}
			
			if( $transparency==100 ) {
				return( imageCopy( $this->handle, $sourceImg->getHandle(), $targetPos->getX(), $targetPos->getY(), $sourcePos->getX(), $sourcePos->getY(), $sourceW, $sourceH ) );
			}
			else {
				return( imageCopyMerge( $this->handle, $sourceImg->getHandle(), $targetPos->getX(), $targetPos->getY(), $sourcePos->getX(), $sourcePos->getY(), $sourceW, $sourceH, $transparency ) );
			}
		}
		
		/**
		 * Skaliert dieses Bild.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return boolean
		 * @param integer Neue Breite
		 * @param integer Neue Höhe
		 */
		public function resize( $newWidth, $newHeight ) {
			$w = $this->getWidth();
			$h = $this->getHeight();
			
			$tmp = imageCreateTrueColor( $newWidth, $newHeight );
			imageSaveAlpha( $tmp, true );
			imageAlphaBlending( $tmp, false );
			
			imageFill( $tmp, imageColorAllocateAlpha( $tmp, 255, 255, 255, 127 ) );
			
			imageCopyResampled( $tmp, $this->handle, 0, 0, 0, 0, $newWidth, $newHeight, $w, $h );
			
			imageDestroy( $this->handle );
			
			$this->handle = $tmp;
		}
		
	}
	
?>