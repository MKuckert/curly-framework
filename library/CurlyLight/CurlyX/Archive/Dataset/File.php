<?php

require_once "CurlyX/Archive/Dataset/Interface.php";

/**
 * CurlyX_Archive_Dataset_File
 *
 * Stellt Daten aus einer Datei zur Verfügung.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Dataset
 * @since 1.0 - 13.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Dataset_File implements CurlyX_Archive_Dataset_Interface {
	
	/**
	 * @var string Pfad zu der Datei
	 */
	private $path;
	
	/**
	 * 
	 * @var boolean Flag, ob die Dateierweiterung bei der Namensermittlung
	 * entfernt werden soll,
	 */
	private $removeExtension;
	
	/**
	 * @var string Der zu verwendende Name. Wird diese Eigenschaft nicht
	 * gesetzt, so wird der Originaldateiname verwendet.
	 */
	private $_overwriteName=NULL;
	
	/**
	 * Gibt den Pfad zu der Datei zurück.
	 * 
	 * @return string
	 */
	public function getPath() {
		return($this->path);
	}
	
	/**
	 * Gibt den zu verwendenden Namen zurück. Wird der Wert NULL zurückgegeben,
	 * so wird der Originaldateiname verwendet. 
	 * 
	 * @return string
	 */
	public function getOverwriteName() {
		return($this->_overwriteName);
	}
	
	/**
	 * Setzt den zu verwendenden Namen. Wird der Wert auf NULL gesetzt, so wird
	 * der Originaldateiname verwendet. 
	 * 
	 * @return void
	 * @param string
	 */
	public function setOverwriteName($value) {
		$this->_overwriteName=$value;
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param string Pfad zu der Datei
	 * @param boolean Flag, ob die Dateierweiterung bei der Namensermittlung
	 * entfernt werden soll,
	 */
	public function __construct($path, $removeExtension=false) {
		$this->path=$path;
		$this->removeExtension=$removeExtension;
	}
	
	/**
	 * Gibt die Größe der referenzierten Datei zurück.
	 * 
	 * @return integer
	 */
	public function getContentSize() {
		return(filesize($this->path));
	}
	
	/**
	 * Gibt den im Archiv zu verwendenden Namen zurück.
	 * 
	 * @return string
	 */
	public function getName() {
		if(($n=$this->getOverwriteName())!==NULL) {
			return($n);
		}
		
		$basename=basename($this->path);
			
		// Dateierweiterung abtrennen
		if($this->removeExtension) {
			$pos=strrpos($basename, '.');
			if($pos!==false) {
				$basename=substr($basename, 0, $pos);
			}
		}

		return($basename);
	}
	
	/**
	 * Gibt den Inhalt der referenzierten Datei zurück.
	 * 
	 * @return string
	 */
	public function getContents() {
		return(file_get_contents($this->path));
	}
	
}