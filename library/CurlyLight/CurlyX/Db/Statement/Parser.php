<?php

/**
 * CurlyX_Db_Statement_Parser
 * 
 * Ein einfacher SQL-Statement-Parser zum ermitteln der Parameter für
 * Prepared Statements.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db.Statement
 * @since 1.0 - 03.12.2008
 * @version 1.0
 */
class CurlyX_Db_Statement_Parser {
	
	const CHAR_NUMERIC_PARAM='?';
	const CHAR_NAMED_PARAM=':';
	
	/**
	 * @var string Das SQL-Statement
	 */
	private $_statement;
	
	/**
	 * @var array Gefundene Parameter
	 */
	private $_params=array();
	
	/**
	 * Gibt das SQL-Statement zurück.
	 * 
	 * @return string
	 */
	public function getStatement() {
		return($this->_statement);
	}
	
	/**
	 * Gibt die gefundenen Parameter zurück.
	 * 
	 * @return array
	 */
	public function getParams() {
		return($this->_params);
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param string Das SQL-Statement
	 */
	public function __construct($statement) {
		$this->_statement=Curly_Types_Ensure::string($statement);
	}
	
	/**
	 * @return void
	 */
	public function parse() {
		$statementLen=strlen($this->_statement);
		$numericParams=1;
		
		for($charIndex=0; $charIndex<$statementLen; $charIndex++) {
			$char=$this->_statement[$charIndex];
			
			// Nummerischer Parameter
			if($char==self::CHAR_NUMERIC_PARAM) {
				$this->_params[]=array(
					$charIndex,
					$numericParams++
				);
			}
			// Benannter Parameter
			else if($char==self::CHAR_NAMED_PARAM) {
				$start=$charIndex;
				$name=$char;
				
				// Ende des Parameters suchen
				for($charIndex++; $charIndex<$statementLen; $charIndex++) {
					$char=$this->_statement[$charIndex];
					
					// Kein Zeichen im Parameternamen
					if(!(ctype_alnum($char) or $char=='_')) {
						break;
					}
					
					$name.=$char;
				}
				
				$this->_params[]=array(
					$start,
					$name
				);
			}
		}
	}
	
}