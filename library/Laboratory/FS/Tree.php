<?php

/**
 * FS_Tree
 * 
 * Stellt einen kompletten FS-Baum dar.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS
 * @since 1.0 - 31.05.2009
 * @version 1.0
 */
class FS_Tree extends FS_Node_Container {
	
	/**
	 * @desc Regulärer Ausdruck für ungültige Zeichen in einem Knotennamen.
	 */
	const NODENAME_REGEX='~\\\\|/~';
	
	/**
	 * @desc Name der Streamwrapper-Klasse
	 */
	const STREAMWRAPPER='FS_StreamWrapper';
	
	/**
	 * @var FS_Metadata_List Die Metadaten-Datensätze zu diesem Baum.
	 */
	private $_metadata;
	
	/**
	 * Gibt die Metadaten-Datensätze zu diesem Baum zurück.
	 * 
	 * @return FS_Metadata_List
	 * @internal
	 */
	public function getMetadata() {
		return $this->_metadata;
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param string Der Name dieses Baumes.
	 */
	public function __construct($name) {
		if(substr($name, -1)!==':') {
			$name.=':';
		}
		parent::__construct($name);
		$this->setTree($this);
		$this->_metadata=new FS_Metadata_List();
		
		FS_Tree_Registry::set($name, $this);
		stream_wrapper_register(rtrim($name, ':'), self::STREAMWRAPPER);
	}
	
	/**
	 * Überprüft ob der übergebene Knotenname ausschließlich gültige Zeichen
	 * enthält.
	 * 
	 * @return boolean
	 * @param string
	 */
	public function isValidNodeName($name) {
		return strlen($name)>0 and !preg_match(self::NODENAME_REGEX, $name);
	}
	
	/**
	 * Stellt sicher, dass der übergebene Knotenname ausschließlich gültige
	 * Zeichen enthält.
	 * 
	 * @throws FS_Exception
	 * @return void
	 * @param string
	 */
	public function ensureValidNodeName($name) {
		if(!$this->isValidNodeName($name)) {
			throw new FS_Exception('The given node name is not valid');
		}
	}
	
	/**
	 * Importiert den übergebenen Knoten in diesen Baum.
	 * 
	 * Wichtig: Der Knoten darf in noch keinem Baum eingefügt worden sein, und
	 * ein Container-Knoten darf noch keine Unterknoten enthalten.
	 * 
	 * @throws FS_Exception
	 * @return FS_Tree
	 * @param FS_Node
	 */
	public function importNode(FS_Node $node) {
		if(!$node->isSolo()) {
			throw new FS_Exception('The given node is not solo');
		}
		$node->setTree($this);
		return $this;
	}
	
	/**
	 * Erstellt einen neuen Container-Knoten mit dem übergebenen Namen und fügt
	 * diesen diesem Baum hinzu.
	 * 
	 * @return FS_Node_Container
	 * @param string Der Name des Knotens
	 */
	public function createContainer($name) {
		$node=new FS_Node_Container($name);
		$node->setTree($this);
		return $node;
	}
	
	/**
	 * Erstellt einen neuen Blatt-Knoten mit dem übergebenen Namen und fügt
	 * diesen diesem Baum hinzu.
	 * 
	 * @return FS_Node_Leaf
	 * @param string Der Name des Knotens
	 */
	public function createLeaf($name) {
		$node=new FS_Node_Leaf($name);
		$node->setTree($this);
		return $node;
	}
	
	/**
	 * Ermittelt den Pfad zu dem übergebenen Knoten.
	 * 
	 * @throws FS_Exception
	 * @return FS_Path
	 * @param FS_Node
	 */
	public function getPathToNode(FS_Node $node) {
		// Nicht Teil dieses Baumes
		if($node->getTree()!==$this) {
			throw new FS_Exception('The given node is not part of this tree');
		}
		
		$path=array();
		$pLast=$p=$node;
		while($p!==NULL) {
			array_unshift($path, $p->getName());
			$pLast=$p;
			$p=$p->getParentNode();
		}
		
		// Letztes Element war nicht der Baum -> Knoten ist nicht korrekt
		// in Baumstruktur eingebunden
		if($pLast!==$this) {
			throw new FS_Exception('The given node is not part of this tree structure');
		}
		
		return new FS_Path($path);
	}
	
	/**
	 * Gibt den Metadaten-Datensatz zu dem übergebenen Knoten zurück.
	 * 
	 * @return FS_Metadata_Interface oder NULL
	 * @param FS_Node
	 * @internal
	 */
	protected function getMetaForNode(FS_Node $node) {
		return $this->_metadata->getNodeDataset($node);
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
		$this->_metadata->addDataset($meta);
		$node->setMetaID($meta->getID());
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
		$node->setMetaID(NULL);
	}
	
}