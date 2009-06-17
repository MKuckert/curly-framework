<?php

/**
 * Curly_Types_Collection_Readonly_Interface
 * 
 * Interface für ein Collection-Objekt, aus dem Daten gelesen werden können.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types
 * @since 1.0 - 15.03.2008
 * @version 1.1
 */
interface Curly_Types_Collection_Readonly_Interface extends ArrayAccess, Countable {
	
	/**
	 * Verknüpft alle Elemente in dieser Collection mit der übergebenen Zeichenkette und
	 * gibt ein String zurück.
	 *
	 * @return string
	 * @param string Trennzeichenkette. Standardwert ist ", "
	 */
	public function join( $delimiter=', ' );
	
	/**
	 * Überprüft, ob in dieser Collection der übergebene Schlüssel gesetzt ist.
	 * 
	 * @return boolean
	 * @param mixed
	 */
	public function keyExists( $offset );
	
	/**
	 * Gibt den Wert zu dem übergebenen Schlüssel zurück.
	 * Ist der Wert noch nicht vorhanden, so wird der Wert $default
	 * zurückgegeben.
	 * 
	 * @return mixed
	 * @param mixed
	 */
	public function get( $offset, $default=null );
	
	/**
	 * Gibt das erste Element in der Collection zurück. Ist die Collection leer,
	 * wird NULL zurückgegeben.
	 * 
	 * @return mixed
	 */
	public function first();
	
	/**
	 * Gibt das letzte Element in der Collection zurück. Ist die Collection leer,
	 * wird NULL zurückgegeben.
	 * 
	 * @return mixed
	 */
	public function last();
	
	/**
	 * Gibt den Inhalt dieser Collection als Array zurück.
	 * 
	 * @return array
	 */
	public function toArray();
	
	/**
	 * Gibt den Inhalt dieser Collection als String zurück.
	 * 
	 * @return string
	 */
	public function toString();
	
	/**
	 * Gibt den Inhalt dieser Collection als String zurück.
	 * 
	 * @return string
	 */
	public function __toString();
	
	/**
	 * Wendet auf jedes Element dieser Collection die übergebene
	 * Callback-Funktion an und gibt eine Collection mit den einzelnen
	 * Rückgabewerten zurück.
	 * 
	 * @see http://php.net/manual/function.array-map.php
	 * @return Curly_Types_Collection
	 * @param callback
	 */
	public function map($callback);
	
}