<?php
	
	/**
	 * GenerationClass
	 * 
	 * @author Martin Kuckert
	 * @copyright Copyright &copy; 2008 Martin Kuckert
	 * @license New BSD License
	 * @package Generation
	 * @since 1.0 - 03.05.2008
	 * @version 1.0
	 */
	class GenerationClass extends Generator {
		
		/**
		 * @var boolean Flag, ob diese Klasse versiegelt wird, d.h. nicht
		 * weiter abgeleitet werden darf.
		 */
		private $_isFinal = false;
 
		/**
		 * @var boolean Flag, ob diese Klasse als Abstrakt deklariert ist.
		 */
		private $_isAbstract = false;
 
		/**
		 * @var boolean Flag, ob dies ein Interface ist.
		 */
		private $_isInterface = false;
 
		/**
		 * @var array Assoziatives Array aller Klassenkonstanten.
		 */
		private $_constants = null;
 
		/**
		 * @var string Die Elternklasse dieser Klasse.
		 */
		private $_parentClass = '';
 
		/**
		 * @var array Liste aller implementierten Interfaces.
		 */
		private $_interfaces = null;
 
		/**
		 * Gibt das Flag, ob diese Klasse versiegelt wird, d.h. nicht weiter
		 * abgeleitet werden darf, zurück.
		 * 
		 * @return boolean
		 */
		public function getIsFinal() {
			return( $this->_isFinal );
		}
 
		/**
		 * Setzt das Flag, ob diese Klasse versiegelt wird, d.h. nicht weiter
		 * abgeleitet werden darf.
		 * 
		 * @return void
		 * @param boolean
		 */
		public function setIsFinal( $value ) {
			$this->_isFinal = Ensure::boolean( $value );
		}
 
		/**
		 * Gibt das Flag, ob diese Klasse als Abstrakt deklariert ist, zurück.
		 * 
		 * @return boolean
		 */
		public function getIsAbstract() {
			return( $this->_isAbstract );
		}
 
		/**
		 * Setzt das Flag, ob diese Klasse als Abstrakt deklariert ist.
		 * 
		 * @return void
		 * @param boolean
		 */
		public function setIsAbstract( $value ) {
			$this->_isAbstract = Ensure::boolean( $value );
		}
 
		/**
		 * @return boolean Flag, ob dies ein Interface ist.
		 */
		public function getIsInterface() {
			return( $this->_isInterface );
		}
 
		/**
		 * @param boolean Flag, ob dies ein Interface ist.
		 */
		public function setIsInterface( $value ) {
			$this->_isInterface = TPropertyValue::ensureBoolean( $value );
		}
 
		/**
		 * @return mixed Assoziatives Array aller Klassenkonstanten.
		 */
		public function getConstants() {
			return( $this->_constants );
		}
 
		/**
		 * @return string Die Elternklasse dieser Klasse.
		 */
		public function getParentClass() {
			return( $this->_parentClass );
		}
 
		/**
		 * @param string Die Elternklasse dieser Klasse.
		 */
		public function setParentClass( $value ) {
			$this->_parentClass = TPropertyValue::ensureString( $value );
		}
 
		/**
		 * @return mixed Liste aller implementierten Interfaces.
		 */
		public function getInterfaces() {
			return( $this->_interfaces );
		}
		
	}
	
?>