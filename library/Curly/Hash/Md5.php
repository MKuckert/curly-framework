<?php

/**
 * Curly_Hash_Md5
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Hash
 * @since 1.0 - 26.03.2008
 * @version 1.0
 */
class Curly_Hash_Md5 extends Curly_Hash_Abstract {
	
	/**
	 * Gibt den Hash der übergebenen Zeichenkette zurück.
	 *
	 * @return string
	 * @param string
	 */
	public function hash($str) {
		return md5($str);
	}

}
