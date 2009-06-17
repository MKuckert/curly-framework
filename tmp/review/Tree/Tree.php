<?php

/**
 * Tree
 * Diese Klasse stellt eine Baumstruktur dar. Knoten des Baumes sind
 * {@link TreeNode}-Objekte und beinhalten einen einzelnen Datensatz der
 * Struktur. Ein Knoten kann beliebig viele Nachfolgerknoten besitzen.
 * Dieser Baum kann einen Wurzelknoten enthalten, auf welchen über
 * die {@link getRoot Root}-Eigenschaft zugegriffen werden kann.
 *
 * Diese Implementierung bietet die Basisfunktionen einer Baumstruktur und
 * die Verwaltung dieser. Die Erstellung eines Baumes aus einer Datenbank
 * oder sonstigen Datenquellen ist die Aufgabe von Unterklassen.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Tree
 * @since 1.0 - 22.05.2008
 * @version 1.0
 */
class Tree extends Object {

	/**
	 * @var TreeNode Der Wurzelknoten dieses Baumes.
	 */
	private $_root = null;

	/**
	 * @var string Der Name der Klasse, die für neue Knotenobjekte
	 * verwendet werden soll. Diese Klasse muss von der TreeNode-Klasse
	 * abgeleitet sein.
	 */
	private $_nodeClass = 'TreeNode';

	/**
	 * @var string Der Name der Klasse, die für neue Knotensammlungsobjekte
	 * verwendet werden soll. Diese Klasse muss von der
	 * TreeNodeCollection-Klasse abgeleitet sein.
	 */
	private $_collectionClass = 'TreeNodeCollection';

	/**
	 * Gibt den Wurzelknoten dieses Baumes zurück.
	 *
	 * @return TreeNode
	 */
	public function getRoot() {
		if( is_null( $this->_root ) ) {
			$this->_root = $this->createNode();
		}
		return( $this->_root );
	}

	public function getNodeClass() {
		return( $this->_nodeClass );
	}

	public function getNodeCollectionClass() {
		return( $this->_collectionClass );
	}

	public function createNode() {
		$class = $this->getNodeClass();
		return( new $class( $this ) );
	}

	public function createNodeCollection( TreeNode $owner ) {
		$class = $this->getNodeCollectionClass();
		return( new $class( $owner ) );
	}

	public function dump() {
		$this->getRoot()->dump();
	}

}

?>