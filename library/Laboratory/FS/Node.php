<?php

/**
 * FS_Node
 * 
 * Basisklasse für die Repräsentation eines Knotens.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS
 * @since 1.0 - 31.05.2009
 * @version 1.0
 */
abstract class FS_Node {
	
	/**
	 * @var FS_Tree Der Baum, im dem sich dieser Knoten befindet.
	 */
	private $_tree;
	
	/**
	 * @var FS_Node_Container Der Knoten, in dem sich dieser Knoten befindet.
	 */
	private $_parentNode;
	
	/**
	 * @var string Der Name dieses Knotens.
	 */
	private $_name;
	
	/**
	 * @var integer Die Metadaten-ID dieses Knotens.
	 */
	private $_metaID=NULL;
	
	/**
	 * Gibt den Baum, im dem sich dieser Knoten befindet, zurück.
	 * 
	 * @return FS_Tree
	 */
	public function getTree() {
		return $this->_tree;
	}
	
	/**
	 * Setzt den Baum, im dem sich dieser Knoten befindet.
	 * 
	 * @return FS_Node
	 * @param FS_Tree
	 */
	protected function setTree(FS_Tree $value) {
		// TODO: Validate Node??
		$this->_tree=$value;
		return $this;
	}
	
	/**
	 * Fragt ab, ob dieser Knoten einem Baum zugeordnet ist.
	 * 
	 * @return boolean
	 */
	public function hasTree() {
		return $this->_tree!==NULL;
	}
	
	/**
	 * Gibt den Knoten, in dem sich dieser Knoten befindet, zurück.
	 * 
	 * @return FS_Node_Container
	 */
	public function getParentNode() {
		return $this->_parentNode;
	}
	
	/**
	 * Legt den Knoten fest, in dem sich dieser Knoten befindet.
	 * 
	 * @return FS_Node
	 * @param FS_Node_Container
	 */
	protected function setParentNode(FS_Node_Container $node) {
		$this->_parentNode=$node;
		return $this;
	}
	
	/**
	 * Entfernt die Referenz auf den Knoten, dem dieser Knoten untergeordnet
	 * ist.
	 * 
	 * @return FS_Node
	 */
	protected function removeParentNode() {
		$this->_parentNode=NULL;
		return $this;
	}
	
	/**
	 * Überprüft, ob dieser Knoten über eine Referenz auf einen Knoten besitzt,
	 * dem dieser Knoten untergeordnet ist.
	 * 
	 * @return boolean
	 */
	public function hasParentNode() {
		return $this->_parentNode!==NULL;
	}
	
	/**
	 * Gibt den Namen dieses Knotens zurück.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * Setzt den Namen dieses Knotens.
	 * 
	 * @throws FS_Exception
	 * @return FS_Node
	 * @param string
	 */
	public function setName($name) {
		if($this->hasParentNode()) {
			$this->getParentNode()->ensureValidChildNodeName($name);
		}
		
		$this->_name=$name;
		return $this;
	}
	
	/**
	 * Gibt die Metadaten-ID dieses Knotens zurück.
	 * 
	 * @return integer oder NULL wenn noch keine gesetzt ist.
	 */
	public function getMetaID() {
		return $this->_metaID;
	}
	
	/**
	 * Setzt die Metadaten-ID dieses Knotens.
	 * 
	 * @return FS_Node
	 * @param integer oder NULL wenn die ID verworfen werden soll.
	 * @internal
	 */
	protected function setMetaID($value) {
		if($value===NULL) {
			$this->_metaID=NULL;
		}
		else {
			$this->_metaID=(int)$value;
		}
		return $this;
	}
	
	/**
	 * Gibt die Metadaten dieses Knotens zurück.
	 * 
	 * @return FS_Metadata_Interface oder NULL
	 */
	public function getMeta() {
		$this->ensureInTree();
		
		return $this->getTree()->getMetaForNode($this);
	}
	
