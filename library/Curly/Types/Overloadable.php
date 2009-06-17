<?php

/**
 * Curly_Types_Overloadable
 * 
 * Basisimplementation der __get- und __set-Methoden, um über get- und
 * set-Methoden strengtypisierte Objekteigenschaften zu simulieren.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types
 * @since 1.0 - 27.02.2008
 * @version 1.0
 */
abstract class Curly_Types_Overloadable {
	
	/**
	 * Simuliert den lesenden Zugriff auf eine Eigenschaft.
	 * 
	 * @throws Curly_Exception
	 * @return mixed
	 * @param string Der Name der Eigenschaft
	 */
	public function __get($name) {
		$method='get'.$name;
		if(method_exists($this,$method)) {
			return $this->$method();
		}
		else {
			throw new Curly_Exception('There is no property '.$name);
		}
	}

	/**
	 * Simuliert den schreibenden Zugriff auf eine Eigenschaft.
	 * 
	 * @throws Curly_Exception
	 * @return mixed
	 * @param string Der Name der Eigenschaft
	 * @param mixed Der zu setzende Wert der Eigenschaft
	 */
	public function __set($name, $value) {
		$method='set'.$name;
		if(method_exists($this, $method)) {
			return $this->$method($value);
		}
		else if(method_exists($this,'get'.$name)) {
			throw new Curly_Exception('The property '.$name.' is readonly');
		}
		else {
			throw new Curly_Exception('There is no property '.$name);
		}
	}

	/**
	 * Überprüft, ob eine Objekt-Eigenschaft vorhanden ist. Dies ist der Fall,
	 * wenn eine entsprechende getter- oder setter-Methode vorhanden ist.
	 * 
	 * @return boolean
	 * @param string Der Name der Eigenschaft
	 */
	public function hasProperty($name) {
		return (method_exists($this,'get'.$name) or method_exists($this,'set'.$name));
	}

	/**
	 * Überprüft, ob die Objekt-Eigenschaft mit dem übergebenen Namen lesbar
	 * ist.
	 * 
	 * @return boolean
	 * @param string Der Name der Eigenschaft
	 */
	public function canGetProperty($name) {
		return method_exists($this,'get'.$name);
	}

	/**
	 * Überprüft, ob die Objekt-Eigenschaft mit dem übergebenen Namen schreibbar
	 * ist.
	 * 
	 * @return boolean
	 * @param string Der Name der Eigenschaft
	 */
	public function canSetProperty($name) {
		return method_exists($this,'set'.$name);
	}
	
}