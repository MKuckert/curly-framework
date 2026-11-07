<?php

/**
 * FS_Metadata_Container
 * 
 * Metadaten-Datensatz für einen Container-Knoten.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Metadata
 * @since 1.0 - 01.06.2009
 * @version 1.0
 */
class FS_Metadata_Container extends FS_Metadata_Base {
	
	/**
	 * @var integer Zeitpunkt des letzten Zugriffs.
	 */
	private $_accessTime=0;
	
	/**
	 * @var integer Zeitpunkt der letzten Modifizierung.
	 */
	private $_modificationTime=0;
	
	/**
	 * Gibt den Zeitpunkt des letzten Zugriffs zurück.
	 * 
	 * @return integer
	 */
	public function getAccessTime() {
		return $this->_accessTime;
	}
	
	/**
	 * Setzt den Zeitpunkt des letzten Zugriffs.
	 * 
	 * @return FS_Metadata_Container
	 * @param integer
	 */
	public function setAccessTime($value) {
		$this->_accessTime=(int)$value;
		return $this;
	}
	
	/**
	 * Gibt den Zeitpunkt der letzten Modifizierung zurück.
	 * 
	 * @return integer
	 */
	public function getModificationTime() {
		return $this->_modificationTime;
	}
	
	/**
	 * Setzt den Zeitpunkt der letzten Modifizierung.
	 * 
	 * @return FS_Metadata_Container
	 * @param integer
	 */
	public function setModificationTime($value) {
		$this->_modificationTime=(int)$value;
		return $this;
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param integer Zeitpunkt des letzten Zugriffs.
	 * @param integer Zeitpunkt der letzten Modifizierung.
	 */
	public function __construct($atime=0, $mtime=0) {
		parent::__construct();
		$this
			->setAccessTime($atime)
			->setModificationTime($mtime);
	}
	
}