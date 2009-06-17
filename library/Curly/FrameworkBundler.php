<?php

require_once "Curly/Types/Iterator/NegateRegex.php";

/**
 * @since 28.10.2008
 */
class Curly_FrameworkBundler {
	
	const VERSION='0.1';
	const IGNORE_REGEX='~(^|/|\\\\)(\\.svn|CVS)(/|\\\\|$)~i';
	
	/**
	 * @var array
	 */
	private $_wantedPackages=array();
	
	/**
	 * @var array
	 */
	private $_packageDependencies=array();
	
	/**
	 * @var string
	 */
	private $_archiveFile='Bundle.zip';
	
	/**
	 * @var string
	 */
	private $_basePath;
	
	/**
	 * @var boolean
	 */
	private $_addSoftDependencies=false;
	
	/**
	 * @var string
	 */
	private $_ignoreRegex=self::IGNORE_REGEX;
	
	/**
	 * @return array
	 */
	public function getWantedPackages() {
		return($this->_wantedPackages);
	}
	
	/**
	 * @return void
	 * @param array
	 */
	public function setWantedPackages(array $value) {
		$this->_wantedPackages=$value;
	}
	
	/**
	 * @return array
	 */
	public function getPackageDependencies() {
		return($this->_packageDependencies);
	}
	
	/**
	 * @return void
	 * @param array
	 */
	public function setPackageDependencies(array $value) {
		$this->_packageDependencies=$value;
	}
	
	/**
	 * @return array
	 */
	public function getPackages() {
		return(array_keys($this->_packageDependencies));
	}
	
	/**
	 * @return string
	 */
	public function getArchiveFile() {
		return($this->_archiveFile);
	}
	
	/**
	 * @return void
	 * @param string
	 */
	public function setArchiveFile($value) {
		$this->_archiveFile=(string)$value;
	}
	
	/**
	 * @return string
	 */
	public function getBasePath() {
		return($this->_basePath);
	}
	
	/**
	 * @return void
	 * @param string
	 */
	public function setBasePath($value) {
		$this->_basePath=(string)$value;
	}
	
	/**
	 * @return boolean
	 */
	public function getAddSoftDependencies() {
		return($this->_addSoftDependencies);
	}
	
	/**
	 * @return void
	 * @param boolean
	 */
	public function setAddSoftDependencies($value) {
		$this->_addSoftDependencies=(boolean)$value;
	}
	
	/**
	 * @return string
	 */
	public function getIgnoreRegex() {
		return($this->_ignoreRegex);
	}
	
	/**
	 * @return void
	 * @param string
	 */
	public function setIgnoreRegex($value) {
		$this->_ignoreRegex=(string)$value;
	}
	
	/**
	 * @return void
	 */
	public function bundle() {
		// Abhängigkeiten auflösen
		for($i=0; $i<count($this->_wantedPackages); $i++) {
			$wanted=$this->_wantedPackages[$i];
			if(isset($this->_packageDependencies[$wanted])) {
				// Harte Abhängigkeiten hinzufügen
				$this->addPackages($this->_packageDependencies[$wanted][0]);
				
				// Weiche Abhängigkeiten hinzufügen
				if($this->_addSoftDependencies and isset($this->_packageDependencies[$wanted][1])) {
					$this->addPackages($this->_packageDependencies[$wanted][1]);
				}
			}
		}
		
		$this->createZipArchive();
	}
	
	/**
	 * @return void
	 * @param string
	 */
	private function addPackages($source) {
		foreach($source as $item) {
			if(!in_array($item, $this->_wantedPackages)) {
				$this->_wantedPackages[]=$item;
			}
		}
	}
	
	/**
	 * @return void
	 */
	private function createZipArchive() {
		$archive=new ZipArchive();
		
		$archive->open($this->_archiveFile, ZipArchive::CREATE|ZipArchive::OVERWRITE);
		foreach($this->_wantedPackages as $wanted) {
			$this->addPackageToArchive($archive, $wanted);
			
			// Archiv schließen und öffnen (http://pecl.php.net/bugs/bug.php?id=9443)
			$archive->close();
			$archive->open($this->_archiveFile);
		}
		
		$archive->setArchiveComment($this->getArchiveComment());
		$archive->close();
	}
	
	/**
	 * @return void
	 * @param ZipArchive
	 * @param string
	 */
	private function addPackageToArchive(ZipArchive $archive, $package) {
		$package=str_replace('_', DIRECTORY_SEPARATOR, $package);
		
		$basepath=$this->_basePath.DIRECTORY_SEPARATOR;
		$basepathLen=strlen($basepath);
		
		// Verzeichnisse hinzufügen
		if(is_dir($basepath.$package)) {
			// Verzeichnisiterator erstellen
			$iterator=new Curly_Types_Iterator_NegateRegex(
				new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator(
						$basepath.$package
					),
					RecursiveIteratorIterator::SELF_FIRST
				),
				$this->_ignoreRegex,
				RegexIterator::MATCH,
				RegexIterator::USE_KEY
			);
			
			foreach($iterator as $item) {
				$path=$item->getPathName();
				$relPath=substr($path, $basepathLen);
				$archive->addFile($path, $relPath);
			}
		}
		
		// Hauptdatei hinzufügen
		$file=$basepath.$package.'.php';
		if(is_file($file)) {
			$archive->addFile($file, $package.'.php');
		}
	}
	
	/**
	 * @return string
	 */
	private function getArchiveComment() {
		$out="This file was created using the Curly FrameworkBundler v".self::VERSION."\n"
			."(c)2008 Martin Kuckert\n\n"
			."This is a bundle of the following packages:\n";
		foreach($this->_wantedPackages as $wanted) {
			$out.=" - ".$wanted."\n";
		}
		return($out);
	}
	
}

?>