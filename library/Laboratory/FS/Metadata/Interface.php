<?php

/**
 * FS_Metadata_Interface
 * 
 * Schnittstelle für einen Metadaten-Datensatz.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Metadata
 * @since 1.0 - 01.06.2009
 * @version 1.0
 */
interface FS_Metadata_Interface {
	
	/**
	 * Gibt die ID dieses Datensatzes zurück.
	 * 
	 * @return integer
	 */
	public function getID();
	
}