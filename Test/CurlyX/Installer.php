<?php

class Test_CurlyX_Installer extends Curly_Test {
	
	private $filename;
	
	protected function setUp() {
		$this->filename=dirname(__FILE__).DIRECTORY_SEPARATOR.'test-install.zip';
	}
	
	public function testInstall() {
		CurlyX_Archive_Parser_Registry::register('CurlyX_Archive_Parser_Base');
		CurlyX_Archive_Parser_Registry::register('CurlyX_Archive_Parser_Zip');
		
		$installer=new CurlyX_Installer();
		$installer->setTargetDir(dirname(__FILE__).DIRECTORY_SEPARATOR.'InstallationDirectory');
		$installer->install($this->filename);
	}
	
}