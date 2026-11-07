<?php

/**
 * FS_Metadata_Base
 * 
 * Basisimplementation für einen Metadaten-Datensatz.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Metadata
 * @since 1.0 - 01.06.2009
 * @version 1.0
 */
abstract class FS_Metadata_Base implements FS_Metadata_Interface {
	
	/**
	 * @var integer Counter zur automatischen ID-Vergabe.
	 */
	static protected $autoID=0;
	
	/**
	 * @var integer ID dieses Datensatzes.
	 */
	protected $_id;
	
	/**
	 * Gibt die ID dieses Datensatzes zurück.
	 * 
	 * @return integer
	 */
	public function getID() {
		return $this->_id;
	}
	
	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->_id=self::$autoID++;
	}
	
}