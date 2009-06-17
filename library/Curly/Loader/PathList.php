<?php

require_once "Curly/Loader/IncludePath.php";

/**
 * Curly_Loader_PathList
 * 
 * Verwendet eine Liste von Pfaden, um Dateien mit den Klassen zu finden.
 *
 * Jedem Eintrag dieser Liste wird dabei das Verzeichnis
 * {@link getBasePath BasePath} vorangestellt, so dass die Pfadliste
 * ausschließlich relative Pfade enthalten kann.
 *
 * Beispiel für die Verwendung dieser Klasse:
 * <code>
 *  $loader = new PathClassLoader();
 *  $loader->addIncludePath( 'path/to/myPackage/' );
 *  $loader->addIncludePath( 'path/to/myOtherPackage/' );
 *  $loader->setBasePath( '/my/libs/' );
 *  // Im Code:
 *  include $loader->getPath( "MyClass" );
 * </code>
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Autoload
 * @since 1.0 - 15.05.2008
 * @version 1.0
 */
class Curly_Loader_PathList extends Curly_Loader_IncludePath {

	/**
	 * @var array Liste der Pfade, in denen Dateien mit Klassen liegen.
	 */
	private $_includePath = array();

	/**
	 * @var string Basisverzeichnis für jeden Pfad.
	 */
	private $_basePath = '';

	/**
	 * Gibt die Liste der Pfade, in denen Dateien mit Klassen liegen,
	 * zurück.
	 *
	 * @return array
	 */
	public function getIncludePath() {
		return( $this->_includePath );
	}

	/**
	 * Fügt der Liste von Pfaden dieses Objektes einen Pfad hinzu.
	 *
	 * @return void
	 * @param string
	 */
	public function addIncludePath( $path ) {
		$this->_includePath[] = $path;
	}

	/**
	 * Entfernt aus der Liste von Pfaden dieses Objektes einen Pfad.
	 *
	 * @return void
	 * @param string
	 */
	public function removeIncludePath( $path ) {
		$key = array_search( $path, $this->_includePath );
		if( $key!==false ) {
			unset( $this->_includePath[$key] );
		}
	}

	/**
	 * Gibt den Basisverzeichnis für jeden Pfad zurück.
	 *
	 * @return string
	 */
	public function getBasePath() {
		return( $this->_basePath );
	}

	/**
	 * Setzt das Basisverzeichnis für jeden Pfad.
	 * Vorsicht: Der Übergabeparameter wird in keinster Weise geprüft!
	 *
	 * @return void
	 * @param string
	 */
	public function setBasePath( $path ) {
		$this->_basePath = $path;
	}

	/**
	 * Gibt den zu verwendenden Pfad aus dem übergebenen Pfad und
	 * Klassennamen zurück.
	 *
	 * Diese Methode wird von getPath aufgerufen, um den physikalischen
	 * Pfad zu den übergebenen Parametern zu ermitteln.
	 *
	 * Diese Überladung der Elternmethode beachtet den BasePath-Parameter.
	 *
	 * @return string
	 * @param string Der zu verwendende Pfad
	 * @param string Der Klassenname
	 */
	protected function buildPath( $path, $class ) {
		return( $this->getBasePath().DIRECTORY_SEPARATOR.parent::buildPath( $path, $class ) );
	}

}