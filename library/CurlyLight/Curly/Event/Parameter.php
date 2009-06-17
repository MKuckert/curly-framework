<?php

require_once "Curly/Event/Parameter/Interface.php";

/**
 * Curly_Event_Parameter
 * 
 * Basisimplementation des Curly_Event_Parameter_Interface-Interface.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Event.Parameter
 * @since 1.0 - 25.11.2008
 * @version 1.0
 */
class Curly_Event_Parameter implements Curly_Event_Parameter_Interface {
	
	/**
	 * @var mixed
	 */
	private $_source=NULL;
	
	/**
	 * Gibt die Objektinstanz zurück, von der dieses Event ausgelöst wurde.
	 * 
	 * @return mixed
	 */
	public function getSource() {
		return($this->_source);
	}
	/**
	 * Setzt die Objektinstanz, von der dieses Event ausgelöst wurde.
	 * 
	 * @return void
	 * @param mixed
	 */
	public function setSource($source) {
		$this->_source=$source;
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param mixed Objektinstanz, von der dieses Event ausgelöst wurde.
	 */
	public function __construct($source) {
		$this->setSource($source);
	}
	
}