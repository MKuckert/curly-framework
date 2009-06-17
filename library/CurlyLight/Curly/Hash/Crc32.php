<?php

/**
 * Curly_Hash_Crc32
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Hash
 * @since 1.0 - 26.03.2008
 * @version 1.0
 */
class Curly_Hash_Crc32 extends Curly_Hash_Abstract {
	
	/**
	 * @var integer Zu verwendende Basis für die Rückgabe.
	 */
	private $_base=36;
	
	/**
	 * Gibt die zu verwendende Basis für die Rückgabe zurück.
	 * 
	 * @return integer
	 */
	public function getBase() {
		return $this->_base;
	}
	
	/**
	 * Setzt die zu verwendende Basis für die Rückgabe.
	 * 
	 * @return void
	 * @param integer
	 */
	public function setBase(integer $value) {
		$this->_base=$value;
	}
	
	/**
	 * Gibt den Hash der übergebenen Zeichenkette zurück.
	 *
	 * @return string
	 * @param string
	 */
	public function hash($str) {
		return base_convert(crc32($str), 10, $this->getBase());
	}

}