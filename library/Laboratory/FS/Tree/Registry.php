<?php

/**
 * FS_Tree_Registry
 * 
 * Statische Registry für die Registrierung von Baum-Objekten. Wird von
 * StreamWrapper-Objekten verwendet um den Kontextbaum zu ermitteln.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Tree
 * @since 1.0 - 01.06.2009
 * @version 1.0
 */
abstract class FS_Tree_Registry {
	
	/**
	 * @var array Liste registrierter Baumobjekte
	 */
	static private $_list=array();
	
	/**
	 * Versucht ein registriertes Baumobjekt zu dem übergebenen Namen zurück zu
	 * geben.
	 * 
	 * @return FS_Tree oder NULL
	 * @param string Name unter dem der Baum registriert wurde.
	 */
	static public function get($name) {
		if(isset(self::$_list[$name])) {
			return self::$_list[$name];
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * Registriert das übergebene Baumobjekt in dieser Registry.
	 * 
	 * @return void
	 * @param string Name unter dem der Baum registriert werden soll.
	 * @param FS_Tree
	 */
	static public function set($name, FS_Tree $tree) {
		self::$_list[$name]=$tree;
	}
	
}