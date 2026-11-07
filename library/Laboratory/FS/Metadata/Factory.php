<?php

/**
 * FS_Metadata_Factory
 * 
 * Fabrikklasse zum Erstellen von Metadata-Elementen.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Metadata
 * @since 1.0 - 01.06.2009
 * @version 1.0
 */
class FS_Metadata_Factory {
	
	/**
	 * @var FS_Metadata_Factory Statische Standardinstanz.
	 */
	static private $_default=NULL;
	
	/**
	 * Gibt die statische Standardinstanz zurück.
	 * 
	 * @return FS_Metadata_Factory
	 */
	static public function getDefault() {
		if(self::$_default===NULL) {
			self::$_default=new self();
		}
		return self::$_default;
	}
	
	/**
	 * Setzt die statische Standardinstanz.
	 * 
	 * @return void
	 * @param FS_Metadata_Factory
	 */
	static public function setDefault(FS_Metadata_Factory $value) {
		self::$_default=$value;
	}
	
	/**
	 * Erstellt eine neue FS_Metadata_Interface-Instanz zu der übergebenen
	 * Typenkennung.
	 * 
	 * @throws FS_Exception
	 * @return FS_Metadata_Interface
	 * @param string
	 */
	public function fabricate($type) {
		switch(strtolower($type)) {
			case 'container':
				return new FS_Metadata_Container();
			case 'item':
				return new FS_Metadata_Item();
			default:
				throw new FS_Exception('Unable to create a metadata instance for the type identifier '.$type);
		}
	}
	
}