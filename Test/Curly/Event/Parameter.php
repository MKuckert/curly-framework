<?php

require_once "Curly/Event/Parameter/Interface.php";

class Test_Curly_Event_Parameter extends Curly_Event_Parameter {
	
	/**
	 * @var integer
	 */
	private $_num=0;
	
	/**
	 * @return integer
	 */
	public function getNum() {
		return($this->_num);
	}
	
	/**
	 * @return void
	 * @param integer
	 */
	public function setNum($num) {
		$this->_num=Curly_Types_Ensure::int($num);
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param mixed Objektinstanz, von der dieses Event ausgelöst wurde.
	 * @param integer
	 */
	public function __construct($source, $num) {
		parent::__construct($source);
		$this->setNum($num);
	}
	
}