<?php

require_once "CurlyX/Archive/Dataset/Interface.php";
require_once "Curly/Types/Ensure.php";

/**
 * CurlyX_Archive_Dataset_Interface
 *
 * Ein Datensatz in Form einer Zeichenkette.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Dataset
 * @since 1.0 - 13.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Dataset_String implements CurlyX_Archive_Dataset_Interface {
	
	/**
	 * @var string Name des Datensatzes 
	 */
	private $name;
	
	/**
	 * @var string Inhalt des Datensatzes 
	 */
	private $contents;
	
	/**
	 * Konstruktor
	 * 
	 * @param string Name des Datensatzes 
	 * @param string Inhalt des Datensatzes 
	 */
	public function __construct($name, $contents) {
		$this->name=$name;
		$this->contents=$contents;
	}
	
	/**
	 * Gibt die Größe dieses Datensatzes zurück.
	 * 
	 * @return integer
	 */
	public function getContentSize() {
		return(strlen($this->contents));
	}
	
	/**
	 * Gibt den im Archiv zu verwendenden Namen zurück.
	 * 
	 * @return string
	 */
	public function getName() {
		return($this->name);
	}
	
	/**
	 * Setzt den im Archiv zu verwendenden Namen.
	 * 
	 * @return void
	 * @param string
	 */
	public function setName($name) {
		$this->name=Curly_Types_Ensure::string($name);
	}
	
	/**
	 * Gibt den Inhalt dieses Datensatzes zurück.
	 * 
	 * @return string
	 */
	public function getContents() {
		return($this->contents);
	}
	
	/**
	 * Setzt den Inhalt dieses Datensatzes.
	 * 
	 * @return void
	 * @param string
	 */
	public function setContents($content) {
		$this->contents=Curly_Types_Ensure::string($content);
	}
	
}