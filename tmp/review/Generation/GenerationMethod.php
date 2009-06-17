<?php
	
	/**
	 * GenerationMethod
	 * Mit Hilfe dieser Klasse kann die Signatur einer Methode einer Klasse
	 * definiert werden.
	 * 
	 * @author Martin Kuckert
	 * @copyright Copyright &copy; 2008 Martin Kuckert
	 * @license New BSD License
	 * @package Generation
	 * @since 1.0 - 03.05.2008
	 * @version 1.0
	 */
	class GenerationMethod extends GenerationFunction {
		
		/**
		 * @var boolean Das Flag, ob diese Methode geschützt ist, d.h. in abgeleiteten Klassen nicht mehr überschrieben werden darf.
		 */
		private $_isFinal = false;
 
		/**
		 * @var boolean Das Flag, ob diese Methode als Abstrakt deklariert werden soll.
		 */
		private $_isAbstract = false;
 
		/**
		 * @var boolean Das Flag, ob diese Methode eine Klassenmethode ist.
		 */
		private $_isStatic = false;
		/**
		 * @var integer Die Sichtbarkeit dieser Methode. Ein Wert einer der
		 * Generation::visibility*-Konstanten.
		 */
		private $_visibility = Generation::visibilityPublic;
 
		/**
		 * @return boolean Das Flag, ob diese Methode geschützt ist, d.h. in abgeleiteten Klassen
		 * nicht mehr überschrieben werden darf.
		 */
		public function getIsFinal() {
			return( $this->_isFinal );
		}
		
		/**
		 * @param boolean Das Flag, ob diese Methode geschützt ist, d.h. in abgeleiteten Klassen
		 * nicht mehr überschrieben werden darf.
		 */
		public function setIsFinal( $value ) {
			$this->_isFinal = Ensure::boolean( $value );
		}
		
		/**
		 * @return boolean Das Flag, ob diese Methode als Abstrakt deklariert werden soll.
		 */
		public function getIsAbstract() {
			return( $this->_isAbstract );
		}
		
		/**
		 * @param boolean Das Flag, ob diese Methode als Abstrakt deklariert werden soll.
		 */
		public function setIsAbstract( $value ) {
			$this->_isAbstract = Ensure::boolean( $value );
		}
		
		/**
		 * @return boolean Das Flag, ob diese Methode eine Klassenmethode ist.
		 */
		public function getIsStatic() {
			return( $this->_isStatic );
		}
		
		/**
		 * @param boolean Das Flag, ob diese Methode eine Klassenmethode ist.
		 */
		public function setIsStatic( $value ) {
			$this->_isStatic = Ensure::boolean( $value );
		}
		
		/**
		 * @return integer Die Sichtbarkeit dieser Methode. Ein Wert einer der
		 * Generation::visibility*-Konstanten.
		 */
		public function getVisibility() {
			return( $this->_visibility );
		}
		
		/**
		 * @param integer Die Sichtbarkeit dieser Methode. Ein Wert einer der
		 * Generation::visibility*-Konstanten.
		 */
		public function setVisibility( $value ) {
			$this->_visibility = Ensure::integer( $value );
		}
		
	}
	
?>