<?php


/**
 * Curly_Javascript
 * 
 * Hilfsklasse für einfachen Zugriff auf die Javascript-Dateien.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly
 * @since 1.0 - 27.04.2008
 * @version 1.0
 */
class Curly_Javascript {
	
	const FILE_EXTENSION='.js';
	
	/**
	 * Gibt den vollständigen Pfad zum Verzeichnis des Curly.Javascript-Paketes
	 * zurück.
	 * 
	 * @return string
	 */
	static public function getPackagePath() {
		return(dirname(__FILE__).DIRECTORY_SEPARATOR.'Javascript');
	}
	
	/**
	 * Gibt den vollständigen Pfad zu einer Datei des Curly.Javascript-Paketes
	 * zurück.
	 * 
	 * @return string
	 * @param string Name der Skriptdatei (ohne Erweiterung).
	 */
	static public function getScriptPath($scriptname) {
		return(self::getPackagePath().DIRECTORY_SEPARATOR.$scriptname.self::FILE_EXTENSION);
	}
	
}
