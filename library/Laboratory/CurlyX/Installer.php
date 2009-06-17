<?php

require_once "CurlyX/Manifest.php";
require_once "CurlyX/Archive/Parser/Registry.php";
require_once "CurlyX/Archive/Parser/Interface.php";
require_once "CurlyX/Archive/Exception.php";

/**
 * CurlyX_Installer
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX
 * @since 1.0 - 18.12.2008
 * @version 1.0
 */
class CurlyX_Installer {

	/**
	 * @desc Name des Manifest-Eintrages im Installer-Archiv
	 */
	const MANIFEST_NAME='MANIFEST';

	/**
	 * @var CurlyX_Archive_Parser_Interface Geöffnetes Installer-Archiv
	 */
	private $_archive=NULL;

	/**
	 * @var CurlyX_Manifest Der Manifest-Eintrag zu diesem Archive
	 */
	private $_manifest=NULL;

	/**
	 * @var string Das zu verwendende Installationsverzeichnis
	 */
	private $_targetDir='.';
	
	/**
	 * @var integer Mode für die mkdir-Funktion in der
	 * {@link ensureDirectory}-Methode.
	 */
	private $_mkdirMode=0777;

	/**
	 * Gibt die Manifest-Instanz zurück.
	 *
	 * @return CurlyX_Installer_Manifest
	 */
	public function getManifest() {
		return($this->_manifest);
	}

	/**
	 * Setzt die Manifest-Instanz.
	 *
	 * @return void
	 * @param CurlyX_Manifest
	 */
	protected function setManifest(CurlyX_Manifest $manifest) {
		$this->_manifest=$manifest;
	}

	/**
	 * Gibt das zu verwendende Installationsverzeichnis zurück.
	 *
	 * @return string
	 */
	public function getTargetDir() {
		return($this->_targetDir);
	}

	/**
	 * Setzt das zu verwendende Installationsverzeichnis.
	 *
	 * @throws CurlyX_Installer_Exception Wenn das übergebene Verzeichnis nicht
	 * 	vorhanden oder nicht schreibbar ist.
	 * @return CurlyX_Installer
	 * @param string
	 */
	public function setTargetDir($value) {
		if(!is_dir($value)) {
			require_once "CurlyX/Installer/Exception.php";
			throw new CurlyX_Installer_Exception('Unable to find the directory '.$value);
		}
		else if(!is_writable($value)) {
			require_once "CurlyX/Installer/Exception.php";
			throw new CurlyX_Installer_Exception('The directory '.$value.' is not writable');
		}

		$this->_targetDir=$value;
		return($this);
	}
	
	/**
	 * Gibt den Mode für die mkdir-Funktion in der
	 * {@link ensureDirectory}-Methode zurück.
	 * 
	 * @return integer
	 */
	public function getMkdirMode() {
		return($this->_mkdirMode);
	}
	
	/**
	 * Setzt den Mode für die mkdir-Funktion in der
	 * {@link ensureDirectory}-Methode.
	 * 
	 * @return void
	 * @param integer
	 */
	public function setMkdirMode($value) {
		$this->_mkdirMode=$value;
	}

	/**
	 * @throws CurlyX_Installer_Exception Wenn ein Fehler auftritt
	 * @return boolean
	 * @param string Dateiname
	 */
	public function install($file) {
		if(!is_file($file)) {
			require_once "CurlyX/Installer/Exception.php";
			throw new CurlyX_Installer_Exception('Unable to find the file '.$file);
		}
		else if(!is_readable($file)) {
			require_once "CurlyX/Installer/Exception.php";
			throw new CurlyX_Installer_Exception('The file '.$file.' is not readable');
		}

		// Installationsverzeichnis nicht vorhanden
		if(!is_dir($this->getTargetDir())) {
			require_once "CurlyX/Installer/Exception.php";
			throw new CurlyX_Installer_Exception('Unable to find directory '.$this->getTargetDir());
		}

		$this->openArchive($file);

		$this->readManifest();

		$this->doInstallation();
	}

