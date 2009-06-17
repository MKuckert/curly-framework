<?php

/**
 * CurlyX_Archive_Dataset_Interface
 *
 * Interface für einen Datensatz in einem Archiv.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Dataset
 * @since 1.0 - 13.12.2008
 * @version 1.0
 */
interface CurlyX_Archive_Dataset_Interface {
	
	/**
	 * Gibt die Größe dieses Datensatzes zurück.
	 * 
	 * @return integer
	 */
	public function getContentSize();
	
	/**
	 * Gibt den im Archiv zu verwendenden Namen zurück.
	 * 
	 * @return string
	 */
	public function getName();
	
	/**
	 * Gibt den Inhalt dieses Datensatzes zurück.
	 * 
	 * @return string
	 */
	public function getContents();
	
}