	/**
	 * Legt die Metadaten dieses Knotens fest.
	 * 
	 * @throws FS_Exception
	 * @return FS_Node
	 * @param FS_Metadata_Interface
	 */
	public function setMeta(FS_Metadata_Interface $dataset) {
		$this->ensureInTree();
		
		$this->getTree()->setMetaForNode($this, $dataset);
		return $this;
	}
	
	/**
	 * Entfernt die Metadaten dieses Knotens.
	 * 
	 * @throws FS_Exception
	 * @return FS_Node
	 */
	public function removeMeta() {
		$this->ensureInTree();
		
		$this->getTree()->removeMetaForNode($this);
		return $this;
	}
	
	/**
	 * Gibt den Metadaten-Datensatz zu dem übergebenen Knoten zurück.
	 * 
	 * @throws FS_Exception
	 * @return FS_Metadata_Interface oder NULL
	 * @param FS_Node
	 * @internal
	 */
	protected function getMetaForNode(FS_Node $node) {
		throw new FS_Exception(__METHOD__.' is not implemented');
	}
	
	/**
	 * Setzt den Metadaten-Datensatz zu dem übergebenen Knoten.
	 * 
	 * @throws FS_Exception
	 * @return void
	 * @param FS_Node
	 * @param FS_Metadata_Interface
	 * @internal
	 */
	protected function setMetaForNode(FS_Node $node, FS_Metadata_Interface $meta) {
		throw new FS_Exception(__METHOD__.' is not implemented');
	}
	
	/**
	 * Entfernt den Metadaten-Datensatz zu dem übergebenen Knoten.
	 * 
	 * @throws FS_Exception
	 * @return void
	 * @param FS_Node
	 * @internal
	 */
	protected function removeMetaForNode(FS_Node $node) {
		throw new FS_Exception(__METHOD__.' is not implemented');
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param string Der Name dieses Knotens.
	 */
	public function __construct($name) {
		$this->setName($name);
	}
	
	/**
	 * Überprüft, ob dieser Knoten in einer Baumstruktur enthalten ist.
	 * 
	 * @return boolean
	 */
	public function isSolo() {
		return !$this->hasParentNode();
	}
	
	/**
	 * Überprüft, ob dieser Knoten ein Blatt in der Baumstruktur ist.
	 * 
	 * @return boolean
	 */
	public function isLeaf() {
		return true;
	}
	
	/**
	 * Stellt sicher, dass dieser Knoten sich in einem Baum befindet.
	 * 
	 * @throws FS_Exception
	 * @return void
	 */
	public function ensureInTree() {
		if(!$this->hasTree()) {
			throw new FS_Exception('This node is not part of a tree and is not modifiable');
		}
	}
	
	/**
	 * Gibt den vorigen Geschwisterknoten zu diesem Knoten zurück.
	 * 
	 * @return FS_Node oder NULL wenn dieser Knoten der erste Knoten ist oder
	 *  kein Unterknoten eines anderen ist.
	 */
	public function getPreviousSibling() {
		// Kein Unterknoten
		if(!$this->hasParentNode()) {
			return NULL;
		}
		
		return $this->getParentNode()->getChildNodeBefore($this);
	}
	
	/**
	 * Gibt den nachfolgenden Geschwisterknoten zu diesem Knoten zurück.
	 * 
	 * @return FS_Node oder NULL wenn dieser Knoten der letzte Knoten ist oder
	 *  kein Unterknoten eines anderen ist.
	 */
	public function getNextSibling() {
		// Kein Unterknoten
		if(!$this->hasParentNode()) {
			return NULL;
		}
		
		return $this->getParentNode()->getChildNodeAfter($this);
	}
	
	/**
	 * Nimmt die übergebene Operation an, um diesen Knoten zu besuchen.
	 * 
	 * @return void
	 * @param FS_Visitor_Interface
	 */
	public function acceptVisitor(FS_Visitor_Interface $visitor) {
		$visitor->visitNode($this);
	}
	
}