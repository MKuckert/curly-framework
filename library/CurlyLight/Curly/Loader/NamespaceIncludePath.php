<?php

require_once "Curly/Loader/IncludePath.php";

/**
 * Curly_Loader_NamespaceIncludePath
 * 
 * Kombiniert die Curly_Loader_IncludePath- und Curly_Loader_Namespace-Klassen.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Loader
 * @since 1.0 - 27.04.2008
 * @version 1.0
 */
class Curly_Loader_NamespaceIncludePath extends Curly_Loader_IncludePath {

	/**
	 * @var array Separatorzeichen, anhand derer der Klassenname in
	 * den Dateipfad übersetzt werden kann.
	 */
	private $_separator = array( '_', '::' );

	/**
	 * Gibt die Separatorzeichen, anhand derer der Klassenname in
	 * den Dateipfad übersetzt werden kann, zurück.
	 *
	 * @return array
	 */
	public function getSeparator() {
		return( $this->_separator );
	}

	/**
	 * Setzt die Separatorzeichen, anhand derer der Klassenname in
	 * den Dateipfad übersetzt werden kann.
	 *
	 * @return void
	 * @param array
	 */
	public function setSeparator( $separator ) {
		if( !is_array( $separator ) ) {
			$separator = func_get_args();
		}
		$separator = array_map( 'strval', $separator );
		$this->_separator = $separator;
	}

	/**
	 * Gibt den Dateinamen zu der Klasse zurück.
	 *
	 * @return string
	 * @param string
	 */
	public function getFilename( $class ) {
		// Separator ersetzen
		$class=str_replace($this->getSeparator(), DIRECTORY_SEPARATOR, $class);
		return parent::getFilename($class);
	}

}