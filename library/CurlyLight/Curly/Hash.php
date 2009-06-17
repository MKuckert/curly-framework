<?php

/**
 * Curly_Hash
 *
 * Registry für eine Curly_Hash_Interface-Implementations-Instanz.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly
 * @since 1.0 - 26.03.2008
 * @version 1.0
 */
class Curly_Hash {

	/**
	 * @var Curly_Hash_Interface Zu verwendende Implementations-Instanz
	 */
	static private $_impl=NULL;

	/**
	 * Gibt die zu verwendende Implementations-Instanz zurück.
	 *
	 * @return Curly_Hash_Interface
	 */
	static public function getImplementation() {
		if(self::$_impl===NULL) {
			self::setImplementation(new Curly_Hash_Sha1());
		}
		return(self::$_impl);
	}

	/**
	 * Setzt die zu verwendende Implementations-Instanz.
	 *
	 * @return void
	 * @param Curly_Hash_Interface
	 */
	static public function setImplementation(Curly_Hash_Interface $value) {
		self::$_impl=$value;
	}

}