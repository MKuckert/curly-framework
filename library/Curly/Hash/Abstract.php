<?php

/**
 * Curly_Hash_Abstract
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Hash
 * @since 1.0 - 26.03.2008
 * @version 1.0
 */
abstract class Curly_Hash_Abstract implements Curly_Hash_Interface {

	/**
	 * Erzeugt einen zufälligen Hash.
	 *
	 * @return string
	 */
	public function rand() {
		if(function_exists('mt_rand')) {
			$prefix=mt_rand();
		}
		else {
			$prefix=rand();
		}
		$prefix.=microtime(true);
		
		return $this->hash(uniqid($prefix, true));
	}

}
