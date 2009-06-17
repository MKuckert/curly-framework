<?php

require_once "Curly/Sourcecode/Tokens.php";
require_once "Curly/Sourcecode/ProcessorStrategy/Interface.php";

/**
 * Curly_Sourcecode_Processor
 * 
 * Stellt einen Prozessor dar, der auf einer
 * Curly_Sourcecode_Tokens-Instanz operiert und registrierte
 * ProcessorStrategy-Instanzen auf diese anwendet. Dabei wird für jeden Token
 * aber höchstens eine Strategy-Instanz verwendet, d.h. die
 * Registrationsreihenfolge ist entscheidend.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
class Curly_Sourcecode_Processor {
	
	/**#@+
	 * Konstante für ein mögliches Fehlerverhalten
	 */
	const ERR_EXCEPTIONS=1;
	const ERR_WARNING=2;
	const ERR_COLLECT=3;
	const ERR_NONE=4;
	/**#@-*/
	
	/**
	 * Tokentyp, der auf jeden Token zutrifft. So kann eine Strategy-Instanz
	 * mehrere Tokentypen verarbeiten.
	 */
	const ALL_TOKENTYPES='--ALL';
	
	/**
	 * @var Curly_Sourcecode_Tokens Zu verwendende Tokens-Instanz.
	 */
	private $_tokens;
	
	/**
	 * @var integer Index des aktuell verarbeiteten Token.
	 */
	private $index;
	
	/**
	 * @var integer Flag zum Fehlerverhalten.
	 */
	private $_errorHandling=self::ERR_EXCEPTIONS;
	
	/**
	 * @var array Gesammelte Fehler im Fehlerverhalten ERR_COLLECT.
	 */
	private $_collectedErrors=array();
	
	/**
	 * @var array Liste von ProcessorStrategy-Instanzen, die sich für einen
	 * entsprechenden Typ registriert haben.
	 */
	protected $_strategies=array();
	
	/**
	 * Gibt die zu verwendende Tokens-Instanz zurück.
	 * 
	 * @return Curly_Sourcecode_Tokens
	 */
	public function getTokens() {
		return $this->_tokens;
	}
	
	/**
	 * Setzt die zu verwendende Tokens-Instanz.
	 * 
	 * @return void
	 * @param Curly_Sourcecode_Tokens
	 */
	public function setTokens(Curly_Sourcecode_Tokens $tokens) {
		$this->_tokens=$tokens;
	}
	
	/**
	 * Gibt den aktuellen Index zurück.
	 * 
	 * @return integer
	 */
	public function getCurrentTokenIndex() {
		return $this->index;
	}
	
	/**
	 * Setzt den aktuellen Index.
	 * 
	 * @throws Curly_Sourcecode_Exception
	 * @return void
	 * @param integer
	 */
	public function setCurrentTokenIndex($value) {
		$value=abs($value);
		$tokenCount=$this->getTokens()->getTokens()->count();
		if($value>=$tokenCount) {
			$this->raiseError('The index '.$value.' is out of bounds');
			$value=$tokenCount-1;
		}
		$this->index=$value;
	}
	
	/**
	 * Gibt das Flag zum Fehlerverhalten zurück.
	 * 
	 * @return integer
	 */
	public function getErrorHandling() {
		return $this->_errorHandling;
	}
	
	/**
	 * Setzt das Flag zum Fehlerverhalten.
	 * 
	 * @return void
	 * @param integer
	 */
	public function setErrorHandling($value) {
		$this->_errorHandling=$value;
	}
	
	/**
	 * Gibt die gesammelten Fehler im Fehlerverhalten ERR_COLLECT zurück.
	 * Jedes Element im Array ist ein Array mit der Fehlermeldung an Index 0
	 * und dem Kontext-Präprozessorausdruck an Index 1, wenn vorhanden.
	 * 
	 * @return array
	 */
	public function getCollectedErrors() {
		return $this->_collectedErrors;
	}
	
	/**
	 * Setzt die gesammelten Fehler zurück.
	 * 
	 * @return void
	 */
	public function resetCollectedErrors() {
		$this->_collectedErrors=array();
	}
	
	/**
	 * Registriert eine ProcessorStrategy-Instanz für einen Tokentyp an dieser
	 * Instanz.
	 * 
	 * @return void
	 * @param string|integer Tokentyp
	 * @param Curly_Sourcecode_ProcessorStrategy_Interface
	 */
	public function registerStrategy($tokentype, Curly_Sourcecode_ProcessorStrategy_Interface $processor) {
		if(!isset($this->_strategies[$tokentype])) {
			$this->_strategies[$tokentype]=array();
		}
		$this->_strategies[$tokentype][]=$processor;
		$processor->registeredAt($this, $tokentype);
	}
	
	/**
	 * Löst die Behandlung des übergebenen Fehlers aus.
	 * 
	 * @return void
	 * @param string Fehlermeldung
	 * @param string Optionale fehlerhafte Präprozessoranweisung
	 */
	public function raiseError($msg, $expression=NULL) {
		switch($this->_errorHandling) {
			case self::ERR_EXCEPTIONS:
				$this->raiseException($msg, $expression);
				break;
			case self::ERR_WARNING:
				if($expression!==NULL) {
					$msg.=' (Expression: '.$expression.')';
				}
				
				trigger_error($msg, E_USER_ERROR);
				break;
			case self::ERR_COLLECT:
				$this->_collectedErrors[]=array(
					$msg, $expression
				);
				break;
			default:
				// Nichts tun
		}
	}
	
	/**
	 * Löst die Behandlung des übergebenen Fehlers als Ausnahme aus.
	 * 
	 * @throws Curly_Sourcecode_Exception
	 * @return void
	 * @param string Fehlermeldung
	 * @param string Optionale fehlerhafte Präprozessoranweisung
	 */
	protected function raiseException($msg, $expression=NULL) {
		if($expression!==NULL) {
			if(is_array($expression)) {
				if(array_keys($expression)!=range(0, count($expression)-1)) {
					$buf=array();
					foreach($expression as $k=>$v) {
						$buf[]=$k.': '.$v;
					}
					$expression=$buf;
				}
				$expression=implode(', ', $expression);
			}
			$msg.=' (Expression: '.$expression.')';
		}
		
		require_once "Curly/Sourcecode/Exception.php";
		throw new Curly_Sourcecode_Exception($msg);
	}
	
	/**
	 * Führt den Präprozessor-Vorgang durch.
	 * 
	 * @return void
	 */
	public function process() {
		$tokens=$this->getTokens();
		$tokensCollection=$tokens->getTokens();
		for($this->index=0; $this->index<$tokensCollection->count(); $this->index++) {
			$this->processToken();
		}
	}
	
	/**
	 * Verarbeitet den aktuellen Token.
	 * 
	 * @return void
	 */
	protected function processToken() {
		$type=$this->getTokens()->getTokenTypeAt($this->index);
		$token=$this->getTokens()->getTokenAt($this->index);
		
		if(!$this->dispatchStrategy($token, $type)) {
			$this->dispatchStrategy($token, self::ALL_TOKENTYPES);
		}
	}
	
	/**
	 * Überprüft alle Strategy-Instanzen, ob eine von diesen den übergebenen
	 * Token verarbeiten wollen.
	 * 
	 * @return boolean true, wenn eine Strategy diesen Token bearbeitet hat.
	 * @param mixed Der Token
	 * @param string|integer Typ des Tokens
	 */
	protected function dispatchStrategy($token, $type) {
		if(!isset($this->_strategies[$type])) {
			return false;
		}
		
		foreach($this->_strategies[$type] as $strategy) {
			if($strategy->acceptsToken($token)) {
				$strategy->processToken($token);
				return true;
			}
		}
		return false;
	}
	
}