<?php
	
	/**
	 * GenerationProperty
	 * Stellt die Eigenschaft einer Klasse dar.
	 * 
	 * @author Martin Kuckert
	 * @copyright Copyright &copy; 2008 Martin Kuckert
	 * @license New BSD License
	 * @package Generation
	 * @since 1.0 - 03.05.2008
	 * @version 1.0
	 */
	class GenerationProperty extends Generator {
 
		/**
		 * @var boolean Das Flag, ob diese Eigenschaft eine Klasseneigenschaft
		 * ist.
		 */
		private $_isStatic = false;
 		
		/**
		 * @var integer Die Sichtbarkeit dieser Eigenschaft. Ein Wert einer der
		 * Generation::visibility*-Konstanten.
		 */
		private $_visibility = Generation::visibilityPublic;
		
		/**
		 * @var mixed Der Standardwert für diese Eigenschaft. Es beschränkt
		 * sich hierbei auf einen konstanten Wert, d.h. auf einen primitiven
		 * Datentyp.
		 */
		private $_defaultValue = null;
 
		/**
		 * @var boolean Flag, ob diese Eigenschaft über einen Standardwert
		 * verfügt.
		 */
		private $_hasDefaultValue = false;
		
		/**
		 * @return boolean Das Flag, ob diese Eigenschaft eine
		 * Klasseneigenschaft ist.
		 */
		public function getIsStatic() {
			return( $this->_isStatic );
		}
		
		/**
		 * @param boolean Das Flag, ob diese Eigenschaft eine
		 * Klasseneigenschaft ist.
		 */
		public function setIsStatic( $value ) {
			$this->_isStatic = Ensure::boolean( $value );
		}
		
		/**
		 * @return integer Die Sichtbarkeit dieser Eigenschaft. Ein Wert einer der
		 * Generation::visibility*-Konstanten.
		 */
		public function getVisibility() {
			return( $this->_visibility );
		}
		
		/**
		 * @param integer Die Sichtbarkeit dieser Eigenschaft. Ein Wert einer der
		 * Generation::visibility*-Konstanten.
		 */
		public function setVisibility( $value ) {
			$this->_visibility = Ensure::integer( $value );
		}
 
		/**
		 * Gibt den Standardwert für diese Eigenschaft zurück.
		 * 
		 * @return mixed
		 */
		public function getDefaultValue() {
			return( $this->_defaultValue );
		}
 
		/**
		 * Setzt den Standardwert für diese Eigenschaft.
		 * 
		 * @return void
		 * @param mixed
		 */
		public function setDefaultValue( $value ) {
			$this->_defaultValue = $value;
		}
 
		/**
		 * Gibt das Flag, ob diese Eigenschaft über einen Standardwert verfügt,
		 * zurück.
		 * 
		 * @return boolean
		 */
		public function getHasDefaultValue() {
			return( $this->_hasDefaultValue );
		}
 
		/**
		 * Setzt das Flag, ob diese Eigenschaft über einen Standardwert 
		 * verfügt.
		 * 
		 * @return void
		 * @param boolean
		 */
		public function setHasDefaultValue( $value ) {
			$this->_hasDefaultValue = Ensure::boolean( $value );
		}
		
	}
	
?>