<?php

/**
 * FS_Visitor_FindNode
 * 
 * Stellt einen Besucher dar, welcher alle Knoten einer Baumstruktur besucht
 * und den Knoten zu dem übergebenen Pfad ermittelt.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Visitor
 * @since 1.0 - 31.05.2009
 * @version 1.0
 */
class FS_Visitor_FindNode implements FS_Visitor_Interface {
	
	/**
	 * @var FS_Path Der zu suchende Pfad.
	 */
	protected $path;
	
	/**
	 * @var integer Die aktuell zu suchende Pfadkomponente.
	 */
	protected $comp=0;
	
	/**
	 * @var FS_Node Der gefundene Knoten.
	 */
	protected $node=NULL;
	
	/**
	 * Konstruktor
	 * 
	 * @param FS_Path|string|path Der zu suchende Pfad
	 */
	public function __construct($path) {
		if(!($path instanceof FS_Path)) {
			$path=new FS_Path($path);
		}
		$this->path=$path;
	}
	
	/**
	 * Überprüft, ob die Suche erfolgreich war.
	 * 
	 * @return boolean
	 */
	public function wasSuccessful() {
		return $this->node!==NULL;
	}
	
	/**
	 * Gibt den gefundenen Knoten zurück.
	 * 
	 * @return FS_Node
	 */
	public function getFoundNode() {
		return $this->node;
	}
	
	/**
	 * Wird von dem übergebenen Knoten aufgerufen, während dieser Besucher die
	 * Knoten der Baumstruktur besucht.
	 * 
	 * @return boolean Flag, ob dieser Besucher die Unterknoten des übergebenen
	 *  Knotens besuchen möchte.
	 * @param FS_Node
	 */
	public function visitNode(FS_Node $node) {
		// Knoten gefunden
		if($node->getName()==$this->path[$this->comp]) {
			$this->comp++;
			// Ist letzte Komponente
			if($this->comp>=$this->path->count()) {
				$this->node=$node;
				return false;
			}
			// Ist nicht letzte Komponente
			else {
				return true;
			}
		}
		// Falscher Knoten
		else {
			return false;
		}
	}
	
}