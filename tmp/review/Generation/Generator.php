<?php
	
	/**
	 * Generator
	 * Basisimplementierung des IGenerator-Interfaces.
	 * 
	 * @author Martin Kuckert
	 * @copyright Copyright &copy; 2008 Martin Kuckert
	 * @license New BSD License
	 * @package Generation
	 * @since 1.0 - 03.05.2008
	 * @version 1.0
	 */
	class Generator extends Object implements IGenerator {
		
		/**
		 * @var string Der Name der Funktion.
		 */
		private $_name = '';
 
		/**
		 * @var string Der Dokumentationskommentar zu dieser Funktion.
		 */
		private $_docComment = '';
 
		/**
		 * Gibt den Namen der Funktion zurück.
		 * 
		 * @return string
		 */
		public function getName() {
			if( $this->_name=='' ) {
				$this->_name = Generation::getIdentifier();
			}
			return( $this->_name );
		}
 
		/**
		 * Setzt den Namen der Funktion.
		 * 
		 * @return void
		 * @param string
		 */
		public function setName( $value ) {
			if( !Generation::isValidName( $value ) ) {
				throw new GenerationException( 'The given value is no valid identifier' );
			}
			$this->_name = $value;
		}
		
		/**
		 * Gibt den Dokumentationskommentar zu dieser Funktion zurück.
		 * 
		 * @return string
		 */
		public function getDocComment() {
			return( $this->_docComment );
		}
		
		/**
		 * Setzt den Dokumentationskommentar zu dieser Funktion.
		 * 
		 * @return void
		 * @param string
		 */
		public function setDocComment( $value ) {
			$this->_docComment = Ensure::string( $value );
		}
		
	}
	
?>