<?php

class Test_Curly_Util_Tokens extends Curly_Test {
	
	private $testfile;
	
	protected function setUp() {
		$this->testfile=dirname(__FILE__).DIRECTORY_SEPARATOR.'testfile.php';
	}
	
	public function testFromTokens() {
		$tokens=new Curly_Sourcecode_Php_Tokens();
		$tokens->parseFile($this->testfile);
		
		$util=new Curly_Util_Tokens();
		$util->replaceRequireTokens($tokens, 'req', 'reqo', 'inc', 'inco');
		
		$this->assertEqualsFile($tokens->render());
	}
	
	public function testFromString() {
		$util=new Curly_Util_Tokens();
		$util->setReplaceTokens(array(
			T_REQUIRE		=> 'req',
			T_REQUIRE_ONCE	=> 'reqo',
			T_INCLUDE		=> 'inc',
			T_INCLUDE_ONCE	=> 'inco'
		));
		$this->assertEqualsFile($util->replaceRequireTokens(file_get_contents($this->testfile))->render());
	}
	
	public function testInDir() {
		$util=new Curly_Util_Tokens();
		$util->setReplaceTokens(array(
			T_REQUIRE		=> 'req',
			T_REQUIRE_ONCE	=> 'reqo',
			T_INCLUDE		=> 'inc',
			T_INCLUDE_ONCE	=> 'inco'
		));
		$sourceDir=dirname(__FILE__);
		$targetDir=$sourceDir.DIRECTORY_SEPARATOR.'../replaced';
		$util->replaceRequireTokensInDir($sourceDir, $targetDir);
	}
	
	private function assertEqualsFile($data) {
		$this->assertEqual($data, file_get_contents($this->testfile.'.assert'), 'Falsch ersetzt');
	}
	
}
