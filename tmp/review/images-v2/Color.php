<?php
	
	/**
	 * Color
	 */
	class Color {
		
		/**
		 * @var integer Der Rotanteil der Farbe
		 */
		private $_r = 0;
		
		/**
		 * @var integer Der Grünanteil der Farbe
		 */
		private $_g = 0;
		
		/**
		 * @var integer Der Blauanteil der Farbe
		 */
		private $_b = 0;
		
		/**
		 * @var integer Der Alphakanal der Farbe
		 */
		private $_a = 0;
		
		/**
		 * Konstruktor
		 * Erstellt ein neues Color-Objekt. Folgende Aufrufe sind möglich:
		 * 
		 * @throws ImageException
		 * 
		 * 1) void
		 *   Bsp: () -> leere Farbe (0,0,0,0)
		 * 2) integer red, integer green, integer blue
		 *   Bsp: ( 255, 255, 255 ) -> Weiß
		 * 3) integer red, integer green, integer blue, integer alpha
		 *   Bsp: ( 0, 0, 0, 64 ) -> Halbtransparentes Schwarz
		 * 4) integer hex
		 *   Bsp: ( 0xFF0000 ) -> Rot
		 * 5) integer hex, integer alpha
		 *   Bsp: ( 0xFF0000, 64 ) -> Halbtransparentes Rot
		 * 6) integer longer_hex
		 *   Bsp: ( 0xFF000040 ) -> Halbtransparentes Rot
		 * 7) string hex
		 *   Bsp: ( 'FF0000' ) -> Rot
		 * 8) string hex, integer alpha
		 *   Bsp: ( 'FF0000', 64 ) -> Halbtransparentes Rot
		 * 9) string longer_hex
		 *   Bsp: ( '40FF0000' ) -> Halbtransparentes Rot
		 * 10) string hex, string alphahex
		 *   Bsp: ( 'FF0000', '40' ) -> Halbtransparentes Rot
		 * 
		 */
		public function __construct() {
			$args = func_get_args();
			$c = func_num_args();
			
			switch( $c ) {
				// 1)
				case 0:
					break;
				// 4), 6), 7), 9)
				case 1:
					$this->setColors( $args[0] );
					break;
				// 5), 8), 10)
				case 2:
					$this->setColors( $args[0] );
					$this->setAlpha( $args[1] );
					break;
				// 2)
				case 3:
					$this->setRed( $args[0] );
					$this->setGreen( $args[1] );
					$this->setBlue( $args[2] );
					break;
				// 3)
				case 4:
					$this->setRed( $args[0] );
					$this->setGreen( $args[1] );
					$this->setBlue( $args[2] );
					$this->setAlpha( $args[3] );
					break;
				default:
					throw new ImageException( 'Wrong contructorcall! See definition for informations.' );
			}
		}
		
		/**
		 * Führt eine Konversion nach String durch
		 * 
		 * @return string
		 */
		public function __toString() {
			return( '#'.get_class( $this ).'[R'.$this->getRed().' G'.$this->getGreen().' B'.$this->getBlue().' A'.$this->getAlpha().']' );
		}
		
		/**
		 * Gibt den Rotanteil der Farbe zurück
		 * 
		 * @return integer
		 */
		public function getRed() {
			return( $this->_r );
		}
		
		/**
		 * Gibt den Grünanteil der Farbe zurück
		 * 
		 * @return integer
		 */
		public function getGreen() {
			return( $this->_g );
		}
		
		/**
		 * Gibt den Blauanteil der Farbe zurück
		 * 
		 * @return integer
		 */
		public function getBlue() {
			return( $this->_b );
		}
		
		/**
		 * Gibt den Alphakanal der Farbe zurück
		 * 
		 * @return integer
		 */
		public function getAlpha() {
			return( $this->_a );
		}
		
		/**
		 * Setzt den Rotanteil der Farbe
		 * 
		 * @return void
		 * @param integer/string
		 */
		public function setRed( $value ) {
			$this->_r = $this->ensureColorInterval( $value );
		}
		
		/**
		 * Setzt den Grünanteil der Farbe
		 * 
		 * @return void
		 * @param integer/string
		 */
		public function setGreen( $value ) {
			$this->_g = $this->ensureColorInterval( $value );
		}
		
		/**
		 * Setzt den Blauanteil der Farbe
		 * 
		 * @return void
		 * @param integer/string
		 */
		public function setBlue( $value ) {
			$this->_b = $this->ensureColorInterval( $value );
		}
		
		/**
		 * Setzt den Alphaanteil der Farbe
		 * 
		 * @return void
		 * @param integer/string
		 */
		public function setAlpha( $value ) {
			$this->_a = $this->ensureAlphaInterval( $value );
		}
		
		/**
		 * Gibt alle Farbkanäle und den Alphakanal gleichzeitig als Integer zurück.
		 * Dieser Wert setzt sich Hexadezimal folgendermaßen zusammen:
		 *  AARRGGBB
		 * 
		 * @return string
		 */
		public function getColors() {
			$r = $this->getRed()>0xF ? dechex( $this->getRed() ) : '0'.dechex( $this->getRed() );
			$g = $this->getGreen()>0xF ? dechex( $this->getGreen() ) : '0'.dechex( $this->getGreen() );
			$b = $this->getBlue()>0xF ? dechex( $this->getBlue() ) : '0'.dechex( $this->getBlue() );
			$a = $this->getAlpha()>0xF ? dechex( $this->getAlpha() ) : '0'.dechex( $this->getAlpha() );
			return( strtoupper( $a.$r.$g.$b ) );
		}
		
		/**
		 * Setzt alle Farbkanäle und den Alphakanal gleichzeitig.
		 * Dieser Wert setzt sich Hexadezimal folgendermaßen zusammen:
		 *  0xAARRGGBB
		 * 
		 * @return void
		 * @param integer/string
		 */
		public function setColors( $value ) {
			if( is_string( $value ) ) {
				$value = hexdec( $value );
			}
			
			// Alphakanal soll mit gesetzt werden
			if( $value>0xFFFFFF ) {
				$this->setAlpha( $value>>24 );
				$this->setRed( $value>>16 );
				$this->setGreen( $value>>8 );
				$this->setBlue( $value );
			}
			else {
				$this->setRed( $value>>16 );
				$this->setGreen( $value>>8 );
				$this->setBlue( $value );
			}
		}
		
		/**
		 * Stellt sicher, dass der übergebene Integer im Werteinterval für Farben
		 * liegt.
		 * 
		 * @return integer
		 * @param string/integer
		 */
		private function ensureColorInterval( $value ) {
			if( is_string( $value ) ) {
				$value = hexdec( $value );
			}
			
			$value = intval( $value );
			if( $value>0xFF ) {
				$value = 0xFF;
			}
			else if( $value<0 ) {
				$value = 0;
			}
			return( $value );
		}
		
		/**
		 * Stellt sicher, dass der übergebene Integer im Werteinterval für den
		 * Alphakanal liegt.
		 * 
		 * @return integer
		 * @param string/integer
		 */
		private function ensureAlphaInterval( $value ) {
			if( is_string( $value ) ) {
				$value = hexdec( $value );
			}
			
			$value = intval( abs( $value ) );
			if( $value>0x7F ) {
				$value = 0x7F;
			}
			else if( $value<0 ) {
				$value = 0;
			}
			return( $value );
		}
		
		/**
		 * Kopiert die Farbkanäle und den Alphakanal von der übergebenen Farbe
		 * 
		 * @return Color Referenz auf dieses Objekt
		 * @param Color
		 */
		public function copyFrom( color $copyFrom ) {
			$this->setRed( $copyFrom->getRed() );
			$this->setGreen( $copyFrom->getGreen() );
			$this->setBlue( $copyFrom->getBlue() );
			return( $this );
		}
		
		/**
		 * Addiert eine Farbe auf diese.
		 * Die Farbkanäle werden addiert, während der Alphakanal subtrahiert wird
		 * (Da ein Alphakanal von 0 vollständige Sichtbarkeit bedeutet)
		 * 
		 * @return Color Referenz auf dieses Objekt
		 * @param Color
		 */
		public function add( Color $add ) {
			$this->setRed( $this->getRed() + $add->getRed() );
			$this->setGreen( $this->getGreen() + $add->getGreen() );
			$this->setBlue( $this->getBlue() + $add->getBlue() );
			$this->setAlpha( $this->getAlpha() - $add->getAlpha() );
			return( $this );
		}
		
		/**
		 * Subtrahiert eine Farbe von dieser.
		 * Die Farbkanäle werden subtrahiert, während der Alphakanal addiert wird
		 * (Da ein Alphakanal von 0 vollständige Sichtbarkeit bedeutet)
		 * 
		 * @return Color Referenz auf dieses Objekt
		 * @param Color
		 */
		public function sub( Color $sub ) {
			$this->setRed( $this->getRed() - $sub->getRed() );
			$this->setGreen( $this->getGreen() - $sub->getGreen() );
			$this->setBlue( $this->getBlue() - $sub->getBlue() );
			$this->setAlpha( $this->getAlpha() + $sub->getAlpha() );
			return( $this );
		}
		
		/**
		 * Überprüft, ob ein weiteres Color-Objekte die gleichen Werte wie dieses
		 * besitzt.
		 * 
		 * @return boolean
		 * @param Color 
		 */
		public function equals( Color $that ) {
			return(
				$this->getRed()==$that->getRed() and
				$this->getGreen()==$that->getGreen() and
				$this->getBlue()==$that->getBlue() and
				$this->getAlpha()==$that->getAlpha()
			);
		}
		
		
	}
	
?>