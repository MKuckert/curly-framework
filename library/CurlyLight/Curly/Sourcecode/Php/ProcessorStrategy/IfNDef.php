<?php

require_once "Curly/Sourcecode/Php/ProcessorStrategy/IfNDef.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_IfNDef
 * 
 * Implementation eines ifndef-Präprozessors.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
class Curly_Sourcecode_Php_ProcessorStrategy_IfNDef extends Curly_Sourcecode_Php_ProcessorStrategy_IfDef {
	
	const EXPRESSION='ifndef';
	
	/**
	 * Konstruktor
	 */
	public function __construct(Curly_Sourcecode_Php_ProcessorStrategy_DefineCollection $defines) {
		parent::__construct($defines);
		$this->_expression=self::EXPRESSION;
	}
	
	/**
	 * Überprüft, ob die Konstante mit dem übergebenen Namen definiert ist.
	 * 
	 * @return boolean
	 * @param string
	 */
	protected function isConstantDefined($name) {
		return !parent::isConstantDefined($name);
	}
	
}