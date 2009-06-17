<?php
	
	/**
	 * Generation
	 * Diese Klasse bietet statische Hilfsfunktionen für die Klassen des
	 * Generation-Packages.
	 * 
	 * @author Martin Kuckert
	 * @copyright Copyright &copy; 2008 Martin Kuckert
	 * @license New BSD License
	 * @package Generation
	 * @since 1.0 - 03.05.2008
	 * @version 1.0
	 */
	class Generation implements Generator {
		
		/**
		 * @desc Kennzeichnet eine öffentliche Sichtbarkeit.
		 */
		const visibilityPublic = 1;
		
		/**
		 * @desc Kennzeichnet eine geschützte Sichtbarkeit.
		 */
		const visibilityProtected = 2;
		
		/**
		 * @desc Kennzeichnet eine private Sichtbarkeit.
		 */
		const visibilityPrivate = 3;
		
		/**
		 * @var integer Zählvariable für die {@link getIdentifier}-Methode.
		 */
		private $ident = 0;
		
		/**
		 * Überprüft, ob der übergebene Namen ein gültiger Funktions-,
		 * Methoden- oder Klassenbezeichner ist.
		 * 
		 * Dies ist der Fall, wenn der Name lediglich aus alphanummerischen
		 * Zeichen oder dem Unterstrich _ besteht. Das erste Zeichen muss dabei
		 * ein Buchstabe oder der Unterstrich sein.
		 * 
		 * Es wird nicht überprüft, ob ein reserviertes Wort verwendet wird.
		 * D.h. bei dem Namen "array" ist das Ergebnis true.
		 * 
		 * @return boolean
		 * @param string
		 */
		static public function isValidName( $name ) {
			return( preg_match( '/^[a-z_][a-z0-9_]*$/i', $name )>0 );
		}
		
		/**
		 * Gibt einen eindeutigen Namen zurück, der für Bezeichner verwendet
		 * werden kann.
		 * 
		 * Diese Methode wird im Generation-Package für den Standardwert von
		 * Bezeichnern verwendet.
		 * 
		 * @return string
		 */
		static public function getIdentifier() {
			return( 'Identifier'.self::$ident++ );
		}
		
	}
	
?>