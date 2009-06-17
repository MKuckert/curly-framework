<?php

/**
 * Curly_Types_String_Iterator
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @package Curly.Types.String
 * @since 1.0 - 15.11.2008
 * @version 1.0
 */
class Curly_Types_String_Iterator implements Iterator {
	
	/**
	 * @var string
	 */
	private $_s;
	
	/**
	 * @var integer
	 */
	private $_pos=0;
	
	/**
	 * @param string
	 */
	public function __construct($value) {
		$this->_s=(string)$value;
	}
	
	/**
	 * @return string
	 */
	public function current() {
		return($this->_s[$this->_pos]);
	}
	
	/**
	 * @return integer
	 */
	public function key() {
		return($this->_pos);
	}
	
	/**
	 * @return void
	 */
	public function next() {
		$this->_pos++;
	}
	
	/**
	 * @return void
	 */
	public function rewind() {
		$this->_pos=0;
	}
	
	/**
	 * @return boolean
	 */
	public function valid() {
		return(strlen($this->_s)>=$this->_pos+1);
	}
	
}