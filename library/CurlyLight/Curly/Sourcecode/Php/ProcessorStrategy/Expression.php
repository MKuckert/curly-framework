<?php

require_once "Curly/Sourcecode/Php/ProcessorStrategy/Prefix.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_Expression
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
abstract class Curly_Sourcecode_Php_ProcessorStrategy_Expression extends Curly_Sourcecode_Php_ProcessorStrategy_Prefix {
	
	/**
	 * @var string Expression, den diese Strategy direkt hinter dem Präfix
	 * erwartet
	 */
	protected $_expression;
	
	/**
	 * @var string Die zuletzt extrahierte Parameterliste
	 */
	protected $_parameterList;
	
	/**
	 * Gibt die Expression, den diese Strategy direkt hinter dem Präfix
	 * erwartet, zurück.
	 * 
	 * @return string
	 */
	public function getExpression() {
		return $this->_expression;
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
		if(!parent::acceptsToken($token)) {
			return false;
		}
		
		// Prefix und Expression per regulären Ausdruck überprüfen und direkt
		// Parameterliste extrahieren
		if(!$this->matchesExpression($token, $this->_expression, $matches)) {
			return false;
		}
		
		$this->_parameterList=isset($matches[1]) ? $matches[1] : NULL;
		return true;
	}
	
	/**
	 * Überprüft, ob der übergebene Token mit der übergebenen Expression
	 * übereinstimmt.
	 * 
	 * @return boolean
	 * @param array Der Token
	 * @param string Die Expression
	 * @param array Referenz für die Übereinstimmungen im regulären Ausdruck
	 */
	protected function matchesExpression($token, $expression, &$matches=NULL) {
		$regex='/^'.
			preg_quote($this->getTokenPrefix(), '/').
			preg_quote($expression, '/').
			'(?:\\s+(.*))?$/i';
		return preg_match($regex, $token[1], $matches)==1;
	}
	
	/**
	 * Wird von der Processor-Instanz für jeden Token des registrierten Typs
	 * aufgerufen.
	 * 
	 * @return void
	 * @param mixed Token
	 */
	public function processToken($token) {
		$this->_processToken($token, $this->_parameterList);
	}
	
	/**
	 * Diese Methode muss von Unterklassen implementiert werden. Zusätzlich zum
	 * Token wird zusätzlich die Parameterliste nach der Expression im Token
	 * übergeben.
	 * 
	 * @return void
	 * @param mixed Token
	 * @param string Parameterliste aus der Expression
	 */
	abstract protected function _processToken($token, $parameter);
	
}