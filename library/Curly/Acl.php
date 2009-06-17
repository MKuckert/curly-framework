<?php

require_once "Zend/Acl.php";

/**
 * @since 26.10.2008
 */
abstract class Curly_Acl extends Zend_Acl {
	
	/*
	 * @var array
	 */
	protected $_options=array();
	
	/**
	 * @return array
	 */
	public function getOptions() {
		return($this->_options);
	}
	
	/**
	 * @return array|Zend_Config
	 */
	public function setOptions($value) {
		if($value instanceof Zend_Config) {
			$value=$value->toArray();
		}
		if(!is_array($value)) {
			throw new Curly_Acl_Exception('Only array or Zend_Config values are valid options parameter');
		}
		
		foreach($value as $key=>$param) {
			$this->_options[$key]=$param;
		}
	}
	
	abstract public function load();
	
}