<?php

require_once "Curly/Assets/Manager.php";

/**
 * Curly_Assets
 * 
 * Registry-Klasse für das Curly.Assets-Paket.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly
 * @since 1.0 - 26.11.2008
 * @version 1.0
 */
class Curly_Assets {
	
	/**
	 * @var Curly_Assets_Manager Zu verwendende Manager-Instanz
	 */
	static private $_manager=NULL;
	
	/**
	 * Gibt die zu verwendende Manager-Instanz zurück.
	 * 
	 * @return Curly_Assets_Manager
	 */
	static public function getManager() {
		if(self::$_manager===NULL) {
			self::setManager(new Curly_Assets_Manager());
		}
		return(self::$_manager);
	}
	
	/**
	 * Setzt die zu verwendende Manager-Instanz.
	 * 
	 * @return void
	 * @param Curly_Assets_Manager
	 */
	static public function setManager(Curly_Assets_Manager $manager) {
		self::$_manager=$manager;
	}
	
	/**
	 * Überprüft, ob bereits eine Manager-Instanz vorhanden ist.
	 * 
	 * @return boolean
	 */
	static public function hasManager() {
		return self::$_manager!==NULL;
	}
	
}