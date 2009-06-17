<?php

/**
 * Curly_Hash_Interface
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Hash
 * @since 1.0 - 26.03.2008
 * @version 1.0
 */
interface Curly_Hash_Interface {

	/**
	 * Gibt den Hash der übergebenen Zeichenkette zurück.
	 *
	 * @return string
	 * @param string
	 */
	public function hash($str);

	/**
	 * Erzeugt einen zufälligen Hash.
	 *
	 * @return string
	 */
	public function rand();

}
