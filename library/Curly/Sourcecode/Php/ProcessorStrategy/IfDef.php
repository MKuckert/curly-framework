<?php

require_once "Curly/Sourcecode/Php/ProcessorStrategy/IfBase.php";
require_once "Curly/Sourcecode/Php/ProcessorStrategy/DefineCollection.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_IfDef
 * 
 * Implementation eines ifdef-Präprozessors.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
class Curly_Sourcecode_Php_ProcessorStrategy_IfDef extends Curly_Sourcecode_Php_ProcessorStrategy_IfBase {
	
	const EXPRESSION='ifdef';
	
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
		$this->_expression=self::EXPRESSION;
	}
	
	/**
	 * Verarbeitet den übergebenen Token mit den Parametern aus dem Ausdruck.
	 * 
	 * @return void
	 * @param mixed Token
	 * @param string Parameterliste aus der Expression
	 */
	protected function _processToken($token, $parameter) {
		$constant=trim($parameter);
		
		// Nach ENDIF-Instruktion suchen
		$endifTokenIndex=$this->findEndifToken();
		
		// ENDIF-Token nicht gefunden
		if($endifTokenIndex==-1) {
			$this->raiseError('Found no '.$this->getEndifExpression().' instruction', $this->getExpression().' '.$constant);
			
			// Token entfernen
			$i=$this->getCurrentIndex();
			$this->getTokens()->removeTokenAt($i);
			$this->setCurrentIndex($i-1);
			
			return;
		}
		
		// Überprüfen, ob Konstante definiert ist
		$index=$this->getCurrentIndex();
		if(!$this->isConstantDefined($constant)) {
			// Gekapselte Tokens entfernen
			$removeRange=$endifTokenIndex-$index+2;
			$this->getTokens()->getTokens()->removeRange($index-1, $removeRange);
			$this->setCurrentIndex($index-1);
		}
		else {
			// Anfangs- und Ende-Tokens entfernen
			$this->getTokens()->removeTokenAt($endifTokenIndex);
			$this->getTokens()->removeTokenAt($index);
			$this->setCurrentIndex($index-1);
		}
	}
	
	/**
	 * Überprüft, ob die Konstante mit dem übergebenen Namen definiert ist.
	 * 
	 * @return boolean
	 * @param string
	 */
	protected function isConstantDefined($name) {
		return $this->_defines->keyExists($name);
	}
	
}