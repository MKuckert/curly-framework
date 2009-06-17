<?php

require_once "Curly/Sourcecode/Php/ProcessorStrategy/Expression.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_Include
 * 
 * Ersetzt einen Token mit einem Include-Befehl im Präfix durch den Inhalt einer
 * innerhalb der IncludePath-Werte referenzierten Datei.
 * 
 * Beispiel für einen Token:
 * <code>
 *  #include path/to/file
 * </code> 
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
class Curly_Sourcecode_Php_ProcessorStrategy_Include extends Curly_Sourcecode_Php_ProcessorStrategy_Expression {
	
	const EXPRESSION='include';
	
	/**
	 * @var array Zu verwendende Include-Pfade
	 */
	private $_includePath=array();
	
	/**
	 * @var array Eine Liste der geparsten Dateien. Der Schlüssel entspricht
	 * dem vollständigen Pfad, der Wert der Anzahl Zugriffe auf diese Datei.
	 */
	protected $_parsedFiles=array();
	
	/**
	 * @var integer Die maximale Tiefe rekursiver Includes.
	 */
	private $_maxRecursionDepth=10;
	
	/**
	 * Gibt die maximale Tiefe rekursiver Includes zurück.
	 * 
	 * @return r
	 */
	public function getMaxRecursionDepth() {
		return $this->_maxRecursionDepth;
	}
	
	/**
	 * Setzt die maximale Tiefe rekursiver Includes.
	 * 
	 * @return void
	 * @param r
	 */
	public function setMaxRecursionDepth($value) {
		$this->_maxRecursionDepth=Curly_Types_Ensure::range($value, 1);
		return $this;
	}
	
	/**
	 * Gibt die zu verwendenden Include-Pfade zurück.
	 * 
	 * @return array
	 */
	public function getIncludePath() {
		return $this->_includePath;
	}
	
	/**
	 * Fügt einen zu verwendenden Include-Pfade an.
	 * 
	 * @return void
	 * @param string
	 */
	public function addIncludePath($path) {
		$this->_includePath[]=$path;
	}
	
	/**
	 * Entfernt einen nicht mehr zu verwendenden Include-Pfade.
	 * 
	 * @return void
	 * @param string
	 */
	public function removeIncludePath($path) {
		$index=array_search($path, $this->_includePath);
		if($index!==false) {
			unset($this->_includePath[$index]);
		}
	}
	
	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->_expression=self::EXPRESSION;
	}
	
	/**
	 * Verarbeitet den übergebenen Token mit den Parametern aus dem Ausdruck.
	 * 
	 * @return void
	 * @param mixed Token
	 * @param string Parameterliste aus der Expression
	 */
	protected function _processToken($token, $parameter) {
		$path=trim($parameter);
		$index=$this->getCurrentIndex();
	
		$path=$this->getPath($path);
		
		// Datei nicht gefunden
		if($path===NULL) {
			$err='Unable to find the file';
		}

		// Datei ist nicht lesbar
		else if(!is_readable($path)) {
			$err='File is not readable';
		}
		
		if(isset($err)) {
			$this->raiseError($err, self::EXPRESSION.' '.$parameter);
			$this->getTokens()->removeTokenAt($index);
			$this->setCurrentIndex($index-1);
			return;
		}
		
		$this->replaceToken($path);
		
		// Ein Token zurück gehen, um die eingefügten Tokens zu evaluieren
		$this->setCurrentIndex($index-1);
	}
	
	/**
	 * Ermittelt den zu verwendenden Dateipfad zu dem übergebenen Pfad.
	 * 
	 * @return string oder NULL wenn nicht ermittelbar.
	 * @param string
	 */
	protected function getPath($path) {
		// Pfad liegt nicht im aktuellen Arbeitsverzeichnis
		if(!is_file($path)) {
			// In Include-Pfaden nach Datei suchen
			foreach($this->getIncludePath() as $includePath) {
				$realpath=realpath($includePath.DIRECTORY_SEPARATOR.$path);
				if(is_file($realpath)) {
					$path=$realpath;
					break;
				}
			}
			
			// Datei immer noch nicht gefunden
			if(!is_file($path)) {
				return NULL;
			}
		}
		return $path;
	}
	
	/**
	 * Ersetzt den aktuellen Token durch den Dateiinhalt der Datei mit dem
	 * übergebenen Namen.
	 * 
	 * @return void
	 * @param string
	 */
	protected function replaceToken($path) {
		if(!isset($this->_parsedFiles[$path])) {
			$this->_parsedFiles[$path]=1;
		}
		else if($this->_parsedFiles[$path]>$this->getMaxRecursionDepth()) {
			$this->raiseError('Maximum include recursion depth of '.$this->getMaxRecursionDepth().' exceeded');
		}
		else {
			$this->_parsedFiles[$path]++;
		}
		
		// Datei parsen
		$includeTokens=new Curly_Sourcecode_Php_Tokens();
		$includeTokens->parseFile($path, true);
		
		$tokens=$this->getTokens();
		$index=$this->getCurrentIndex();
		
		// Aktuellen Token entfernen
		$tokens->removeTokenAt($index);
		
		// Geparste Tokens zu verarbeiteten Tokens hinzufügen
		$tokens->insertTokensAt($index, $includeTokens);
	}
	
}