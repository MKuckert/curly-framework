<?php

require_once "Curly/Sourcecode/ProcessorStrategy/Interface.php";

/**
 * Curly_Sourcecode_ProcessorStrategy_Abstract
 * 
 * Basisimplementation des ProcessorStrategy-Interface.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
abstract class Curly_Sourcecode_ProcessorStrategy_Abstract implements Curly_Sourcecode_ProcessorStrategy_Interface {
	
	/**
	 * @var Curly_Sourcecode_Processor Processor-Instanz, an der diese Strategy
	 * registriert wurde.
	 */
	private $_processor;
	
	/**
	 * @var string|integer Tokentyp, für den diese Instanz registriert wurde.
	 */
	private $_tokentype;
	
	/**
	 * Gibt die Processor-Instanz, an der diese Strategy registriert wurde,
	 * zurück.
	 * 
	 * @return Curly_Sourcecode_Processor
	 */
	public function getProcessor() {
		return $this->_processor;
	}
	
	/**
	 * Gibt den Tokentyp, für den diese Instanz registriert wurde, zurück.
	 * 
	 * @return string|integer
	 */
	public function getTokentype() {
		return $this->_tokentype;
	}
	
	/**
	 * Gibt die Curly_Sourcecode_Tokens-Instanz der Processor-Instanz zurück,
	 * an der diese Strategy registriert wurde.
	 * 
	 * @return Curly_Sourcecode_Tokens
	 */
	public function getTokens() {
		return $this->_processor->getTokens();
	}
	
	/**
	 * Gibt den aktuellen Token-Index der Processor-Instanz zurück.
	 * 
	 * @return integer
	 */
	public function getCurrentIndex() {
		return $this->_processor->getCurrentTokenIndex();
	}
	
	/**
	 * Setzt den aktuellen Token-Index der Processor-Instanz.
	 * 
	 * @return void
	 * @param integer
	 */
	public function setCurrentIndex($value) {
		$this->_processor->setCurrentTokenIndex($value);
	}
	
	/**
	 * Gibt den aktuellen Token der Processor-Instanz zurück.
	 * 
	 * @return array
	 */
	public function getCurrentToken() {
		return $this->getTokens()->getTokenAt($this->getCurrentIndex());
	}
	
	/**
	 * Löst die Behandlung des übergebenen Fehlers aus.
	 * 
	 * @return void
	 * @param string Fehlermeldung
	 * @param string Optionale fehlerhafte Präprozessoranweisung
	 */
	protected function raiseError($msg, $expression=NULL) {
		$this->_processor->raiseError($msg, $expression);
	}
	
	/**
	 * Wird von der Processor-Instanz nach der Registrierung dieser Instanz
	 * aufgerufen.
	 * 
	 * @return void
	 * @param Curly_Sourcecode_Processor
	 * @param string|integer Tokentyp, für den diese Instanz registriert wurde
	 */
	public function registeredAt(Curly_Sourcecode_Processor $processor, $type) {
		$this->_processor=$processor;
		$this->_tokentype=$type;
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
		return true;
	}
	
}
