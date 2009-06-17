<?php

/**
 * TreeNodeCollection
 * Stellt eine Sammlung von {@link TreeNode}-Objekten dar. Diese Klasse
 * wird von der {@link Tree}- bzw. TreeNode-Klasse verwendet.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Tree
 * @since 1.0 - 22.05.2008
 * @version 1.0
 */
class TreeNodeCollection extends Object {

	/**
	 * @var TreeNode Knoten-Objekt, zu dem diese Sammlung gehört.
	 */
	private $_owner;

	/**
	 * @var array Die Sammlungsimplementierung dieser Liste.
	 */
	private $_implementation = array();

	/**
	 * Gibt die Sammlungsimplementierung dieser Liste zurück.
	 *
	 * @return array
	 */
	protected function &getImplementation() {
		return $this->_implementation;
	}

	/**
	 * Konstruktor
	 * Der Knoten, dem diese Sammlung gehört, muss direkt festgelegt
	 * werden.
	 *
	 * @param TreeNode
	 */
	public function __construct( TreeNode $owner ) {
		$this->_owner = $owner;
	}

	/**
	 * Gibt das Knoten-Objekt zurück, zu dem diese Sammlung gehört.
	 *
	 * @return TreeNode
	 */
	public function getOwner() {
		return( $this->_owner );
	}

	/**
	 * Gibt das TreeNode-Objekt an der Stelle $index in dieser Sammlung
	 * zurück.
	 *
	 * Ist an der Stelle kein Objekt gesetzt, wird NULL zurückgegeben.
	 *
	 * @return TreeNode
	 * @param int
	 */
	public function getAt( $index ) {
		if( isset( $this->_implementation[$index] ) ) {
			return( $this->_implementation[$index] );
		}
		else {
			return( null );
		}
	}

	/**
	 * Fügt ein TreeNode-Objekt an der Stelle $index in diese Sammlung ein.
	 *
	 * @return boolean
	 * @param int
	 * @param TreeNode
	 */
	public function setAt( $index, TreeNode $node ) {
		$index = Ensure::uint( $index );
			
		if( $index>count( $this->_implementation ) ) {
			return( false );
		}
			
		// Alten Knoten entfernen
		$this->removeAt( $index );
			
		$this->_implementation[$index] = $node;
		$node->moved( $index, $this->_owner );
			
		return( true );
	}

	/**
	 * Fügt ein TreeNode-Objekt an das Ende dieser Sammlung ein.
	 *
	 * @return boolean
	 * @param TreeNode
	 */
	public function add( TreeNode $node ) {
		$index = count( $this->_implementation );
		$this->_implementation[$index] = $node;
		$node->moved( $index, $this->_owner );
	}

	/**
	 * Entfernt den Knoten an der übergebenen Position aus dieser Sammlung.
	 *
	 * @return boolean
	 * @param int
	 */
	public function removeAt( $index ) {
		if( !isset( $this->_implementation[$index] ) ) {
			return( false );
		}
			
		$node = $this->_implementation[$index];
		unset( $this->_implementation[$index] );
			
		// Index neu durchnummerieren
		$this->_implementation = array_values( $this->_implementation );
		for( $i=$index, $n=count( $this->_implementation ); $i<$n; $i++ ) {
			$this->_implementation[$i]->moved( $i );
		}
			
		$node->leaveAlone();
			
		return( true );
	}

	/**
	 * Diese Methode gibt das TreeNode-Objekt zurück, welches einen Index
	 * nach dem übergebenen Index in dieser Sammlung gesetzt ist.
	 * Ist dieser Index nicht vorhanden, wird NULL zurückgegeben.
	 *
	 * @return TreeNode
	 * @param int
	 */
	public function getNext( $index ) {
		return( $this->getAt( Ensure::uint( $index )+1 ) );
	}

	/**
	 * Diese Methode fügt das TreeNode-Objekt einen Index nach dem
	 * übergebenen Index in diese Sammlung ein.
	 *
	 * Ist an dieser Stelle bereits ein Objekt vorhanden, so wird dieses
	 * aus dieser Sammlung entfernt.
	 *
	 * @return boolean
	 * @param int
	 * @param TreeNode Der einzufügende Knoten
	 */
	public function setNext( $index, TreeNode $node ) {
		$index = Ensure::uint( $index );
			
		// Index ist ungültig
		if( !isset( $this->_implementation[$index] ) ) {
			return( false );
		}
			
		$index++;
		return( $this->setAt( $index, $node ) );
	}

	/**
	 * Diese Methode gibt das TreeNode-Objekt zurück, welches einen Index
	 * vor dem übergebenen Index in dieser Sammlung gesetzt ist.
	 * Ist dieser Index nicht vorhanden, wird NULL zurückgegeben.
	 *
	 * @return TreeNode
	 * @param int
	 */
	public function getPrevious( $index ) {
		return( $this->getAt( Ensure::uint( $index )-1 ) );
	}

	/**
	 * Diese Methode fügt das TreeNode-Objekt einen Index vor dem
	 * übergebenen Index in diese Sammlung ein.
	 *
	 * Ist an dieser Stelle bereits ein Objekt vorhanden, so wird dieses
	 * aus dieser Sammlung entfernt.
	 *
	 * @return boolean
	 * @param int
	 * @param TreeNode Der einzufügende Knoten
	 */
	public function setPrevious( $index, TreeNode $node ) {
		$index = Ensure::uint( $index );
			
		// Index ist ungültig
		if( !isset( $this->_implementation[$index] ) ) {
			return( false );
		}
			
		$index--;
		return( $this->setAt( $index, $node ) );
	}

	/**
	 * Gibt die Anzahl Knoten in dieser Sammlung zurück.
	 *
	 * @return int
	 */
	public function getCount() {
		return( count( $this->_implementation ) );
	}

}

?>