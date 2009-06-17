<?php

class Test_CurlyX_Archive_Compiler_Zip extends Curly_Test {
	
	public function testMain() {
		$compiler=new CurlyX_Archive_Compiler_Zip();
		$compiler->addDataset(new CurlyX_Archive_Dataset_String('fooo', 'baaar'));
		$compiler->addDataset(new CurlyX_Archive_Dataset_File(__FILE__, false));
		$compiler->compile(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'zip-archive.zip');
	}
	
}