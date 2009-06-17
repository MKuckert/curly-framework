<?php

class Test_CurlyX_Archive_Compiler_Base extends Curly_Test {
	
	/*public function testPlain() {
		$compiler=new CurlyX_Archive_Compiler_Base();
		$compiler->addDataset(new CurlyX_Archive_Dataset_String('fooo', 'baaar'));
		$compiler->addDataset(new CurlyX_Archive_Dataset_File(__FILE__, false));
		$compiler->compile(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'plain.archive');
	}*/
	
	/*public function testCrypt() {
		$compiler=new CurlyX_Archive_Compiler_Base();
		$compiler->setAlgorithm(new CurlyX_Archive_Compiler_Base_Algorithm_Crypt($compiler, MCRYPT_BLOWFISH));
		$compiler->addDataset(new CurlyX_Archive_Dataset_String('fooo', 'baaar'));
		$compiler->addDataset(new CurlyX_Archive_Dataset_File(__FILE__, false));
		$compiler->compile(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'crypt.archive');
		
	}*/
	
	/*public function testCompress() {
		$compiler=new CurlyX_Archive_Compiler_Base();
		$compiler->setAlgorithm(new CurlyX_Archive_Compiler_Base_Algorithm_Compress());
		$compiler->addDataset(new CurlyX_Archive_Dataset_String('fooo', 'baaar'));
		$compiler->addDataset(new CurlyX_Archive_Dataset_File(__FILE__, false));
		$compiler->compile(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'compress.archive');
	}*/
	
	public function testChainPlain() {
		$compiler=new CurlyX_Archive_Compiler_Base();
		$compiler->setAlgorithm(new CurlyX_Archive_Compiler_Base_Algorithm_Chain($compiler, array(
			new CurlyX_Archive_Compiler_Base_Algorithm_Plain()
		)));
		$compiler->addDataset(new CurlyX_Archive_Dataset_String('fooo', 'baaar'));
		$compiler->addDataset(new CurlyX_Archive_Dataset_PhpFile(__FILE__, false));
		$compiler->compile(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'chainplain.archive');
	}
	
	public function testChainCompress() {
		$compiler=new CurlyX_Archive_Compiler_Base();
		$compiler->setAlgorithm(new CurlyX_Archive_Compiler_Base_Algorithm_Chain($compiler, array(
			new CurlyX_Archive_Compiler_Base_Algorithm_Compress()
		)));
		$compiler->addDataset(new CurlyX_Archive_Dataset_String('fooo', 'baaar'));
		$compiler->addDataset(new CurlyX_Archive_Dataset_PhpFile(__FILE__, false));
		$compiler->compile(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'chaincompress.archive');
	}
	
	public function testChainCrypt() {
		$compiler=new CurlyX_Archive_Compiler_Base();
		$compiler->setAlgorithm(new CurlyX_Archive_Compiler_Base_Algorithm_Chain($compiler, array(
			new CurlyX_Archive_Compiler_Base_Algorithm_Crypt($compiler, MCRYPT_BLOWFISH)
		)));
		$compiler->addDataset(new CurlyX_Archive_Dataset_String('fooo', 'baaar'));
		$compiler->addDataset(new CurlyX_Archive_Dataset_PhpFile(__FILE__, false));
		$compiler->compile(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'chaincrypt.archive');
	}
	
	public function testChainCryptCompress() {
		$compiler=new CurlyX_Archive_Compiler_Base();
		$compiler->setAlgorithm(new CurlyX_Archive_Compiler_Base_Algorithm_Chain($compiler, array(
			new CurlyX_Archive_Compiler_Base_Algorithm_Compress(),
			new CurlyX_Archive_Compiler_Base_Algorithm_Crypt($compiler, MCRYPT_BLOWFISH)
		)));
		$compiler->addDataset(new CurlyX_Archive_Dataset_String('fooo', 'baaar'));
		$compiler->addDataset(new CurlyX_Archive_Dataset_PhpFile(__FILE__, false));
		$compiler->compile(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'chaincryptcompress.archive');
	}
	
	public function testChainCryptCrypt() {
		$compiler=new CurlyX_Archive_Compiler_Base();
		$compiler->setAlgorithm(new CurlyX_Archive_Compiler_Base_Algorithm_Chain($compiler, array(
			new CurlyX_Archive_Compiler_Base_Algorithm_Crypt($compiler, MCRYPT_BLOWFISH),
			new CurlyX_Archive_Compiler_Base_Algorithm_Crypt($compiler, MCRYPT_RIJNDAEL_256)
		)));
		$compiler->addDataset(new CurlyX_Archive_Dataset_String('fooo', 'baaar'));
		$compiler->addDataset(new CurlyX_Archive_Dataset_PhpFile(__FILE__, false));
		$compiler->compile(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'chaincryptcrypt.archive');
	}
	
}