<?php

/**
 * FS_Visitor_Interface
 * 
 * Stellt einen Besucher dar, welcher alle Knoten einer Baumstruktur besucht.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Visitor
 * @since 1.0 - 31.05.2009
 * @version 1.0
 */
interface FS_Visitor_Interface {
	
	/**
	 * Wird von dem übergebenen Knoten aufgerufen, während dieser Besucher die
	 * Knoten der Baumstruktur besucht.
	 * 
	 * @return boolean Flag, ob dieser Besucher die Unterknoten des übergebenen
	 *  Knotens besuchen möchte.
	 * @param FS_Node
	 */
	public function visitNode(FS_Node $node);
	
}