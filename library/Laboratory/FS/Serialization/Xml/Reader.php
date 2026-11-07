<?php

/**
 * FS_Serialization_Xml_Reader
 * 
 * Klasse zum Lesen eines in XML serialisierten Baumes.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Serialization.Xml
 * @since 1.0 - 01.06.2009
 * @version 1.0
 */
class FS_Serialization_Xml_Reader implements FS_Serialization_Reader {
	
	/**
	 * @var FS_Metadata_Factory Die zu verwendende Fabrikinstanz für die
	 * Erstellung von Metadata-Objekten.
	 */
	private $_metadataFactory=NULL;
	
	/**
	 * Gibt die zu verwendende Fabrikinstanz für die Erstellung von
	 * Metadata-Objekten zurück.
	 * 
	 * @return FS_Metadata_Factory
	 */
	public function getMetadataFactory() {
		if($this->_metadataFactory===NULL) {
			$this->_metadataFactory=FS_Metadata_Factory::getDefault();
		}
		return $this->_metadataFactory;
	}
	
	/**
	 * Setzt die zu verwendende Fabrikinstanz für die Erstellung von
	 * Metadata-Objekten.
	 * 
	 * @return FS_Serialization_Xml_Reader
	 * @param FS_Metadata_Factory
	 */
	public function setMetadataFactory(FS_Metadata_Factory $value) {
		$this->_metadataFactory=$value;
		return $this;
	}
	
	/**
	 * @var DOMDocument Das zu lesende XML-Dokument.
	 */
	protected $doc;
	
	/**
	 * @var DOMXPath XPath-Objekt auf dem zu lesenden XML-Dokument.
	 */
	protected $xpath;
	
	/**
	 * Ließt die serialisierten Daten aus der Datei mit dem übergebenen Namen.
	 * 
	 * @throws FS_Exception
	 * @return FS_Tree
	 * @param string
	 */
	public function readFile($filename) {
		$doc=new DOMDocument();
		$doc->preserveWhitespace=false;
		if(!$doc->load($filename)) {
			throw new FS_Exception('Failed to parse the file '.$filename);
		}
		return $this->parse($doc);
	}
	
	/**
	 * Ließt die serialisierten Daten aus der übergebenen Zeichenkette.
	 * 
	 * @throws FS_Exception
	 * @return FS_Tree
	 * @param string
	 */
	public function readString($data) {
		$doc=new DOMDocument();
		$doc->preserveWhitespace=false;
		if(!$doc->loadXML($filename)) {
			throw new FS_Exception('Failed to parse the given xml data');
		}
		return $this->parse($doc);
	}
	
	/**
	 * Parst das übergebene XML-Dokument.
	 * 
	 * @throws FS_Exception
	 * @return FS_Tree
	 * @param DOMDocument
	 */
	public function parse(DOMDocument $doc) {
		$this->doc=$doc;
		$this->xpath=new DOMXPath($doc);
		$tree=$this->parseTree();
		return $tree;
	}
	
	/**
	 * Parst die Baumstruktur aus dem XML-Dokument.
	 * 
	 * @throws FS_Exception
	 * @return FS_Tree
	 */
	protected function parseTree() {
		$treeNodes=$this->xpath->query('/fs/tree[position()=1][@name]');
		
		// Keine Daten gefunden
		if($treeNodes->length<=0) {
			throw new FS_Exception('No tree data found');
		}
		$treeNode=$treeNodes->item(0);
		$treeName=trim($treeNode->getAttribute('name'));
		if($treeName==='') {
			throw new FS_Exception('The name attribute of the tree tag requires to have a value');
		}
		
		$tree=new FS_Tree($treeName);
		$this->parseNodes($tree, $treeNode);
		
		return $tree;
	}
	
	/**
	 * Parst die Knoten zu dem übergebenen Knoten und XML-Element.
	 * 
	 * @return void
	 * @param FS_Node
	 * @param DOMElement
	 */
	protected function parseNodes(FS_Node $parent, DOMElement $domNode) {
		$nodeNodes=$this->xpath->query('//node[@name]', $domNode);
		// TODO: FS_Serialization_Xml_Reader::parseNodes
	}
	
	/**
	 * Sucht im XML-Dokument nach den Metadaten mit der übergebenen ID und gibt
	 * ein entsprechendes Metadata-Objekt zurück.
	 * 
	 * @return FS_Metadata_Interface oder NULL
	 * @param string
	 */
	protected function parseMetaNode($id) {
		$metaNodes=$this->xpath->query('/fs/metadata[position()=1]/meta[@id="'.$id.'"][@type]');
		// Keine Daten vorhanden
		if($metaNodes->length<=0) {
			return NULL;
		}
		
		return $metaNodes->item(0);
	}
	
}