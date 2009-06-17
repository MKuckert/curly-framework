<?php

require_once "Curly/Sourcecode/Php/ProcessorStrategy/Abstract.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_Define
 * 
 * Ersetzt einen Token durch den Wert einer Konstanten aus einer
 * Define-Collection-Instanz.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
class Curly_Sourcecode_Php_ProcessorStrategy_DefineValue extends Curly_Sourcecode_Php_ProcessorStrategy_Abstract {
	
	/**
	 * @var Curly_Sourcecode_Php_ProcessorStrategy_DefineCollection Collection
	 * für bereis definierte Werte.
	 */
	private $_defines;
	
	/**
	 * Gibt die Collection für bereis definierte Werte zurück.
	 * 
	 * @return Curly_Sourcecode_Php_ProcessorStrategy_DefineCollection
	 */
	public function getDefines() {
		return $this->_defines;
	}
	
	/**
	 * Konstruktor
	 */
	public function __construct(Curly_Sourcecode_Php_ProcessorStrategy_DefineCollection $defines) {
		$this->_defines=$defines;
	}
	
	/**
	 * Verarbeitet den übergebenen Token.
	 * 
	 * @return void
	 * @param mixed Token
	 */
	public function processToken($token) {
		$tokenValue=$token[1];
		
		if(!$this->_defines->keyExists($tokenValue)) {
			return;
		}
		
		$define=$this->_defines->get($tokenValue);
		
		// Wert parsen
		$defineTokens=new Curly_Sourcecode_Php_Tokens();
		$defineTokens->parse($define, true);
		
		$tokens=$this->getTokens();
		$index=$this->getCurrentIndex();
		
		// Aktuellen Token entfernen
		$tokens->removeTokenAt($index);
		
		// Define in Tokens einstreuen
		$inserted=$tokens->insertTokensAt($index, $defineTokens);
		
		// Eingefügte Tokens überspringen
		$this->setCurrentIndex($inserted+$index);
	}
	
}