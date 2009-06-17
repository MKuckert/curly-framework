<?php

class Test_Curly_Sourcecode_Minimizer_Css extends Curly_Test {
	
	public function testMinimize() {
		$sourceStream=new Curly_Stream(dirname(__FILE__).DIRECTORY_SEPARATOR.'test.css', 'r');
		$targetStream=new Curly_Stream(dirname(__FILE__).DIRECTORY_SEPARATOR.'out.css', 'w');
		
		$minimizer=new Curly_Sourcecode_Minimizer_Css();
		$minimizer->setSourceStream($sourceStream);
		$minimizer->setTargetStream($targetStream);
		$minimizer->minimize();
	}
	
}