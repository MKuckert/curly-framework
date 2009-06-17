<?php

require_once "Curly/Types/Collection.php";
require_once "Curly/Sourcecode/Php/ProcessorStrategy/IfBase.php";
require_once "Curly/Expression/Evaluator.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_If
 *
 * Implementation eines IF-Präprozessors mit ELSE- und ELSEIF-Unterausdrücken.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
class Curly_Sourcecode_Php_ProcessorStrategy_If extends Curly_Sourcecode_Php_ProcessorStrategy_IfBase {

	const EXPRESSION='if';
	const ELIF_EXPRESSION='elif';
	const ELSE_EXPRESSION='else';
	
	/**
	 * @var string Der Ausdruck für den ELIF-Token
	 */
	private $_elifToken=self::ELIF_EXPRESSION;
	
	/**
	 * @var string Der Ausdruck für den ELSE-Token
	 */
	private $_elseToken=self::ELSE_EXPRESSION;
	
	/**
	 * @var Curly_Types_Collection Lookup-Collection für Werte in If-Ausdrücken.
	 */
	private $_values=NULL;
	
	/**
	 * @var Curly_Expression_Evaluator Instanz zum Auswerten von Ausdrücken.
	 */
	private $_expressionEvaluator=NULL;
	
	/**
	 * Gibt den Ausdruck für den ELIF-Token zurück.
	 * 
	 * @return string
	 */
	public function getElifToken() {
		return $this->_elifToken;
	}
	
	/**
	 * Gibt den Ausdruck für den ELSE-Token zurück.
	 * 
	 * @return string
	 */
	public function getElseToken() {
		return $this->_elseToken;
	}
	
	/**
	 * Gibt die Lookup-Collection für Werte in If-Ausdrücken zurück.
	 * 
	 * @return Curly_Types_Collection
	 */
	public function getValues() {
		if($this->_values==NULL) {
			$this->_values=new Curly_Types_Collection();
		}
		return $this->_values;
	}
	
	/**
	 * Gibt die Instanz zum Auswerten von Ausdrücken zurück.
	 * 
	 * @return Curly_Expression_Evaluator
	 */
	public function getExpressionEvaluator() {
		if($this->_expressionEvaluator==NULL) {
			$this->_expressionEvaluator=new Curly_Expression_Evaluator($this->getValues());
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
	 * 
	 * @param Ausdruck für den ELIF-Token
	 * @param Ausdruck für den ELSE-Token
	 * @param Curly_Types_Collection Standard-Werte
	 */
	public function __construct(Curly_Types_Collection $values=NULL, $elifToken=NULL, $elseToken=NULL) {
		if($values!==NULL) {
			$this->_values=$values;
		}
		if($elifToken!==NULL) {
			$this->_elifToken=$elifToken;
		}
		if($elseToken!==NULL) {
			$this->_elseToken=$elseToken;
		}
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
		// TODO: CLEAN ME UP!
		
		$ifExpression=trim($parameter);
		
		// Nach ENDIF-Instruktion suchen
		$endIfTokenIndex=$this->findEndifToken();
		
		// ENDIF-Token nicht gefunden
		if($endIfTokenIndex==-1) {
			$this->raiseError('Found no '.$this->getEndifExpression().' instruction', $this->getExpression().' '.$constant);
			
			// Token entfernen
			$i=$index;
			$this->getTokens()->removeTokenAt($i);
			$this->setCurrentIndex($i-1);
			
			return;
		}
		
		// Nach ELIF-Token suchen
		$elifTokenIndices=array();
		$elifTokenIndex=$this->getCurrentIndex();
		do {
			$elifTokenIndex=$this->findToken($this->getElifToken(), $elifTokenIndex, $endIfTokenIndex);
		}
		while($elifTokenIndex!=-1 and $elifTokenIndices[]=$elifTokenIndex);
		
		// Nach ELSE-Token suchen
		$hasElifTokens=count($elifTokenIndices)>0;
		if($hasElifTokens) {
			$startIndex=end($elifTokenIndices);
		}
		else {
			$startIndex=$this->getCurrentIndex();
		}
		$elseTokenIndex=$this->findToken($this->getElseToken(), $startIndex, $endIfTokenIndex);
		$hasElseToken=$elseTokenIndex!=-1;
		
		//// Ausdrücke auswerten
		$tokens=$this->getTokens();
		$tokensCollection=$tokens->getTokens();
		
		// IF-Ausdruck auswerten
		if($this->isTrueExpression($ifExpression)) {
			// Token ermitteln, bis zu dem Block übernommen werden soll
			if($hasElifTokens) {
				$useUntil=reset($elifTokenIndices);
			}
			else if($hasElseToken) {
				$useUntil=$elseTokenIndex;
			}
			else {
				$useUntil=$endIfTokenIndex;
			}
			
			// Tokens am Ende entfernen
			$tokensCollection->removeRange($useUntil, $endIfTokenIndex-$useUntil+1);
			
			// IF-Token entfernen
			$tokens->removeTokenAt($this->getCurrentIndex());
			
			return;
		}
		
		// ELIF-Ausdrücke auswerten
		foreach($elifTokenIndices as $key=>$elifTokenIndex) {
			$token=$this->getTokens()->getTokenAt($elifTokenIndex);
			if(!$this->matchesExpression($token, $this->getElifToken(), $matches)) {
				continue;
			}
			
			$expression=$matches[1];
			// Ausdruck auswerten
			if($this->isTrueExpression($expression)) {
				// Tokens am Ende entfernen
				if(isset($elifTokenIndices[$key+1])) {
					$useUntil=$elifTokenIndices[$key+1];
				}
				else if($hasElseToken) {
					$useUntil=$elseTokenIndex;
				}
				else {
					$useUntil=$endIfTokenIndex;
				}
				
				$tokensCollection->removeRange($useUntil, $endIfTokenIndex-$useUntil+1);
				
				// Vordere Tokens entfernen
				$index=$this->getCurrentIndex();
				$tokensCollection->removeRange($index, $elifTokenIndex-$index+1);
				
				return;
			}
		}
		
		// ELSE verwenden
		if($hasElseToken) {
			// ENDIF-Token entfernen
			$tokens->removeTokenAt($endIfTokenIndex);
			
			// Vordere Tokens entfernen
			$index=$this->getCurrentIndex();
			$tokensCollection->removeRange($index, $elseTokenIndex-$index+1);
		}
		// Keinen Ausdruck verwenden
		else {
			$index=$this->getCurrentIndex();
			$tokensCollection->removeRange($index, $endIfTokenIndex-$index+1);
		}
	}
	
	/**
	 * Überprüft, ob der übergebene Ausdruck zu true evaluiert.
	 * 
	 * @return boolean
	 * @param string
	 */
	protected function isTrueExpression($expression) {
		return $this->getExpressionEvaluator()->evaluate($expression);
	}

}