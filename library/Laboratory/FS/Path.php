<?php

/**
 * FS_Path
 * 
 * Stellt einen Pfad zu einem Knoten in einer Baumstruktur dar.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS
 * @since 1.0 - 31.05.2009
 * @version 1.0
 */
class FS_Path implements ArrayAccess, IteratorAggregate, Countable {
	
	/**
	 * @desc Separatorzeichen
	 */
	const SEPARATOR='/';
	
	/**
	 * @desc Alternatives Separatorzeichen
	 */
	const ALT_SEPARATOR='\\';
	
	/**
	 * @var array Die einzelnen Pfadkomponenten
	 */
	private $_components=array();
	
	/**
	 * Gibt die einzelnen Pfadkomponenten als Array zurück.
	 * 
	 * @return array
	 */
	public function getComponents() {
		return $this->_components;
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param string|array Pfad oder einzelne -komponenten
	 */
	public function __construct($path) {
		if(is_array($path)) {
			$path=array_map('strval', $path);
			$this->_components=array_values($path);
		}
		else {
			$path=strval($path);
			$regex='/'.preg_quote(self::SEPARATOR, '/').'|'.preg_quote(self::ALT_SEPARATOR, '/').'/i';
			$path=preg_split($regex, $path, -1, PREG_SPLIT_NO_EMPTY);
			$this->_components=$path;
		}
	}
	
	/**
	 * Statische Methode zum Erstellen eines Pfad-Objektes.
	 * 
	 * @return FS_Path
	 * @param string|array Pfad oder einzelne -komponenten
	 */
	static public function create($path) {
		return new FS_Path($path);
	}
	
	/**#@+
	 * Konvertiert diese Instanz in eine Zeichenkette.
	 * 
	 * @return string
	 */
	public function toString() {
		$c=$this->_components;
		return
			array_shift($c)
			.self::SEPARATOR
			.self::SEPARATOR
			.implode(self::SEPARATOR, $c);
	}
	public function __toString() {
		return $this->toString();
	}
	/**#@-*/
	
	/**
	 * Entfernt die hinterste Pfadkomponente.
	 * 
	 * @return string
	 */
	public function pop() {
		return array_pop($this->_components);
	}
	
	/**
	 * Gibt einen Iterator auf die Komponenten dieses Pfades zurück.
	 * 
	 * @return Iterator
	 */
	public function getIterator() {
		return new ArrayIterator($this->_components);
	}
	
	/**#@+
	 * Methode für das ArrayAccess-Interface.
	 */
	/**
	 * @return boolean
	 * @param integer
	 */
	public function offsetExists($offset) {
		return isset($this->_components[$offset]);
	}
	/**
	 * @throws OutOfBoundsException
	 * @return string
	 * @param integer
	 */
	public function offsetGet($offset) {
		if(!isset($this->_components[$offset])) {
			throw new OutOfBoundsException('Offset '.$offset.' does not exist');
		}
		return $this->_components[$offset];
	}
	/**
	 * @throws OutOfBoundsException
	 * @return void
	 * @param integer
	 * @param string
	 */
 	public function offsetSet($offset, $value) {
		$count=count($this->_components);
		if($offset===NULL) {
			$offset=$count;
		}
		if($offset<0) {
			throw new OutOfBoundsException('Offset '.$offset.' has to be a positiv or zero value');
		}
		else if($offset>$count) {
			throw new OutOfBoundsException('Offset '.$offset.' has to be smaller or equal the component count');
		}
		
		$this->_components[$offset]=strval($value);
	}
	/**
	 * @return void
	 * @param integer
	 */
 	public function offsetUnset($offset) {
		unset($this->_components[$offset]);
		// Neu nummerieren
		$this->_components=array_values($this->_components);
	}
	/**#@-*/
	
	/**
	 * Gibt die Anzahl Pfadkomponenten zurück.
	 * 
	 * @return integer
	 */
	public function count() {
		return count($this->_components);
	}
	
}