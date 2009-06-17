<?php

class Test_Curly_Loader extends Curly_Test {
	
	private $basepath='';
	
	protected function setUp() {
		$this->basepath=dirname(__FILE__).DIRECTORY_SEPARATOR.'__loader_testdata';
	}
	
	protected function _normalize($path) {
		return(str_replace('/', DIRECTORY_SEPARATOR, $path));
	}
	
	public function testNamespace() {
		$loader=new Curly_Loader_Namespace($this->basepath);
		$this->assertEqual($this->_normalize($this->basepath.'/foo/bar/asd.php'), $loader->getPath('foo_bar_asd'), 'Falschen Pfad ermittelt');
	}
	
	public function testIncludePath() {
		$loader=new Curly_Loader_IncludePath();
		$loader->addIncludePath($this->basepath);
		$this->assertEqual($this->_normalize($this->basepath.'/foooo.php'), $loader->getPath('foooo'), 'Falschen Pfad ermittelt');
	}
	
	public function testList() {
		$loader=new Curly_Loader_List();
		$path=$this->basepath.DIRECTORY_SEPARATOR.'foo/bar/asd.php';
		$loader->addEntry('foo_bar_asd', $path);
		$this->assertEqual($path, $loader->getPath('foo_bar_asd'), 'Falschen Pfad ermittelt');
	}
	
	public function testPathList() {
		$incl=$this->_normalize('foo/bar');
		
		$loader=new Curly_Loader_PathList();
		$loader->addIncludePath($incl);
		$loader->setBasePath($this->basepath);
		
		$this->assertEqual($this->basepath.DIRECTORY_SEPARATOR.$incl.DIRECTORY_SEPARATOR.'asd.php', $loader->getPath('asd'), 'Falschen Pfad ermittelt');
	}
	
	public function testRecursive() {
		$loader=new Curly_Loader_Recursive();
		$loader->setBasePath($this->basepath);
		
		$this->assertEqual($this->_normalize($this->basepath.'/foo/bar/asd.php'), $loader->getPath('asd'), 'Falschen Pfad ermittelt');
	}
	
	public function testDispatcher() {
		$CurlyDir=dirname(Curly_Version::getPath());
		
		$dispatcher=new Curly_Loader_Dispatcher();
		
		$loader=new Curly_Loader_Namespace();
		$loader->setBasePath($CurlyDir);
		$dispatcher->registerLoader($loader);
		
		$path=$this->_normalize($CurlyDir.'/Curly/Bootstrap/Section/Auth.php');
		$found=$dispatcher->getPath('Curly_Bootstrap_Section_Auth');
		
		$this->assertEqual($path, $found, 'Falschen Pfad ermittelt');
	}
	
}