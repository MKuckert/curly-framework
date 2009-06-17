<?php

require_once "Curly/Loader/Abstract.php";

/**
 * Curly_Loader_List
 * 
 * Dieser ClassLoader verwendet eine Assoziationsliste, um die
 * Definitionsdatei von Klassen zu finden. Diese Liste wird manuell über
 * die {@link addEntry}- und {@link removeEntry}-Methoden befüllt.
 *
 * Diese Klasse ist dann sinnvoll, wenn die Klassendateien über kein
 * gemeinsames Namensmuster verfügen. Sonst sollte immer eine andere
 * ClassLoader-Klasse verwendet werden.
 *
 * Beispiel für die Anwendung dieser Klasse:
 * <code>
 *  $loader = new AssignClassLoader();
 *  $loader->addEntry( 'MyClass', 'path/to/myClass.class.php' );
 *  $loader->addEntry( 'my_Other_Class', '/home/me/src/other.inc' );
 *  $loader->load( 'MyClass' );
 * </code>
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Loader
 * @since 1.0 - 29.05.2008
 * @version 1.0
 */
class Curly_Loader_List extends Curly_Loader_Abstract {

	/**
	 * @var array Liste aller bekannten Dateien und der Pfad zu dem
	 * Verzeichnis, in dem diese liegen.
	 */
	private $_knownFiles = array();
	
	/**
	 * @var string Basisverzeichnis für jeden Pfad.
	 */
	private $_basePath = '';
	
	/**
	 * Gibt die Liste aller bekannten Dateien und der Pfad zu dem Verzeichnis,
	 * in dem diese liegen, zurück.
	 * 
	 * @return array
	 */
	public function getKnownFiles() {
		return $this->_knownFiles;
	}
	
	/**
	 * Setzt die Liste aller bekannten Dateien und der Pfad zu dem Verzeichnis,
	 * in dem diese liegen.
	 * 
	 * @return void
	 * @param array
	 */
	public function setKnownFiles(array $value) {
		$this->_knownFiles=$value;
	}
	
	/**
	 * Fügt der Liste aller bekannten Dateien und der Pfad zu dem Verzeichnis,
	 * in dem diese liegen weitere Einträge hinzu.
	 * 
	 * @return void
	 * @param array
	 */
	public function addKnownFiles(array $value) {
		$this->_knownFiles=array_merge($this->_knownFiles, $value);
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
		if( isset( $this->_knownFiles[$class] ) ) {
			return( $this->getBasePath().DIRECTORY_SEPARATOR.$this->_knownFiles[$class] );
		}
		else {
			return( false );
		}
	}

	/**
	 * Diese Methode fügt der Dateiliste einen Eintrag hinzu.
	 *
	 * @return void
	 * @param string Der Klassenname
	 * @param string Der Pfad zu der Datei, in der sich die Definition zu
	 *  der Klasse befindet.
	 */
	public function addEntry( $class, $path ) {
		$this->_knownFiles[$class] = $path;
	}

	/**
	 * Diese Methode entfernt einen Eintrag aus der Dateiliste.
	 *
	 * @return void
	 * @param string der Klassenname
	 */
	public function removeEntry( $class ) {
		unset( $this->_knownFiles[$class] );
	}

}