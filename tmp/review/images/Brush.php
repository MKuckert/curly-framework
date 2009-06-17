<?php
	
	/**
	 * Brush
	 * Stellt einen Pinsel für Zeichenoperationen dar. Dieser kapselt
	 * Einstellungen zu der zu verwendenden Farbe, Linienstärke, etc.
	 * 
	 * @author Martin Kuckert
	 * @package GDImage
	 * @since 21.11.2007
	 * @copyright 2007 Martin Kuckert
	 * @license Copyright 2007 by Martin Kuckert
	 * @see http://php.net/image
	 * @version 1.0
	 */
	class Brush extends GetSetAccess {
		
		/**
		 * @var GDImage Speichert das Bild des Pinsels
		 * @see http://php.net/imagesetbrush
		 * @see http://php.net/imagesettile
		 */
		private $image = null;
		
		/**
		 * @var Color Speichert die zu verwendende Farbe des Pinsels für
		 * Zeichenoperationen.
		 */
		private $color = null;
		
		/**
		 * @var Color[] Speichert den Style des Pinsels
		 * @see http://php.net/imagesetstyle
		 */
		private $style = null;
		
		/**
		 * @var integer Speichert die zu verwendende Strichbreite des Pinsels
		 * @see http://php.net/imagesetthickness
		 */
		private $thickness = null;
		
		/**
		 * Konstruktor
		 * Bietet die Möglichkeit, die Member direkt in der Initialisierung zu setzen
		 * 
		 * @param GDImage
		 * @param Color
		 * @param Color[]
		 * @param integer
		 */
		public function __construct( GDImage $image=null, Color $color=null, array $style=null, $thickness=null ) {
			if( !is_null( $image ) ) {
				$this->setImage( $image );
			}
			if( !is_null( $color ) ) {
				$this->setColor( $color );
			}
			if( !is_null( $style ) ) {
				$this->setStyle( $style );
			}
			if( !is_null( $thickness ) ) {
				$this->setThickness( $thickness );
			}
		}
		
		/**
		 * Führt eine Konversion nach String durch
		 * 
		 * @return string
		 */
		public function __toString() {
			$res = array();
			if( $this->hasImage() ) {
				$res[] = 'Image '.$this->getImage()->__toString();
			}
			if( $this->hasColor() ) {
				$res[] = 'Color '.$this->getColor()->__toString();
			}
			if( $this->hasStyle() ) {
				$r = array();
				foreach( $this->getStyle() as $color ) {
					$r[] = $color->__toString();
				}
				$res[] = 'Style '.implode( ",\n   ", $r );
			}
			if( $this->hasThickness() ) {
				$res[] = 'Thickness '.$this->getThickness();
			}
			
			
			return( '#'.get_class( $this ).'['.implode( ' ', $res ).']' );
		}
		
		/**
		 * Gibt das Bild des Pinsels zurück
		 * 
		 * @return GDImage
		 */
		public function getImage() {
			return( $this->image );
		}
		
		/**
		 * Legt das Bild des Pinsels fest
		 * 
		 * @return void
		 * @param GDImage
		 */
		public function setImage( GDImage $image ) {
			$this->image = $image;
		}
		
		/**
		 * Gibt zurück, ob der Pinsel ein Bild besitzt
		 * 
		 * @return boolean
		 */
		public function hasImage() {
			return( !is_null( $this->image ) );
		}
		
		/**
		 * Entfernt das Bild aus dem Pinsel
		 * 
		 * @return void
		 */
		public function removeImage() {
			$this->image = null;
		}
		
		/**
		 * Gibt die Farbe des Pinsels zurück
		 * 
		 * @return Color
		 */
		public function getColor() {
			return( $this->color );
		}
		
		/**
		 * Legt die Farbe des Pinsels fest
		 * 
		 * @return void
		 * @param Color
		 */
		public function setColor( Color $color ) {
			$this->color = $color;
		}
		
		/**
		 * Gibt zurück, ob der Pinsel eine Farbe besitzt
		 * 
		 * @return boolean
		 */
		public function hasColor() {
			return( !is_null( $this->color ) );
		}
		
		/**
		 * Entfernt die Farbe aus dem Pinsel
		 * 
		 * @return void
		 */
		public function removeColor() {
			$this->color = null;
		}
		
		/**
		 * Gibt den Style des Pinsels zurück
		 * 
		 * @return Color[]
		 */
		public function getStyle() {
			return( $this->style );
		}
		
		/**
		 * Legt den Style des Pinsels fest
		 * 
		 * @throws InvalidArgumentException
		 * @return void
		 * @param Color[]
		 */
		public function setStyle( array $style ) {
			foreach( $style as $s ) {
				if( !( $s instanceof Color ) ) {
					throw new InvalidArgumentException( 'All elements have do be an instance of Color' );
				}
			}
			$this->style = array_values( $style );
		}
		
		/**
		 * Gibt zurück, ob der Pinsel einen Style besitzt
		 * 
		 * @return boolean
		 */
		public function hasStyle() {
			return( !is_null( $this->style ) );
		}
		
		/**
		 * Entfernt den Style aus dem Pinsel
		 * 
		 * @return void
		 */
		public function removeStyle() {
			$this->style = null;
		}
		
		/**
		 * Gibt die Strichbreite des Pinsels zurück
		 * 
		 * @return integer
		 */
		public function getThickness() {
			return( $this->thickness );
		}
		
		/**
		 * Legt die Strichbreite des Pinsels fest
		 * 
		 * @throws InvalidArgumentException
		 * @return void
		 * @param integer
		 */
		public function setThickness( $thickness ) {
			if( !is_int( $thickness ) ) {
				throw new InvalidArgumentException( 'Argument has to be of type integer!' );
			}
			else if( $thickness<=0 ) {
				throw new InvalidArgumentException( 'Argument has to be greater than zero' );
			}
			
			$this->thickness = $thickness;
		}
		
		/**
		 * Gibt zurück, ob der Pinsel eine Strichbreite gesetzt hat
		 * 
		 * @return boolean
		 */
		public function hasThickness() {
			return( !is_null( $this->thickness ) );
		}
		
		/**
		 * Entfernt die Strichbreite aus dem Pinsel
		 * 
		 * @return void
		 */
		public function removeThickness() {
			$this->thickness = null;
		}
		
	}
	
?>