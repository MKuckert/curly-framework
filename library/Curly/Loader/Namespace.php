<?php

require_once "Curly/Loader/Abstract.php";

/**
 * Curly_Loader_Namespace
 * 
 * Eine Instanz dieser Klasse ermittelt aus dem Namen einer zu ladenden
 * Klasse den Pfad zur Quelldatei, relativ zu einem Basisverzeichnis.
 *
 * Diese Konvention wird beispielsweise vom Zend-Framework verwendet. Die
 * einzelnen Pfadteile werden durch einen Unterstrich "_" gekennzeichnet.
 * Die Klasse Zend_Config_Ini liegt beispielsweise in der Datei
 * Zend/Config/Ini.php.
 *
 * Mit der Eigenschaft {@link getSeparator Separator} können die möglichen
 * Zeichen, anhand derer eine Umwandlung von Klassenname in Dateipfad
 * vorgenommen wird abgefragt werden.
 *
 * Beispiel für ein Objekt dieser Klasse für das Laden von Klassen des
 * Zend-Frameworks:
 * <code>
 *  $loader = new Curly_Loader_Namespace();
 *  $loader->setBasePath( 'path/to/zend-lib/' );
 *  $class = 'Zend_Config_Ini';
 *  $loader->getPath( $class ); // Liefert: 'path/to/zend-lib/Zend/Config/Ini.php'
 * </code>
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Loader
 * @since 1.0 - 15.05.2008
 * @version 1.1
 */
class Curly_Loader_Namespace extends Curly_Loader_Abstract {

	/**
	 * @var array Basisverzeichnisse der zu ladenden Klassen.
	 */
	private $_basePath = array();

	/**
	 * @var array Separatorzeichen, anhand derer der Klassenname in
	 * den Dateipfad übersetzt werden kann.
	 */
	private $_separator = array( '_', '::' );

	/**
	 * Gibt die Basisverzeichnisse der zu ladenden Klassen zurück.
	 *
	 * @return array
	 */
	public function getBasePath() {
		return( $this->_basePath );
	}

	/**
	 * Setzt die Basisverzeichnisse der zu ladenden Klassen.
	 *
	 * @return void
	 * @param array
	 */
	public function setBasePath( $path ) {
		if( !is_array( $path ) ) {
			$path = func_get_args();
		}
		$path = array_map( 'strval', $path );
		$this->_basePath = $path;
	}

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
	 * Gibt den Dateipfad zu der übergebenen Klasse zurück, die die
	 * Definition für die Klasse enthält.
	 *
	 * @return string
	 * @param string
	 */
	public function getPath( $class ) {
		// Separator ersetzen
		$class = str_replace( $this->getSeparator(), DIRECTORY_SEPARATOR, $class );
		$file = DIRECTORY_SEPARATOR.$this->getFilename( $class );
		
		foreach( $this->getBasePath() as $basepath ) {
			$path = $basepath.$file;
			if( is_file( $path ) ) {
				return( $path );
			}
		}
		return( false );
	}
	
	/**
	 * Konstruktor
	 * 
	 * Es können direkt die Basispfade und Separatorzeichen gesetzt werden.
	 * 
	 * @param array
	 * @param array
	 */
	public function __construct( $basepath=null, $separator=null ) {
		if( !is_null( $basepath ) ) {
			$this->setBasePath( $basepath );
		}
		if( !is_null( $separator ) ) {
			$this->setSeparator( $separator );
		}
	}

}