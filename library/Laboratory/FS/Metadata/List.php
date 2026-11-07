<?php

/**
 * FS_Metadata_List
 * 
 * Stellt eine Liste von Metadaten-Datensätzen bereit.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS.Metadata
 * @since 1.0 - 01.06.2009
 * @version 1.0
 */
class FS_Metadata_List {
	
	/**
	 * @var array Datensätze dieser Liste.
	 */
	private $_datasets=array();
	
	/**
	 * Gibt den Datensatz zu der übergebenen ID zurück.
	 * 
	 * @return FS_Metadata_Interface oder NULL wenn nicht vorhanden
	 * @param integer
	 */
	public function getDataset($id) {
		if(isset($this->_datasets[$id])) {
			return $this->_datasets[$id];
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * Fügt den übergebenen Datensatz zu dieser Liste hinzu.
	 * 
	 * @return FS_Metadata_List
	 * @param FS_Metadata_Interface
	 */
	public function addDataset(FS_Metadata_Interface $dataset) {
		$this->_datasets[$dataset->getID()]=$dataset;
		return $this;
	}
	
	/**
	 * Entfernt den übergebenen Datensatz aus dieser Liste hinzu.
	 * 
	 * @return FS_Metadata_List
	 * @param FS_Metadata_Interface|integer Datensatz oder ID
	 */
	public function removeDataset($id) {
		if($id instanceof FS_Metadata_Interface) {
			$id=$id->getID();
		}
		unset($this->_datasets[$id]);
		return $this;
	}
	
	/**
	 * Gibt den Datensatz des übergebenen Knotens zurück.
	 * 
	 * @return FS_Metadata_Interface oder NULL
	 * @param FS_Node
	 */
	public function getNodeDataset(FS_Node $node) {
		$id=$node->getMetaID();
		if($id===NULL) {
			return NULL;
		}
		return $this->getDataset($id);
	}
	
}