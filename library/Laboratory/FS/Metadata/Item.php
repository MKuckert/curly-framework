<?php

/**
 * FS_Metadata_Item
 * 
 * Metadaten-Datensatz für einen Leaf-Knoten.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Metadata
 * @since 1.0 - 01.06.2009
 * @version 1.0
 */
class FS_Metadata_Item extends FS_Metadata_Container {
	
	/**
	 * @var string Dateninhalt
	 */
	private $_contents='';
	
	/**
	 * Gibt den Dateninhalt zurück.
	 * 
	 * @return string
	 */
	public function getContents() {
		return $this->_contents;
	}
	
	/**
	 * Setzt den Dateninhalt.
	 * 
	 * @return FS_Metadata_Item
	 * @param string
	 */
	public function setContents($value) {
		$this->_contents=(string)$value;
	}
	
	/**
	 * Gibt die Größe des Dateninhalt zurück.
	 * 
	 * @return integer
	 */
	public function getContentSize() {
		return strlen($this->_contents);
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param string Dateninhalt
	 * @param integer Zeitpunkt des letzten Zugriffs.
	 * @param integer Zeitpunkt der letzten Modifizierung.
	 */
	public function __construct($contents='', $atime=0, $mtime=0) {
		parent::__construct($atime, $mtime);
		$this->setContents($contents);
	}
	
}