<?php

/**
 * TreeNode
 * Basisklasse für einen Knoten innerhalb der Baumklasse {@link Tree}.
 *
 * Ein Knoten ist immer einem Baum-Objekt zugeordnet.
 *
 * Die Methoden eines Knoten operieren immer auf dem aktuellen Index des
 * Knotens in der Unterknotensammlung des Elternknotens. Ein erstellter
 * Baum könnte beispielsweise folgende Gestalt besitzen:
 * <code>
 *      1
 *    / | \
 *   2  3  4
 *  /  / \
 * 5  6   7
 * </code>
 * Der Knoten 1 ist der Wurzelknoten des Baumes.
 * Die Knoten 2, 3 und 4 sind die Unterknoten des Knoten 1 und besitzen den
 * Index 0 bis 2. Wird am Knoten 3 die
 * {@link getNextSibling NextSibling}-Eigenschaft abgerufen, wird mit dem
 * aktuellen Index des Knotens (Wert 1) über die TreeNodeCollection-Instanz
 * des Elternknotens 1 auf den Knoten 4 mit dem Index 2 zugegriffen. Auf
 * gleicher Weise erfolgt der Zugriff auf die
 * {@link getPreviousSibling PreviousSibling}-Eigenschaft.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Tree
 * @since 1.0 - 22.05.2008
 * @version 1.0
 */
class TreeNode extends Object {

	/**
	 * @desc Ist der Index eines Knotens auf diesen Wert gesetzt, ist
	 * dieser in keiner TreeNodeCollection enthalten, verfügt also nur über
	 * eingeschränkte Funktionalität.
	 */
	const SingleNode = -1;

	/**
	 * @var int Der Index dieses Knotens in der TreeNodeCollection des
	 * Elternknotens, in dem sich dieser Knoten befindet.
	 */
	private $_index = self::SingleNode;

	/**
	 * @var Tree Der Baum, dem dieser Knoten angehört.
	 */
	private $_tree = null;

	/**
	 * @var TreeNodeCollection Die Sammlung der Unterknoten dieses Knotens.
	 */
	private $_childNodes = null;

	/**
	 * @var TreeNode Der Elternknoten dieses Knotens.
	 */
	private $_parentNode = null;

	private $ID;
	static private $IDs=0;
	public function getID() {
		return $this->ID;
	}

	/**
	 * Konstruktor
	 * Bei der Erstellung eines Knotens muss der Baum, in dem dieser Knoten
	 * enthalten ist, gesetzt werden.
	 *
	 * @param Tree
	 */
	public function __construct( Tree $tree ) {
		$this->_tree = $tree;
		$this->ID = self::$IDs++;
	}
	public function dump() {
		echo $this->_index.': '.$this->ID."\n";
		$c = $this->getChildNodes();
			
		$n=$c->getCount();
		if( $n<=0 ) {
			return;
		}
			
		echo "children:\n";
		for( $i=0; $i<$n; $i++ ) {
			$c->getAt( $i )->dump();
		}
	}

	/**
	 * Gibt den Index dieses Knotens zurück.
	 *
	 * @return int
	 */
	public function getIndex() {
		return( $this->_index );
	}

	/**
	 * Setzt den Index dieses Knotens.
	 *
	 * @return boolean
	 * @param int
	 */
	public function setIndex( $index ) {
		$parent = $this->getParentNode();
			
		if( is_null( $parent ) ) {
			return( false );
		}
			
		return( $parent->getChildNodes()->setAt( $index, $this ) );
	}

	/**
	 * Gibt das Baum-Objekt, zu dem dieser Knoten gehört, zurück.
	 *
	 * @return Tree
	 */
	public function getTree() {
		return( $this->_tree );
	}

	/**
	 * Gibt die Sammlung der Unterknoten dieses Knotens zurück.
	 *
	 * Besitzt dieser Knoten noch keine TreeNodeCollection für Unterknoten,
	 * wird eine neue Instanz erstellt.
	 *
	 * @return TreeNodeCollection
	 */
	public function getChildNodes() {
		if( is_null( $this->_childNodes ) ) {
			$this->_childNodes = $this->getTree()->createNodeCollection( $this );
		}
		return( $this->_childNodes );
	}

