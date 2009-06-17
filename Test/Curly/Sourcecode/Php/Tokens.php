<?php

class Test_Curly_Sourcecode_Php_Tokens extends Curly_Test {
	
	public function testStripWhitespace() {
		//require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'stripped.php';
		
		$refl=new ReflectionClass('CurlyX_Archive_Dataset_PhpFile');
		
		$file=$refl->getFileName();
		//$file=dirname(__FILE__).DIRECTORY_SEPARATOR.'test.php';
		
		$tokens=new Curly_Sourcecode_Php_Tokens();
		$tokens->parseFile($file);
		$start=microtime(true);
		$tokens->stripWhitespace();
		echo (microtime(true)-$start).'s'.PHP_EOL;
		$tokens->renderFile(dirname(__FILE__).DIRECTORY_SEPARATOR.'stripped.php');
		
		$start=microtime(true);
		$stripped=php_strip_whitespace($file);
		echo (microtime(true)-$start).'s'.PHP_EOL;
		file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'php-stripped.php', $stripped);
	}
	
}