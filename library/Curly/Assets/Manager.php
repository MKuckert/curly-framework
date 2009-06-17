<?php

/**
 * Curly_Assets_Manager
 * 
 * Stellt einen Manager für Dateianhänge in Websites dar.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Assets
 * @since 1.0 - 26.11.2008
 * @version 1.0
 */
class Curly_Assets_Manager {
	
	/**#@+
	 * Standardwerte
	 */
	const DEFAULT_BASEDIR='assets';
	const DEFAULT_BASEURL='assets';
	const DEFAULT_GENDIR='generic';
	/**#@-*/
	
	/**
	 * @desc Regulärer Ausdruck zum Ignorieren von Dateimustern beim Kopieren
	 * von Verzeichnissen.
	 */
	const IGNORE_REGEX='~(^|/|\\\\)(\\.svn|CVS)(/|\\\\|$)~i';
	
	/**
	 * @var string Basisverzeichnis für Assets
	 */
	private $_basedir;
	
	/**
	 * @var string Basis-URL für Assets
	 */
	private $_baseurl;
	
	/**
	 * @var boolean Flag, ob der letzte Modifikationszeitpunkt der Dateien
	 *  verglichen werden soll, um immer die aktuellste Datei zu exportieren.
	 */
	private $_checkmtime=true;

	/**
	 * @var Curly_Hash_Interface Die zu verwendende Hash-Implementation.
	 */
	private $_hash=NULL;
	
	/**
	 * @var string Regulärer Ausdruck zum Ignorieren von Dateimustern beim
	 * Kopieren von Verzeichnissen.
	 */
	private $_ignoreRegex=self::IGNORE_REGEX;
	
	/**
	 * @var integer UMask für Asset-Dateien.
	 */
	private $_umask=0777;
	
	/**
	 * @var string Dateiverzeichnis für generische Dateien.
	 */
	private $_genericDirectory=self::DEFAULT_GENDIR;
	
	/**
	 * @var array Liste bereits bekannter Asset-URLs.
	 */
	protected $assetUrls=array();
	
	/**
	 * Gibt das Basisverzeichnis für Assets zurück,
	 * 
	 * @return string
	 */
	public function getBaseDir() {
		return($this->_basedir);
	}
	
	/**
	 * Setzt das Basisverzeichnis für Assets,
	 * 
	 * @return void
	 * @param string
	 */
	public function setBaseDir($value) {
		$this->_basedir=Curly_Types_Ensure::string($value);
	}
	
	/**
	 * Gibt die Basis-URL für Assets zurück,
	 * 
	 * @return string
	 */
	public function getBaseUrl() {
		return($this->_baseurl);
	}
	
	/**
	 * Setzt die Basis-URL für Assets,
	 * 
	 * @return void
	 * @param string
	 */
	public function setBaseUrl($value) {
		$this->_baseurl=Curly_Types_Ensure::string($value);
	}
	
	/**
	 * @return boolean Gibt das Flag, ob der letzte Modifikationszeitpunkt der
	 *  Dateien verglichen werden soll, um immer die aktuellste Datei zu
	 *  exportieren, zurück.
	 */
	public function getCheckModificationTime() {
		return($this->_checkmtime);
	}
	
	/**
	 * @return void
	 * @param boolean Setzt das Flag, ob der letzte Modifikationszeitpunkt der
	 *  Dateien verglichen werden soll, um immer die aktuellste Datei zu
	 *  exportieren.
	 */
	public function setCheckModificationTime($value) {
		$this->_checkmtime=$value;
	}

	/**
	 * Gibt die zu verwendende Hash-Implementation zurück.
	 *
	 * @return Curly_Hash_Interface
	 */
	public function getHash() {
		if($this->_hash===NULL) {
			$this->_hash=new Curly_Hash_Crc32();
		}
		return $this->_hash;
	}

	/**
	 * Setzt die zu verwendende Hash-Implementation.
	 *
	 * @return void
	 * @param Curly_Hash_Interface
	 */
	public function setHash(Curly_Hash_Interface $value) {
		$this->_hash=$value;
	}
	
