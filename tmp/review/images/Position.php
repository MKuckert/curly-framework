<?php
	
	/**
	 * Position
	 * Stellt eine X/Y-Position in einem Bild dar.
	 * 
	 * @author Martin Kuckert
	 * @package GDImage
	 * @since 19.11.2007
	 * @copyright Copyright &copy; 2007 Martin Kuckert
	 * @license New BSD License
	 * @version 1.0
	 */
	class Position extends ValuePair {
		
		/**
		 * @var string Typ, dem die Werte entsprechen müssen
		 */
		protected $type = 'integer';
		
		/**
		 * Führt eine Konversion nach String durch
		 * 
		 * @return string
		 */
		public function __toString() {
			return( '#'.get_class( $this ).'[X'.$this->getX().' Y'.$this->getY().']' );
		}
		
		/**
		 * Gibt die X-Koordinate zurück
		 * 
		 * @return integer
		 */
		public function getX() {
			return( $this->getFirst() );
		}
		
		/**
		 * Gibt die Y-Koordinate zurück
		 * 
		 * @return integer
		 */
		public function getY() {
			return( $this->getSecond() );
		}
		
		/**
		 * Setzt die X-Koordinate
		 * 
		 * @return void
		 * @param integer
		 */
		public function setX( $value ) {
			$this->setFirst( $value );
		}
		
		/**
		 * Setzt die Y-Koordinate
		 * 
		 * @return void
		 * @param integer
		 */
		public function setY( $value ) {
			$this->setSecond( $value );
		}
		
	}
	
?>