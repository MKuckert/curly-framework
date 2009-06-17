<?php

require_once "Curly/Loader/Interface.php";

/**
 * Curly_Loader_Abstract
 * 
 * Basisklasse für Klassen, die weitere Klassen nachladen können.
 *
 * Über die {@link load}-Methode kann die Definitionsdatei zu einer Klasse
 * direkt eingebunden werden.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Autoload
 * @since 1.0 - 27.04.2008
 * @version 1.0
 */
abstract class Curly_Loader_Abstract implements Curly_Loader_Interface {

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
		return( false );
	}

	/**
	 * Diese Klasse kann in Unterklassen verwendet werden, das Format der
	 * PHP-Dateien zu beeinflussen. Standardmäßig wird der Klassenname mit
	 * der Dateierweiterung ".php" als Dateiname zurückgegeben.
	 *
	 * @return string
	 * @param string
	 */
	public function getFilename( $class ) {
		return( $class.'.php' );
	}

	/**
	 * Versucht, die Definitionsdatei zu der Klasse mit dem übergebenen
	 * Namen einzubinden.
	 *
	 * @throws ClassNotFoundException Diese Instanz konnte den Pfad zu der
	 *  Definitionsdatei der Klasse nicht ermitteln.
	 * @return void
	 * @param string Name der Klasse
	 */
	public function load( $class ) {
		$path = $this->getPath( $class );
		if( $path===false ) {
			require_once "Curly/Exception.php";
			throw new Curly_Exception( 'Unable to determine the path to the definition file of class '.$class );
		}
		require_once $path;
	}

}