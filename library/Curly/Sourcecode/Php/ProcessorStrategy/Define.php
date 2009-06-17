<?php

require_once "Curly/Sourcecode/Php/ProcessorStrategy/Expression.php";
require_once "Curly/Expression/Evaluator.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_Define
 * 
 * Definiert einen Wert in einer DefineCollection-Instanz.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
class Curly_Sourcecode_Php_ProcessorStrategy_Define extends Curly_Sourcecode_Php_ProcessorStrategy_Expression {
	
	const EXPRESSION='define';
	
	/**
	 * @var Curly_Sourcecode_Php_ProcessorStrategy_DefineCollection Collection
	 * für bereis definierte Werte.
	 */
	private $_defines;
	
	/**
	 * @var Curly_Expression_Evaluator Instanz zum Auswerten von Ausdrücken.
	 */
	private $_expressionEvaluator=NULL;
	
	/**
	 * Gibt die Collection für bereis definierte Werte zurück.
	 * 
	 * @return Curly_Sourcecode_Php_ProcessorStrategy_DefineCollection
	 */
	public function getDefines() {
		return $this->_defines;
	}
	
	/**
	 * Gibt die Instanz zum Auswerten von Ausdrücken zurück.
	 * 
	 * @return Curly_Expression_Evaluator
	 */
	public function getExpressionEvaluator() {
		if($this->_expressionEvaluator==NULL) {
			$this->_expressionEvaluator=new Curly_Expression_Evaluator($this->getDefines());
		}
		return $this->_expressionEvaluator;
	}
	
	/**
	 * Setzt die Instanz zum Auswerten von Ausdrücken.
	 * 
	 * @return Curly_Sourcecode_Php_ProcessorStrategy_If
	 * @param Curly_Expression_Evaluator
	 */
	public function setExpressionEvaluator(Curly_Expression_Evaluator $value) {
		$this->_expressionEvaluator=$value;
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
		$split=preg_split('/\\s+/', $parameter, 2);
		if(count($split)!=2) {
			$this->raiseError($this->_expression.' instruction expects 2 arguments', $parameter);
			return;
		}
		
		$value=rtrim($split[1]);
		if(!$this->getExpressionEvaluator()->prepareExpression($value)) {
			$value=true;
		}
		
		$this->_defines->set($split[0], $value);
		
		// Token für Präprozessoranweisung entfernen
		$index=$this->getCurrentIndex();
		$this->getTokens()->removeTokenAt($index);
		$this->setCurrentIndex($index-1);
	}
	
}