	/**
	 * Gibt den regulären Ausdruck zum Ignorieren von Dateimustern beim
	 * Kopieren von Verzeichnissen zurück.
	 * 
	 * @return string
	 */
	public function getIgnoreRegex() {
		return $this->_ignoreRegex;
	}
	
	/**
	 * Setzt den regulären Ausdruck zum Ignorieren von Dateimustern beim
	 * Kopieren von Verzeichnissen.
	 * 
	 * @return void
	 * @param string
	 */
	public function setIgnoreRegex($value) {
		$this->_ignoreRegex=Curly_Types_Ensure::string($value);
	}
	
	/**
	 * Gibt die UMask für Asset-Dateien zurück.
	 * 
	 * @return integer
	 */
	public function getUMask() {
		return $this->_umask;
	}
	
	/**
	 * Setzt die UMask für Asset-Dateien.
	 * 
	 * @return void
	 * @param integer
	 */
	public function setUMask($value) {
		$this->_umask=Curly_Types_Ensure::int($value);
	}
	
	/**
	 * Gibt das Dateiverzeichnis für generische Dateien zurück.
	 * 
	 * @return string
	 */
	public function getGenericDirectory() {
		return $this->_genericDirectory;
	}
	
	/**
	 * Setzt das Dateiverzeichnis für generische Dateien.
	 * 
	 * @return void
	 * @param string
	 */
	public function setGenericDirectory($value) {
		$this->_genericDirectory=Curly_Types_Ensure::string($value);
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param string Basisverzeichnis für Assets
	 * @param string Basis-URL für Assets
	 * @param boolean Flag, ob der letzte Modifikationszeitpunkt der Dateien
	 *  verglichen werden soll.
	 */
	public function __construct($basedir=self::DEFAULT_BASEDIR, $baseurl=self::DEFAULT_BASEURL, $checkmtime=true) {
		$this->setBaseDir($basedir);
		$this->setBaseUrl($baseurl);
		$this->setCheckModificationTime($checkmtime);
	}

	/**
	 * Stellt sicher, dass der übergebene Dateiname gültig ist und wirft im
	 * Fehlerfall eine Ausnahme.
	 *
	 * @throws Curly_Assets_Exception
	 * @return void
	 * @param string
	 */
	protected function ensureFile($file) {
		// Datei nicht vorhanden
		if(!is_file($file)) {
			require_once "Curly/Assets/Exception.php";
			throw new Curly_Assets_Exception('File '.$file.' not found');
		}
		// Datei nicht lesbar
		if(!is_readable($file)) {
			require_once "Curly/Assets/Exception.php";
			throw new Curly_Assets_Exception('File '.$file.' is not readable');
		}
	}

	/**
	 * Stellt sicher, dass der übergebene Verzeichnisname gültig ist und wirft
	 * im Fehlerfall eine Ausnahme.
	 *
	 * @throws Curly_Assets_Exception
	 * @return void
	 * @param string
	 */
	protected function ensureDirectory($dir) {
		// Verzeichnis nicht vorhanden
		if(!is_dir($dir)) {
			require_once "Curly/Assets/Exception.php";
			throw new Curly_Assets_Exception('Directory '.$dir.' not found');
		}
		// Verzeichnis nicht lesbar
		if(!is_readable($dir)) {
			require_once "Curly/Assets/Exception.php";
			throw new Curly_Assets_Exception('Directory '.$dir.' is not readable');
		}
	}
	
	/**
	 * Erstellt eine neue Datei mit dem übergebenen Namen als Asset und gibt
	 * ein Array zurück, in dem die Asset-URL (Arraykey "url") und der
	 * komplette Dateipfad (Arraykey "file") enthalten sind.
	 * Zusätzlich im Schlüssel "mtime" die letzte Änderung der Datei
	 * abgelegt. Im Schlüssel "created" wird ein boolscher Wert abgelegt, ob
	 * die Datei neu angelegt wurde (true) oder bereits vorhanden war (false).
	 * 
	 * @return array
	 * @param string Dateiname
	 */
	public function createFile($file) {
		$hash=$this->getGenericDirectory();
		
		$retval=array(
			'url'	=> $this->getAssetUrl($hash, $file),
			'file'	=> $this->getAssetDir($hash, $file)
		);
		
		if(is_file($retval['file'])) {
			$retval['mtime']=filemtime($retval['file']);
			$retval['created']=false;
		}
		else {
			$mtime=time();
			if(!touch($retval['file'], $mtime)) {
				require_once "Curly/Assets/Exception.php";
				throw new Curly_Assets_Exception('Unable to create generic file '.$file.' in the asset directory');
			}
			
			chmod($retval['file'], $this->getUMask());
			$retval['mtime']=$mtime;
			$retval['created']=true;
		}
		$retval['url'].='?_m='.$retval['mtime'];
		
		return $retval;
	}
	
	/**
	 * Veröffentlicht die Datei mit dem übergebenen Dateinamen.
	 * 
	 * @return string Die URL, um auf die Datei zugreifen zu können.
	 * @param string Dateipfad
	 */
	public function publishFile($file) {
		if(isset($this->assetUrls[$file])) {
			return $this->assetUrls[$file];
		}
		
		$this->ensureFile($file);
		
		// Verzeichnisnamen-Hash bilden
		$hash=$this->hash(dirname($file));

		// URL erstellen
		$assetUrl=$this->getFileAssetUrl($hash, $file);
		
		// Überprüfen, ob Datei bereits veröffentlicht wurde
		$assetPath=$this->getAssetDir($hash, $file);
		
		// Datei bereits veröffentlicht
		if(is_file($assetPath)) {
			// Veröffentlichte Datei ist neuer als aktuelle Datei oder soll nicht ersetzt werden
			if(filemtime($assetPath)>filemtime($file) or !$this->getCheckModificationTime()) {
				// Datei nicht neu veröffentlichen
				$this->assetUrls[$file]=$assetUrl;
				return $assetUrl;
			}
		}

		$this->copyFile($file, $assetPath);
		$this->assetUrls[$file]=$assetUrl;
		return $assetUrl;
	}
	
	/**
	 * Veröffentlicht das unter dem übergebenen Pfad vorhandene Verzeichnis.
	 * 
	 * @return string Die URL, um auf das Verzeichnis zugreifen zu können.
	 * @param string Verzeichnispfad
	 */
	public function publishDirectory($dir) {
		if(isset($this->assetUrls[$dir])) {
			return $this->assetUrls[$dir];
		}
		
		$this->ensureDirectory($dir);
		
		// Verzeichnisnamen-Hash bilden
		$hash=$this->hash($dir);

		// URL erstellen
		$assetUrl=$this->getAssetUrl($hash, $dir);
		
		// Überprüfen, ob Verzeichnis bereits veröffentlicht wurde
		$assetPath=$this->getAssetDir($hash, $dir);
		
		// Inhalt ist bereits veröffentlicht
		if(is_dir($assetPath)) {
			// Veröffentlichtes Verzeichnis ist neuer als aktuelles Verzeichnis oder soll nicht ersetzt werden
			if(!$this->getCheckModificationTime()) {
				$this->assetUrls[$dir]=$assetUrl;
				return $assetUrl;	
			}
		}
		
		// Verzeichnisinhalt veröffentlichen
		$this->copyDirectory($dir, $assetPath);
		$this->assetUrls[$dir]=$assetUrl;
		return $assetUrl;
	}
	
	/**
	 * Bildet den Hash zu einem Verzeichnisnamen und gibt diesen zurück.
	 * 
	 * @return string
	 * @param string
	 */
	protected function hash($dirname) {
		return $this->getHash()
			->hash($dirname);
	}

	/**
	 * Gibt die Url zu dem übergebenen Pfad zurück, unter dem dieser nach der
	 * Veröffentlichung als Asset erreichbar ist.
	 *
	 * @return string
	 * @param string Hash des Assets
	 * @param string Dateipfad des Assets.
	 */
	protected function getAssetUrl($hash, $file) {
		return $this->getBaseUrl().'/'.$hash.'/'.basename($file);
	}

	/**
	 * Gibt die Url zu der übergebenen Datei zurück.
	 *
	 * @return string
	 * @param string Hash des Assets
	 * @param string Dateipfad des Assets.
	 */
	protected function getFileAssetUrl($hash, $file) {
		return $this->getAssetUrl($hash, $file).'?_m='.filemtime($file);
	}
	
	/**
	 * Gibt den Pfad zurück, unter dem die Datei mit dem übergebenen Pfad als
	 * Asset veröffentlicht werden soll.
	 *
	 * @throws Curly_Assets_Exception
	 * @return string
	 * @param string Hash des Assets
	 * @param string Dateipfad des Assets.
	 */
	protected function getAssetDir($hash, $file) {
		$dir=$this->getBaseDir().DIRECTORY_SEPARATOR.$hash;
		
		// Verzeichnis nicht vorhanden
		if(!is_dir($dir)) {
			if(!mkdir($dir, 0777, true)) {
				require_once "Curly/Assets/Exception.php";
				throw new Curly_Assets_Exception('Unable to create the asset directory '.$dir);
			}
		}

		return $dir.DIRECTORY_SEPARATOR.basename($file);
	}

	/**
	 * Kopiert die Datei mit dem übergebenen Pfad an die Stelle dessen
	 * Asset-Pfades.
	 *
	 * @throws Curly_Assets_Exception
	 * @return void
	 * @param string Quell-Datei
	 * @param string Asset-Datei
	 */
	protected function copyFile($file, $assetPath) {
		if(!copy($file, $assetPath)) {
			require_once "Curly/Assets/Exception.php";
			throw new Curly_Assets_Exception('Unable to copy file '.$file.' to the asset directory');
		}
		
		chmod($assetPath, $this->getUMask());
	}

	/**
	 * Kopiert die Dateien innerhalb des Verzeichnisses mit dem übergebenen
	 * Pfad an die Stelle dessen Asset-Pfades.
	 *
	 * @throws Curly_Assets_Exception
	 * @return void
	 * @param string Quell-Verzeichnis
	 * @param string Asset-Verzeichnis
	 */
	protected function copyDirectory($dir, $assetPath) {
		$iterator=new Curly_Types_Iterator_NegateRegex(
			new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(
					$dir
				),
				RecursiveIteratorIterator::LEAVES_ONLY
			),
			$this->getIgnoreRegex(),
			RegexIterator::MATCH,
			RegexIterator::USE_KEY
		);
		
		$checkMTime=$this->getCheckModificationTime();
		$umask=$this->getUMask();
		$dirlen=strlen($dir);
		foreach($iterator as $item) {
			$path=$item->getPathName();
			$relPath=substr($path, $dirlen);
			$fullAssetPath=$assetPath.DIRECTORY_SEPARATOR.$relPath;
			$fullAssetDir=dirname($fullAssetPath);
			
			// Verzeichnis anlegen
			if(!is_dir($fullAssetDir)) {
				mkdir($fullAssetDir, $umask, true);
				chmod($fullAssetDir, $umask);
			}
			
			// Datei bereits vorhanden und aktueller
			if(is_file($fullAssetPath) and (
				!$checkMTime or
				filemtime($fullAssetPath)>filemtime($path)
			)) {
				continue;
			}
			
			// Datei kopieren
			if(!copy($path, $fullAssetPath)) {
				require_once "Curly/Assets/Exception.php";
				throw new Curly_Assets_Exception('Unable to copy file '.$path.' to the asset directory');
			}
			
			chmod($fullAssetPath, $umask);
		}
	}
	
}
