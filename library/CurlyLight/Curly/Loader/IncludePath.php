<?php

require_once "Curly/Loader/Abstract.php";

/**
 * Curly_Loader_IncludePath
 * 
 * Anhand des include_path-Wertes werden die Pfade zu Klassendateien
 * ermittelt und diese automatisch nachgeladen.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Loader
 * @since 1.0 - 27.04.2008
 * @version 1.0
 */
class Curly_Loader_IncludePath extends Curly_Loader_Abstract {

	/**
	 * Gibt den Dateipfad zu der übergebenen Klasse zurück, die die
	 * Definition für die Klasse enthält.
	 *
	 * Kann dieser ClassLoader keine Datei zu der Klasse finden, wird false
	 * zurückgegeben.
	 *
	 * @return string
	 * @param string
	 */
	public function getPath( $class ) {
		foreach( $this->getIncludePath() as $path ) {
			$file = $this->buildPath( $path, $class );
			if( is_file( $file ) ) {
				return( $file );
			}
		}
		return( false );
	}

	/**
	 * Gibt alle Pfade in der include_path-Einstellung als Array zurück.
	 *
	 * @return array
	 */
	public function getIncludePath() {
		return( explode( PATH_SEPARATOR, get_include_path() ) );
	}

	/**
	 * Fügt dem include_path-Wert einen Pfad hinzu.
	 *
	 * @return void
	 * @param string
	 */
	public function addIncludePath( $path ) {
		set_include_path( get_include_path().PATH_SEPARATOR.$path );
	}

	/**
	 * Gibt den zu verwendenden Pfad aus dem übergebenen Pfad und
	 * Klassennamen zurück.
	 *
	 * Diese Methode wird von getPath aufgerufen, um den physikalischen
	 * Pfad zu den übergebenen Parametern zu ermitteln.
	 *
	 * @return string
	 * @param string Der zu verwendende Pfad
	 * @param string Der Klassenname
	 */
	protected function buildPath( $path, $class ) {
		return( $path.DIRECTORY_SEPARATOR.$this->getFilename( $class ) );
	}

}