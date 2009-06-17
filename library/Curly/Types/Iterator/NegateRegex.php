<?php

/**
 * Curly_Types_Iterator_NegateRegex
 * 
 * Liefert das negierte Ergebnis einer gleichartigen RegexIterator-Instance.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types.Iterator
 * @since 1.0 - 28.10.2008
 * @version 1.0
 */
class Curly_Types_Iterator_NegateRegex extends RegexIterator {
	
	/**
	 * @return boolean
	 */
	public function accept() {
		return(!parent::accept());
	}
	
}
