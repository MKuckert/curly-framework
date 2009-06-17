<?php

require_once "Curly/Sourcecode/ProcessorStrategy/Abstract.php";

/**
 * Curly_Sourcecode_ProcessorStrategy_Fail
 * 
 * Löst einen Fehler aus, weil der Token, mit dem diese Instanz aufgerufen wird,
 * nicht bereits vorher bearbeitet wurde.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
class Curly_Sourcecode_ProcessorStrategy_Fail extends Curly_Sourcecode_ProcessorStrategy_Abstract {
	
	/**
	 * Wird von der Processor-Instanz für jeden Token des registrierten Typs
	 * aufgerufen, um zu überprüfen, ob diese Instanz diesen Token bearbeiten
	 * wird.
	 * 
	 * Diese Instanz akzeptiert jeden Token, diese Methode gibt also immer true zurück.
	 * 
	 * @return boolean
	 * @param mixed Token
	 */
	public function acceptsToken($token) {
		return true;
	}
	
	/**
	 * Wird von der Processor-Instanz für jeden Token des registrierten Typs
	 * aufgerufen.
	 * 
	 * @return void
	 * @param mixed Token
	 */
	public function processToken($token) {
		$this->raiseError('Unhandled token of type '.$this->getTokentype().' at index '.$this->getCurrentIndex(), $token);
	}
	
}