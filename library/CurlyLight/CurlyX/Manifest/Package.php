<?php

/**
 * CurlyX_Manifest_Package
 *
 * Stellt ein Paket innerhalb eines Manifests dar. Wird von der
 * CurlyX_Manifest_Writer-Klasse verwendet.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Manifest
 * @since 1.0 - 30.12.2008
 * @version 1.0
 */
class CurlyX_Manifest_Package {

	/**
	 * @var CurlyX_Manifest_Writer Die zu dieser Instanz gehörige
	 * Writer-Instanz.
	 */
	private $_writer=NULL;

	/**
	 * @var DOMElement Die Element-Instanz, die diese Instanz verwendet.
	 */
	private $_element=NULL;

	/**
	 * @var array Lookup-Liste von Einzel-Elementen im DOM-Dokument.
	 */
	private $_createdElements=array(
		'name'			=> NULL,
	  	'version'		=> NULL,
	  	'published'		=> NULL,
	  	'description'	=> NULL
	);

	/**
	 * @var array Lookup-Liste von Container-Elementen im DOM-Dokument.
	 */
	private $_containerElements=array(
		'dependencies'	=> NULL,
		'files'			=> NULL
	);

	/**
	 * Konstruktor
	 *
	 * @param CurlyX_Manifest_Writer
	 */
	public function __construct(CurlyX_Manifest_Writer $writer) {
		$this->_writer=$writer;

		$this->init();
	}

	/**
	 * Fügt die nötigen XML-Daten an die DOM-Dokumentinstanz des
	 * Manifest-Writers an.
	 *
	 * @return void
	 */
	protected function init() {
		$w=$this->_writer;
		$doc=$w->getDoc();

		// Package-Knoten erzeugen
		$this->_element=$w->createElement('package');
		$doc->firstChild->appendChild($this->_element);

		// Elemente erstellen
		foreach(array($this->_createdElements, $this->_containerElements) as $arr) {
			foreach($arr as $name=>$unused) {
				$node=$w->createElement($name);
				$txt=$doc->createTextNode('');
				$node->appendChild($txt);
				$this->_createdElements[$name]=$txt;
				$this->_element->appendChild($node);
			}
		}
	}

	/**
	 * Gibt einen Wert dieses Paketes zurück.
	 *
	 * @return string
	 * @param string Name des Wertes
	 */
	public function __get($name) {
		if(isset($this->_createdElements[$name])) {
			return($this->_createdElements[$name]->nodeValue);
		}
		else {
			require_once "CurlyX/Manifest/Exception.php";
			throw new CurlyX_Manifest_Exception('The value '.$name.' is not defined');
		}
	}

	/**
	 * Setzt einen Wert dieses Paketes.
	 *
	 * @return string
	 * @param string Name des Wertes
	 * @param string Der Wert
	 */
	public function __set($name, $value) {
		if(isset($this->_createdElements[$name])) {
			$this->_createdElements[$name]->nodeValue=$value;
		}
		else {
			require_once "CurlyX/Manifest/Exception.php";
			throw new CurlyX_Manifest_Exception('The value '.$name.' is not defined');
		}
	}
	
	// TODO: addDependency, addFile, ...

	/*
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
	 */

}