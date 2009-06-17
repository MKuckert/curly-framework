<?php
	
	/**
	 * GenerationFunction
	 * Mit Hilfe dieser Klasse kann die Signatur einer Funktion definiert
	 * werden.
	 * 
	 * @author Martin Kuckert
	 * @copyright Copyright &copy; 2008 Martin Kuckert
	 * @license New BSD License
	 * @package Generation
	 * @since 1.0 - 03.05.2008
	 * @version 1.0
	 */
	class GenerationFunction extends Generator {
 
		/**
		 * @var boolean Flag, ob diese Funktion den Rückgabewert als Referenz
		 * zurückgeben soll.
		 */
		private $_returnsReference = false;
		
		/**
		 * @var array Das Array von GenerationParameter-Objekten, die die
		 * Parameter dieser Funktion beschreiben.
		 */
		private $_parameters = array();
		
		/**
		 * Gibt das Flag, ob diese Funktion den Rückgabewert als Referenz
		 * zurückgeben soll, zurück.
		 * 
		 * @return string
		 */
		public function getReturnsReference() {
			return( $this->_returnsReference );
		}
		
		/**
		 * Setzt das Flag, ob diese Funktion den Rückgabewert als Referenz
		 * zurückgeben soll.
		 * 
		 * @return void
		 * @param string
		 */
		public function setReturnsReference( $value ) {
			$this->_returnsReference = Ensure::boolean( $value );
		}
		
		/**
		 * Gibt das Array von GenerationParameter-Objekten, die die Parameter
		 * dieser Funktion beschreiben, zurück.
		 * 
		 * @return array&
		 */
		public function &getParameters() {
			return $this->_parameters;
		}
		
		/**
		 * Magische Funktion für die Nachbearbeitung dieser Instanz nach dem
		 * Klonen, die alle {@link getParameters Parameter} klont.
		 * 
		 * @return void
		 */
		public function __clone() {
			foreach( $this->_parameters as $key=>$value ) {
				$this->_parameters[$key] = clone $value;
			}
		}
		
	}
	
?>