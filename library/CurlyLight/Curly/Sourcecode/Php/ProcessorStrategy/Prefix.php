<?php

require_once "Curly/Sourcecode/ProcessorStrategy/Abstract.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_Prefix
 * 
 * Diese Strategie erkennt einen Token anhand eines Präfixes im Tokenwert diesen
 * als gültig und gibt in der acceptsToken-Methode einen entsprechenden Wert
 * zurück.
 * Der Standardwert des 
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.Php.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
abstract class Curly_Sourcecode_Php_ProcessorStrategy_Prefix extends Curly_Sourcecode_Php_ProcessorStrategy_Abstract {
	
	/**
	 * @var string Token-Präfix, damit diese Strategy einen Token akzeptiert. 
	 */
	private $_tokenPrefix='#';
	
	/**
	 * Gibt das Token-Präfix, damit diese Strategy einen Token akzeptiert,
	 * zurück.
	 * 
	 * @return string
	 */
	public function getTokenPrefix() {
		return $this->_tokenPrefix;
	}
	
	/**
	 * Setzt das Token-Präfix, mit dem diese Strategy einen Token akzeptiert.
	 * 
	 * @return void
	 * @param string
	 */
	public function setTokenPrefix($value) {
		$this->_tokenPrefix=$value;
	}
	
	/**
	 * Wird von der Processor-Instanz für jeden Token des registrierten Typs
	 * aufgerufen, um zu überprüfen, ob diese Instanz diesen Token bearbeiten
	 * wird.
	 * 
	 * @return boolean
	 * @param mixed Token
	 */
	public function acceptsToken($token) {
		return $this->matchesPrefix($token);
	}
	
	/**
	 * Überprüft, ob der übergebene Token dem Präfix genügt.
	 * 
	 * @return boolean
	 * @param mixed Token
	 */
	protected function matchesPrefix($token) {
		$prefix=$this->_tokenPrefix;
		return substr($token[1], 0, strlen($prefix))==$prefix;
	}
	
}