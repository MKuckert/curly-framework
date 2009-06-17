<?php
	
	/**
	 * GenerationParameter
	 * Stellt einen Übergabeparameter in einer {@link GenerationFunction} oder
	 * {@link GenerationMethod} dar.
	 * 
	 * @author Martin Kuckert
	 * @copyright Copyright &copy; 2008 Martin Kuckert
	 * @license New BSD License
	 * @package Generation
	 * @since 1.0 - 03.05.2008
	 * @version 1.0
	 */
	class GenerationParameter extends Generator {
		
		/**
		 * @var boolean Flag, ob dieser Parameter per Referenz übergeben wird.
		 */
		private $_isPassedByReference = false;
 
		/**
		 * @var boolean Der Typ dieses Parameters.
		 */
		private $_type = false;
		
		/**
		 * @var string Der Standardwert für diesen Parameter. Es beschränkt
		 * sich hierbei auf einen konstanten Wert, d.h. auf einen primitiven
		 * Datentyp.
		 */
		private $_defaultValue = null;
 
		/**
		 * @var boolean Flag, ob dieser Parameter über einen Standardwert
		 * verfügt.
		 */
		private $_hasDefaultValue = false;
 
		/**
		 * @return boolean Flag, ob dieser Parameter per Referenz übergeben
		 * wird.
		 */
		public function getIsPassedByReference() {
			return( $this->_isPassedByReference );
		}
 
		/**
		 * @param boolean Flag, ob dieser Parameter per Referenz übergeben
		 * wird.
		 */
		public function setIsPassedByReference( $value ) {
			$this->_isPassedByReference = Ensure::boolean( $value );
		}
 
		/**
		 * @return string Der Typ dieses Parameters.
		 */
		public function getType() {
			return( $this->_type );
		}
 
		/**
		 * @param string Der Typ dieses Parameters.
		 */
		public function setType( $value ) {
			$this->_type = Ensure::string( $value );
		}
 
		/**
		 * Gibt den Standardwert für diesen Parameter zurück.
		 * 
		 * @return mixed
		 */
		public function getDefaultValue() {
			return( $this->_defaultValue );
		}
 
		/**
		 * Setzt den Standardwert für diesen Parameter.
		 * 
		 * @return void
		 * @param mixed
		 */
		public function setDefaultValue( $value ) {
			$this->_defaultValue = $value;
		}
 
		/**
		 * Gibt das Flag, ob dieser Parameter über einen Standardwert verfügt,
		 * zurück.
		 * 
		 * @return boolean
		 */
		public function getHasDefaultValue() {
			return( $this->_hasDefaultValue );
		}
 
		/**
		 * Setzt das Flag, ob dieser Parameter über einen Standardwert verfügt.
		 * 
		 * @return void
		 * @param boolean
		 */
		public function setHasDefaultValue( $value ) {
			$this->_hasDefaultValue = Ensure::boolean( $value );
		}
		
	}
	
?>