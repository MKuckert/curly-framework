<?php

class Test_CurlyX_Sourcecode_Php_Formatter extends Curly_Test {

	private $sourceDir='';
	private $targetDir='';
	private $imlibDir='';

	protected function setUp() {
		$base=dirname(__FILE__);
		$this->sourceDir=$base.'/__format_testdata/';
		$this->targetDir=$base.'/__format_testdata/target/';
		$this->imlibDir=$base.'/../../../../IMLib';
	}

	public function testFullDir() {
		foreach(glob($this->sourceDir.'*.php') as $file) {
			$input=$file;
			$output=$this->targetDir.basename($file);

			$formatter=new CurlyX_Sourcecode_Php_Formatter();
			$formatter->parse(file_get_contents($input))->renderFile($output);
		}
	}

	public function testZendFW() {
		$iterator=new Curly_Types_Iterator_NegateRegex(
			new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(
					$this->imlibDir
				),
				RecursiveIteratorIterator::SELF_FIRST
			),
			Curly_FrameworkBundler::IGNORE_REGEX,
			RegexIterator::MATCH,
			RegexIterator::USE_KEY
		);

		foreach($iterator as $item) {
			$path=$item->getPathName();

			$formatter=new CurlyX_Sourcecode_Php_Formatter();
			$formatter->parse(file_get_contents($filename));

			$relPath=substr($path, $basepathLen);
			$archive->addFile($path, $relPath);
		}
	}

}