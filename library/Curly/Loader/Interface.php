<?php

/**
 * Curly_Loader_Interface
 * 
 * Interface, welches alle Klassen zum Laden weiterer Klassen
 * implementieren müssen.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Loader
 * @since 1.0 - 27.04.2008
 * @version 1.0
 */
interface Curly_Loader_Interface {

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
	public function getPath( $class );

}