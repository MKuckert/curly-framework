<?php

/**
 * FS_Node_Container
 * 
 * Knoten für die Kapselung mehrerer Unterknoten
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Node
 * @since 1.0 - 31.05.2009
 * @version 1.0
 */
class FS_Node_Container extends FS_Node {
	
	/**
	 * @var array Diesem Knoten untergeordnete Knoten.
	 */
	private $_childNodes=array();
	
	/**
	 * Gibt die diesem Knoten untergeordnete Knoten zurück.
	 * 
	 * @throws FS_Exception
	 * @return array
	 */
	public function getChildNodes() {
		return $this->_childNodes;
	}
	
	/**
	 * Überprüft, ob dieser Knoten über untergeordnete Knoten verfügt.
	 * 
	 * @return boolean
	 */
	public function hasChildNodes() {
		return count($this->_childNodes)>0;
	}
	
	/**
	 * Fügt diesem Knoten einen untergeordneten Knoten hinzu.
	 * 
	 * @throws FS_Exception
	 * @return FS_Node_Container
	 * @param FS_Node
	 */
	public function addChildNode(FS_Node $node) {
		return $this->insertChildNodeBefore($node);
	}
	
	/**
	 * Fügt diesem Knoten einen untergeordneten Knoten vor den übergebenen
	 * zweiten Knoten hinzu.
	 * 
	 * @throws FS_Exception
	 * @return FS_Node_Container
	 * @param FS_Node
	 * @param FS_Node Fehlt dieser Referenzknoten, wird der Knoten an das Ende
	 *  angefügt.
	 */
	public function insertChildNodeBefore(FS_Node $node, FS_Node $referenceNode=NULL) {
		$this->ensureValidChildNode($node);
		
		// Kein Baum zugeordnet
		if(!$node->hasTree()) {
			$this->getTree()->importNode($node);
		}
		
		// Ist bereits in Struktur
		if($node->hasParentNode()) {
			$node->getParentNode()->removeChildNode($node);
		}
		
		// Kein Referenzknoten gegeben
		if($referenceNode===NULL) {
			// Knoten einfach hinzufügen
			$this->_childNodes[]=$node;
		}
		else {
			// Referenzknoten suchen
			$index=array_search($referenceNode, $this->_childNodes, true);
			if($index===false) {
				throw new FS_Exception('The given reference node is no child node of this node');
			}
			array_splice($this->_childNodes, $index, 0, array($node));
		}
		
		$node->setParentNode($this);
		
		return $this;
	}
	
	/**
	 * Stellt sicher, dass der übergebene Knoten ein gültiger Unterknoten ist.
	 * 
	 * @throws FS_Exception
	 * @return void
	 * @param FS_Node
	 */
	protected function ensureValidChildNode(FS_Node $node) {
		$this->ensureInTree();
		
		// Falschem Baum zugeordnet
		if($node->hasTree() and $node->getTree()!==$this->getTree()) {
			throw new FS_Exception('The given node is not part of tree this node is contained in');
		}
		
		// Bereits Unterknoten
		if(in_array($node, $this->_childNodes, true)) {
			throw new FS_Exception('The given node is already a child node of this node');
		}
		
		// Name bereits vergeben
		$this->ensureValidChildNodeName($node->getName());
		
		// Ist Containerknoten -> Überprüfen, ob dies Unterknoten ist.
		if($node instanceof FS_Node_Container) {
			$p=$this;
			while($p!==NULL) {
				if($p===$node) {
					throw new FS_Exception('Hierarchy error: This node is a subnode of the given node');
				}
				$p=$p->getParentNode();
			}
		}
	}
	
	/**
	 * Stellt sicher dass der übergebene Name für einen neuen Unterknoten
	 * gültig ist, d.h. noch kein anderer Knoten mit diesem Namen als
	 * Unterknoten vorhanden ist.
	 * 
	 * @throws FS_Exception
	 * @return void
	 * @param string
	 */
	public function ensureValidChildNodeName($name) {
		$this->ensureInTree();
		
		// Ungültiger Name
		$this->getTree()->ensureValidNodeName($name);
		
		// Name bereits vergeben
		foreach($this->_childNodes as $childNode) {
			if($childNode->getName()===$name) {
				throw new FS_Exception('A node with the name of the given node is already a child node of this node');
			}
		}
	}
	
