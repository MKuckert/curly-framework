<?php

require_once "CurlyX/Manifest/Package.php";

/**
 * CurlyX_Manifest_Writer
 * 
 * Implementiert einen Writer für Manifest-Dateien nach dem Format 0.1.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Manifest
 * @since 1.0 - 30.12.2008
 * @version 1.0
 */
class CurlyX_Manifest_Writer {
	
	const NAMESPACE=CurlyX_Manifest::MANIFEST_NAMESPACE;
	const QUALIFIED_NAME='installer';
	
	/**
	 * @var DOMDocument Zu verwendende DOM-Dokumentinstanz.
	 */
	private $_doc=NULL;
	
	/**
	 * @var array Die diesem Manifest angefügte Packages.
	 */
	private $_packages=array();
	
	/**
	 * Gibt die zu verwendende DOM-Dokumentinstanz zurück.
	 * Diese Methode sollte den entsprechenden Writer-Klassen vorbehalten
	 * bleiben.
	 */
	public function getDoc() {
		return($this->_doc);
	}
	
	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->createDocument();
	}
	
	/**
	 * Erstellt die zu verwendende DOM-Dokumentinstanz.
	 * 
	 * @return void
	 */
	protected function createDocument() {
		$this->_doc=new DOMDocument();
		$root=$this->createElement('manifest');
		$this->_doc->appendChild($root);
	}
	
	/**
	 * Erstellt einen DOM-Knoten.
	 * 
	 * @return DOMElement
	 * @param string Der zu verwendende Name
	 */
	public function createElement($name) {
		return($this->_doc->createElementNS(self::NAMESPACE, self::QUALIFIED_NAME, $name));
	}
	
	/**
	 * Fügt diesem Manifest einen Author hinzu.
	 * 
	 * @return CurlyX_Manifest_Writer
	 * @param array Daten über den Author als assoziatives Array
	 */
	public function addAuthor(array $data) {
		$authorNode=$this->createElement('author');
		
		foreach($data as $name=>$value) {
			$node=$this->createElement($name);
			$node->appendChild($this->_doc->createTextNode($value));
			$authorNode->appendChild($node);
		}
	}
	
	/**
	 * Fügt diesem Manifest ein Package hinzu. Es wird eine neue
	 * CurlyX_Manifest_Package-Instanz erstellt und diesem Writer angefügt.
	 * 
	 * @return CurlyX_Manifest_Package
	 */
	public function addPackage() {
		$package=new CurlyX_Manifest_Package($this);
		$this->_packages[]=$package;
		return($package);
	}
	
/*
<installer:manifest xmlns:installer="http://schema.curlybracket.de/Installer_v0.1.xsd"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://schema.curlybracket.de/Installer_v0.1.xsd Installer_v0.1.xsd ">
  <author>
    <name>Martin Kuckert</name>
    <email>martin@curlybracket.de</email>
    <url>http://curlybracket.de/</url>
  </author>
  <package>
  	<name>MyModule</name>
  	<version>0.1pa</version>
  	<published>2008-12-10T00:00:00</published>
  	<description>Is good for this and for that.</description>
  	<dependencies>
  		<dependency type="module" name="SharpLite" version="1.0" />
  		<dependency type="module" name="XmlParser" version="1.1" />
  		<dependency type="parser" name="MyParser" version="0.8" />
  	</dependencies>
  	<files>
  		<file name="MyClass.php" base="MyDir" />
  		<file name="MySecondClass.php" base="MyDir/Second" />
  	</files>
  </package>
</installer:manifest>
*/
	
}