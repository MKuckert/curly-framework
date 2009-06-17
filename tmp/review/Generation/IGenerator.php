<?php
	
	/**
	 * IGenerator
	 * Dieses Interface muss von allen Klassen des Generation-Packages
	 * implementiert werden.
	 * 
	 * @author Martin Kuckert
	 * @copyright Copyright &copy; 2008 Martin Kuckert
	 * @license New BSD License
	 * @package Generation
	 * @since 1.0 - 03.05.2008
	 * @version 1.0
	 */
	interface IGenerator {
		
		/**
		 * Gibt den Namen des generierten Objektes zurück.
		 * 
		 * @return string
		 */
		public function getName();
 
		/**
		 * Setzt den Namen des generierten Objektes.
		 * 
		 * @return void
		 * @param string
		 */
		public function setName( $value );
		
		/**
		 * Gibt den Dokumentationskommentar zu dem generierten Objekt zurück.
		 * 
		 * @return string
		 */
		public function getDocComment();
		
		/**
		 * Setzt den Dokumentationskommentar zu dem generierten Objekt.
		 * 
		 * @return void
		 * @param string
		 */
		public function setDocComment( $value );
		
	}
	
?>