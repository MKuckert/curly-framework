<?php

class Test_Curly_Sourcecode_Php_Processor extends Curly_Test {
	
	private $basedir;
	private $tokens;
	
	protected function setUp() {
		$this->basedir=dirname(__FILE__).DIRECTORY_SEPARATOR;
	}
	
	protected function getProcessor() {
		$file=$this->basedir.'test.php';
		
		$this->tokens=new Curly_Sourcecode_Php_Tokens();
		$this->tokens->parseFile($file);
		
		$processor=new Curly_Sourcecode_Processor();
		$processor->setTokens($this->tokens);
		return $processor;
	}
	
	public function testInclude() {
		$processor=$this->getProcessor();
		
		$include=new Curly_Sourcecode_Php_ProcessorStrategy_Include();
		$include->addIncludePath($this->basedir);
		$processor->registerStrategy(T_COMMENT, $include);
		
		$processor->process();
		
		$this->tokens->renderFile($this->basedir.'include-test.php');
	}
	
	public function testDefine() {
		$processor=$this->getProcessor();
		
		Curly_Sourcecode_Php_ProcessorStrategy_DefineUtil::registerAt($processor);
		
		$processor->process();
		
		$this->tokens->renderFile($this->basedir.'define-test.php');
	}
	
	public function testIf() {
		$processor=$this->getProcessor();
		
		$if=new Curly_Sourcecode_Php_ProcessorStrategy_If();
		$if->getValues()->mergeWith(array(
			'USE_NS'	=> 1,
			'TARGET_OS'	=> 'MACOSX'
		));
		$processor->registerStrategy(T_COMMENT, $if);
		
		$processor->process();
		
		$this->tokens->renderFile($this->basedir.'if-test.php');
	}
	
	public function testFull() {
		$processor=$this->getProcessor();
		
		$created=Curly_Sourcecode_Php_ProcessorStrategy_DefineUtil::registerAt($processor);
		
		$ref=$created['define'];
		$if=new Curly_Sourcecode_Php_ProcessorStrategy_If($ref->getDefines());
		$if->setExpressionEvaluator($ref->getExpressionEvaluator());
		$processor->registerStrategy(T_COMMENT, $if);
		
		$include=new Curly_Sourcecode_Php_ProcessorStrategy_Include();
		$include->addIncludePath($this->basedir);
		$processor->registerStrategy(T_COMMENT, $include);
		
		$processor->process();
		
		$this->tokens->renderFile($this->basedir.'full-test.php');
	}
	
}