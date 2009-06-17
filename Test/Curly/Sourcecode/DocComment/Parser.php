<?php

require_once "Curly/Sourcecode/DocComment/Parser.php";
require_once "Curly/Sourcecode/Php/Tokens.php";

class Test_Curly_Sourcecode_DocComment_Parser extends Curly_Test {
	
	private $parser;
	
	protected function setUp() {
		$this->parser=new Curly_Sourcecode_DocComment_Parser();
	}
	
	/**
	 * Testet die parse-Methode
	 *     der Curly_Sourcecode_DocComment_Parser-Klasse
	 * 
	 * @return void
	 * @desc auf einfache Weise
	 * @foo bar
	 * @foo bar2
	 */
	public function testParse() {
		$method=new ReflectionMethod(__CLASS__, __FUNCTION__);
		
		$tags=$this->parser->parse($method->getDocComment());
		var_dump($tags);
		
	}
	
	/**
	 * Testet die parseAll-Methode der Parser-Klasse
	 */
	public function testParseAll() {
		$tokens=new Curly_Sourcecode_Php_Tokens();
		$tokens->parseFile(__FILE__);
		
		$tags=$this->parser->parseAll($tokens->getTokensOfType(T_DOC_COMMENT));
		var_dump($tags);
	}
	
}