	/**
	 * Überprüft, ob dieser Knoten über Unterknoten verfügt und gibt
	 * entsprechend einen boolschen Wert zurück.
	 *
	 * @return boolean
	 */
	public function getHasChildNodes() {
		return( is_null( $this->_childNodes ) or $this->_childNodes->count()<=0 );
	}

	/**
	 * Gibt den Elternknoten dieses Knotens zurück.
	 *
	 * Handelt es sich bei diesem Knoten um den Wurzelknoten des Baumes
	 * oder einen unzugeordneten Baum, wird NULL zurückgegeben.
	 *
	 * @return TreeNode
	 */
	public function getParentNode() {
		return( $this->_parentNode );
	}

	/**
	 * Setzt den Elternknoten dieses Knotens.
	 *
	 * Wird kein Parameter bzw. NULL übergeben, wird dieser Knoten von
	 * seinem Elternknoten entkoppelt.
	 *
	 * @return void
	 * @param TreeNode
	 */
	public function setParentNode( TreeNode $parent = null ) {
		if( is_null( $parent ) ) {
			$this->leaveAlone();
		}
		else {
			$oldParent = $this->getParentNode();

			// Dieser Knoten hat einen Elternknoten
			if( !is_null( $oldParent ) ) {
				$oldParent->getChildNodes()->removeAt( $this->getIndex() );
			}

			$parent->getChildNodes()->add( $this );
		}
	}

	/**
	 * Gibt den rechten Nachbarknoten dieses Knotens zurück.
	 *
	 * Hat dieser Knoten keinen rechten Nachbarknoten, wird NULL
	 * zurückgegeben.
	 *
	 * @return TreeNode
	 */
	public function getNextSibling() {
		$parent = $this->getParentNode();
		if( is_null( $parent ) ) {
			return( null );
		}
			
		return( $parent->getChildNodes()->getNext( $this->getIndex() ) );
	}

	/**
	 * Setzt den rechten Nachbarknoten dieses Knotens.
	 *
	 * @return boolean Wurde der Knoten erfolgreich eingefügt, wird true
	 *  zurückgegeben.
	 * @param TreeNode
	 */
	public function setNextSibling( TreeNode $node ) {
		$parent = $this->getParentNode();
		if( is_null( $parent ) ) {
			return( false );
		}
			
		return( $parent->getChildNodes()->setNext( $this->getIndex(), $node ) );
	}

	/**
	 * Gibt den linken Nachbarknoten dieses Knotens zurück.
	 *
	 * Hat dieser Knoten keinen linken Nachbarknoten, wird NULL
	 * zurückgegeben.
	 *
	 * @return TreeNode
	 */
	public function getPreviousSibling() {
		$parent = $this->getParentNode();
		if( is_null( $parent ) ) {
			return( null );
		}
			
		return( $parent->getChildNodes()->getPrevious( $this->getIndex() ) );
	}

	/**
	 * Benachrichtigt diesen Knoten darüber, dass sich die aktuelle
	 * Position in der Baumstruktur geändert hat.
	 *
	 * Diese Methode sollte nicht direkt aufgerufen werden!
	 *
	 * @return void
	 * @param int Der neue Index dieses Knotens
	 * @param TreeNode Der neue Elternknoten
	 */
	public function moved( $index, TreeNode $parent=null ) {
		$this->_index = $index;
		if( !is_null( $parent ) ) {
			$this->_parentNode = $parent;
		}
	}

	/**
	 * Benachrichtigt diesen Knoten darüber, dass dieser aus der
	 * Baumstruktur entfernt wurde und keine feste Position mehr besitzt.
	 * Lediglich die Zuordnung zu dem Baum bleibt bestehen.
	 *
	 * @return void
	 */
	public function leaveAlone() {
		$this->_index = self::SingleNode;
		$this->_parentNode = null;
	}

}

?>