<?php
	
	/**
	 * ValuePair
	 * Stellt ein Wertepaar dar
	 * 
	 * @author Martin Kuckert
	 * @package GDImage
	 * @since 21.11.2007
	 * @copyright Copyright &copy; 2007 Martin Kuckert
	 * @license New BSD License
	 * @version 1.0
	 */
	abstract class ValuePair extends GetSetAccess {
		
		/**
		 * @var string Typ, dem die Werte entsprechen müssen
		 */
		protected $type = 'integer';
		
		/**
		 * @var mixed Wert 1
		 */
		private $first;
		
		/**
		 * @var mixed Wert 2
		 */
		private $second;
		
		/**
		 * Konstruktor
		 * Erstellt ein neues ValuePair-Objekt.
		 * 
		 * @param mixed Wert 1
		 * @param mixed Wert 2
		 */
		public function __construct( $first=null, $second=null ) {
			$this->setFirst( $first );
			$this->setSecond( $second );
		}
		
		/**
		 * Gibt den ersten Wert zurück
		 * 
		 * @return mixed
		 */
		public function getFirst() {
			return( $this->first );
		}
		
		/**
		 * Gibt den zweiten Wert zurück
		 * 
		 * @return mixed
		 */
		public function getSecond() {
			return( $this->second );
		}
		
		/**
		 * Setzt den ersten Wert
		 * 
		 * @return void
		 * @param mixed
		 */
		public function setFirst( $value ) {
			$this->first = $this->setCorrectType( $value );
		}
		
		/**
		 * Setzt den zweiten Wert
		 * 
		 * @return void
		 * @param mixed
		 */
		public function setSecond( $value ) {
			$this->second = $this->setCorrectType( $value );
		}
		
		/**
		 * Konvertiert intern einen übergebenen Wert in den korrekten Typ.
		 * $type muss kompatibel zu der Funktion {@link http://php.net/settype settype} sein
		 * 
		 * @return mixed
		 * @param mixed
		 * @see http://php.net/settype
		 */
		protected function setCorrectType( $value ) {
			settype( $value, $this->type );
			return( $value );
		}
		
	}
	
?>