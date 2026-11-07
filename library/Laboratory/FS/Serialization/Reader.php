<?php

/**
 * FS_Serialization_Reader
 * 
 * Klasse zum Lesen eines Serialisierten Baumes.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Serialization
 * @since 1.0 - 01.06.2009
 * @version 1.0
 */
interface FS_Serialization_Reader {
	
	/**
	 * Ließt die serialisierten Daten aus der Datei mit dem übergebenen Namen.
	 * 
	 * @throws FS_Exception
	 * @return FS_Tree
	 * @param string
	 */
	public function readFile($filename);
	
	/**
	 * Ließt die serialisierten Daten aus der übergebenen Zeichenkette.
	 * 
	 * @throws FS_Exception
	 * @return FS_Tree
	 * @param string
	 */
	public function readString($data);
	
}