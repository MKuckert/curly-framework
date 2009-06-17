<?php

class Test_CurlyX_Archive_Parser_Base extends Curly_Test {
	
	/*public function testPlain() {
		$parser=new CurlyX_Archive_Parser_Base();
		$parser->parse(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'plain.archive');
		
		foreach($parser->getEntryNames() as $name) {
			echo $name.' => '.var_export($parser->load($name), true).PHP_EOL;
		}
	}*/
	
	/*public function testCrypt() {
		$parser=new CurlyX_Archive_Parser_Base();
		$parser->parse(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'crypt.archive');
		
		echo var_export($parser->load('fooo'), true).PHP_EOL;
		echo var_export($parser->load('Base.php'), true).PHP_EOL;
	}*/
	
	/*public function testCompress() {
		$parser=new CurlyX_Archive_Parser_Base();
		$parser->parse(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'compress.archive');
		
		echo var_export($parser->load('fooo'), true).PHP_EOL;
		echo var_export($parser->load('Base.php'), true).PHP_EOL;
	}*/
	
	/*public function testChainPlain() {
		$parser=new CurlyX_Archive_Parser_Base();
		$parser->parse(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'chainplain.archive');
		
		echo var_export($parser->load('fooo'), true).PHP_EOL;
		echo var_export($parser->load('Base.php'), true).PHP_EOL;
	}*/
	
	/*public function testChainCompress() {
		$parser=new CurlyX_Archive_Parser_Base();
		$parser->parse(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'chaincompress.archive');
		
		echo var_export($parser->load('fooo'), true).PHP_EOL;
		echo var_export($parser->load('Base.php'), true).PHP_EOL;
	}*/
	
	/*public function testChainCrypt() {
		$parser=new CurlyX_Archive_Parser_Base();
		$parser->parse(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'chaincrypt.archive');
		
		echo var_export($parser->load('fooo'), true).PHP_EOL;
		echo var_export($parser->load('Base.php'), true).PHP_EOL;
	}*/
	
	public function testChainCryptCompress() {
		$parser=new CurlyX_Archive_Parser_Base();
		$parser->parse(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'chaincryptcompress.archive');
		
		echo var_export($parser->load('fooo'), true).PHP_EOL;
		echo var_export($parser->load('Base.php'), true).PHP_EOL;
	}
	
	/*public function testChainCryptCrypt() {
		$parser=new CurlyX_Archive_Parser_Base();
		$parser->parse(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'chaincryptcrypt.archive');
		
		echo var_export($parser->load('fooo'), true).PHP_EOL;
		//echo var_export($parser->load('Base.php'), true).PHP_EOL;
	}*/
	
}