	/**
	 * Vollzieht die eigentliche Installation
	 *
	 * @throws CurlyX_Installer_Exception Wenn ein Fehler auftritt
	 * @return void
	 */
	protected function doInstallation() {
		$this->installFiles();
	}

	/**
	 * Öffnet die Datei.
	 *
	 * @throws CurlyX_Installer_Exception Wenn ein Fehler auftritt
	 * @return void
	 * @param string
	 */
	protected function openArchive($file) {
		try {
			$class=CurlyX_Archive_Parser_Registry::findForFile($file);
			$this->_archive=new $class();
			
			if(!$this->_archive->parse($file)) {
				require_once "CurlyX/Installer/Exception.php";
				throw new CurlyX_Installer_Exception('Unable to parse the archive file '.$file, $res);
			}
		}
		catch(CurlyX_Archive_Exception $ex) {
			require_once "CurlyX/Installer/Exception.php";
			throw new CurlyX_Installer_Exception($ex->getMessage());
		}
	}

	/**
	 * Ließt die Manifest-Daten aus dem Installer-Archiv.
	 *
	 * @throws CurlyX_Installer_Exception Wenn ein Fehler auftritt
	 * @return void
	 */
	protected function readManifest() {
		// Manifesteintrag holen
		try {
			$manifestContent=$this->_archive->load(self::MANIFEST_NAME);
		}
		catch(CurlyX_Archive_Exception $ex) {
			require_once "CurlyX/Installer/Exception.php";
			throw new CurlyX_Installer_Exception($ex->getMessage());
		}

		$manifest=new CurlyX_Manifest();
		if(!$manifest->loadString($manifestContent)) {
			require_once "CurlyX/Installer/Exception.php";
			throw new CurlyX_Installer_Exception('Unable to load the manifest entry');
		}
		$this->setManifest($manifest);
	}

	/**
	 * Installiert die Dateien auf dem Installer-Archiv im Dateisystem.
	 *
	 * @throws CurlyX_Installer_Exception Wenn ein Fehler auftritt
	 * @return void
	 */
	protected function installFiles() {
		$manifest=$this->getManifest();
		$files=$manifest->getFileList();

		foreach($files as $file) {
			//'name'
			//'base'
			// Sicherstellen, das Verzeichnis vorhanden ist
			$this->ensureDirectory($file['base']);

			// Dateiinhalt über kompletten Pfad laden
			try {
				$contents=$this->_archive->load($file['base'].'/'.$file['name']);
			}
			catch(CurlyX_Archive_Exception $ex) {
				$contents=NULL;
			}
				
			// Dateiinhalt über Dateinamen direkt laden
			if($contents===NULL) {
				try {
					$contents=$this->_archive->load($file['name']);
				}
				catch(CurlyX_Archive_Exception $ex) {
					$contents=NULL;
				}
			}

			// Aufgeben
			if($contents===NULL) {
				require_once "CurlyX/Installer/Exception.php";
				throw new CurlyX_Installer_Exception('Unable to load the contents of the file '.$file['name']);
			}

			// Datei schreiben
			$path=$this->getTargetDir().DIRECTORY_SEPARATOR.$file['base'].DIRECTORY_SEPARATOR.$file['name'];
			if(file_put_contents($path, $contents)===false) {
				require_once "CurlyX/Installer/Exception.php";
				throw new CurlyX_Installer_Exception('Unable to write the contents of the file '.$file['name']);
			}
		}
	}

	/**
	 * Stellt sicher, dass das Installations-Unterverzeichnis mit dem
	 * übergebenen Pfad vorhanden ist.
	 *
	 * @throws CurlyX_Installer_Exception Wenn ein Fehler auftritt
	 * @return void
	 * @param string
	 */
	protected function ensureDirectory($subdir) {
		$path=$this->getTargetDir().DIRECTORY_SEPARATOR.$subdir;
		if(!is_dir($path)) {
			if(!mkdir($path, $this->getMkdirMode(), true)) {
				require_once "CurlyX/Installer/Exception.php";
				throw new CurlyX_Installer_Exception('Unable to create the installation directory '.$path);
			}
		}
	}

}