	/**
	 * Entfernt den übergebenen Knoten aus diesem Knoten.
	 * 
	 * @throws FS_Exception
	 * @return FS_Node_Container
	 * @param FS_Node
	 */
	public function removeChildNode(FS_Node $node) {
		$index=array_search($node, $this->_childNodes, true);
		if($index===false) {
			throw new FS_Exception('The given node is not a child node of this node');
		}
		
		// Knoten herausnehmen
		unset($this->_childNodes[$index]);
		$node->removeParentNode();
		
		// Liste neu durchnummerieren
		$this->_childNodes=array_values($this->_childNodes);
		
		return $this;
	}
	
	/**
	 * Ersetzt den übergebenen zweiten Knoten durch den ersten Knoten.
	 * 
	 * @throws FS_Exception
	 * @return FS_Node_Container
	 * @param FS_Node
	 * @param FS_Node
	 */
	public function replaceChildNode(FS_Node $newnode, FS_Node $oldnode) {
		return $this
			->insertChildNodeBefore($newnode, $oldnode)
			->removeChildNode($oldnode);
	}
	
	/**
	 * Gibt den ersten Unterknoten dieses Knotens zurück.
	 * 
	 * @return FS_Node oder NULL wenn dieser Knoten über keine Unterknoten
	 * verfügt.
	 */
	public function getFirstChildNode() {
		if(!$this->hasChildNodes()) {
			return NULL;
		}
		
		return $this->_childNodes[0];
	}
	
	/**
	 * Gibt den letzten Unterknoten dieses Knotens zurück.
	 * 
	 * @return FS_Node oder NULL wenn dieser Knoten über keine Unterknoten
	 * verfügt.
	 */
	public function getLastChildNode() {
		if(!$this->hasChildNodes()) {
			return NULL;
		}
		
		return $this->_childNodes[count($this->_childNodes)-1];
	}
	
	/**
	 * Gibt den Unterknoten vor dem übergebenen Knoten zurück.
	 * 
	 * @return FS_Node
	 * @param FS_Node
	 */
	public function getChildNodeBefore(FS_Node $node) {
		$index=array_search($node, $this->_childNodes, true);
		// Nicht gefunden oder vorderster Knoten
		if($index==false) {
			return NULL;
		}
		
		return $this->_childNodes[$index-1];
	}
	
	/**
	 * Gibt den Unterknoten nach dem übergebenen Knoten zurück.
	 * 
	 * @return FS_Node
	 * @param FS_Node
	 */
	public function getChildNodeAfter(FS_Node $node) {
		$index=array_search($node, $this->_childNodes, true);
		// Nicht gefunden
		if($index===false) {
			return NULL;
		}
		// Hinterster Knoten
		else if(count($this->_childNodes)-1==$index) {
			return NULL;
		}
		
		return $this->_childNodes[$index+1];
	}
	
	/**
	 * Überprüft, ob dieser Knoten in einer Baumstruktur enthalten ist.
	 * 
	 * @return boolean
	 */
	public function isSolo() {
		return parent::isSolo() && !$this->hasChildNodes();
	}
	
	/**
	 * Überprüft, ob dieser Knoten ein Blatt in der Baumstruktur ist.
	 * 
	 * @return boolean
	 */
	public function isLeaf() {
		return !$this->hasChildNodes();
	}
	
	/**
	 * Magische Methode. Wird nach dem Klonen dieses Objektes am Klon
	 * aufgerufen und erzeugt eine tiefe Kopie.
	 * 
	 * @return void
	 */
	public function __clone() {
		foreach($this->_childNodes as $index=>$node) {
			$this->_childNodes[$index]=clone $node;
		}
	}
	
	/**
	 * Nimmt die übergebene Operation an, um diesen Knoten zu besuchen.
	 * 
	 * @return void
	 * @param FS_Visitor_Interface
	 */
	public function acceptVisitor(FS_Visitor_Interface $visitor) {
		if($visitor->visitNode($this)) {
			foreach($this->_childNodes as $childNode) {
				$childNode->acceptVisitor($visitor);
			}
		}
	}
	
}