<?php

require_once "Curly/Loader/Abstract.php";

/**
 * Curly_Loader_Recursive
 * 
 * Dieser ClassLoader durchsucht bei der Anfrage nach einem Klassenpfad das
 * in {@link getBasePath BasePath} angegebene Verzeichnis rekursiv nach der
 * Definitionsdatei zu der gesuchten Klasse.
 *
 * Um das erneute Parsen des Verzeichnisses bei mehreren Anfragen zu
 * vermeiden, cacht diese Klasse den Verzeichnisinhalt intern.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Loader
 * @since 1.0 - 27.05.2008
 * @version 1.0
 * @TODO Cache-Instanz anstatt eines Arrays verwenden und als Objekteigenschaft zur Verfügung stellen.
 */
class Curly_Loader_Recursive extends Curly_Loader_Abstract {

	/**
	 * @var string Basisverzeichnis für jeden Pfad.
	 */
	private $_basePath = '';

	/**
	 * @var array Liste aller bekannten Dateien und der Pfad zu dem
	 * Verzeichnis, in dem diese liegen.
	 */
	private $_knownFiles = null;

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
	 * Der interne Cache bereits bekannter Dateipositionen wird geleert.
	 *
	 * @return void
	 * @param string
	 */
	public function setBasePath( $path ) {
		$this->_basePath = $path;
		$this->_knownFiles = null;
	}

	/**
	 * Diese Methode sucht im Basisverzeichnis nach der Definitionsdatei zu
	 * der gesuchten Klasse. Wird eine Definitionsdatei gefunden, wird der
	 * Pfad zu dieser Datei zurückgegeben. Ansonsten wird false
	 * zurückgegeben.
	 *
	 * Kann dieser ClassLoader keine Datei zu der Klasse finden, wird false
	 * zurückgegeben.
	 *
	 * @return string
	 * @param string
	 */
	public function getPath( $class ) {
		$filename = $this->getFilename( $class );
			
		// Pfade rekursiv durchsuchen und Cache erstellen
		if( is_null( $this->_knownFiles ) ) {
			$this->findFiles();
		}
			
		if( isset( $this->_knownFiles[$filename] ) ) {
			return( $this->_knownFiles[$filename].DIRECTORY_SEPARATOR.$filename );
		}
		else {
			return( false );
		}
	}

	/**
	 * Diese Datei durchsucht das {@link getBasePath BasePath}-Verzeichnis
	 * rekursiv und speichert alle gefundenen Dateien im internen Cache.
	 *
	 * Ein eventuell vorhandener interner Cache wird geleert.
	 *
	 * @return void
	 */
	protected function findFiles() {
		$queue = array( $this->getBasePath() );
		$this->_knownFiles = array();
			
		// Alle Queue-Einträge verarbeiten
		while( count( $queue )>0 ) {
			$path = array_shift( $queue );
			$handle = opendir( $path );
			if( $handle===false ) {
				continue;
			}

			while( ( $entry = readdir( $handle ) )!==false ) {
				if( $entry=='.' or $entry=='..' ) {
					continue;
				}
					
				$full = $path.DIRECTORY_SEPARATOR.$entry;
					
				if( is_dir( $full ) ) {
					$queue[] = $full;
				}
				else if( is_file( $full ) ) {
					$this->_knownFiles[$entry] = $path;
				}
			}

			closedir( $handle );
		}
	}

}