<?php

/**
 * Curly_Types_Collection_Interface
 * 
 * Interface für ein Collection-Objekt.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types
 * @since 1.0 - 15.03.2008
 * @version 1.1
 */
interface Curly_Types_Collection_Interface extends Curly_Types_Collection_Readonly_Interface {
	
	/**
	 * Fügt diesem Collection-Objekt eine Sammlung von Daten hinzu.
	 *
	 * @return void
	 * @param mixed Die anzufügenden Daten
	 * @param boolean Wird dieses Flag auf true gesetzt, werden die Indizes
	 *  ignoriert und die Datensätze einzeln angefügt.
	 */
	public function mergeWith( $data, $ignoreIndizes=false );
	
	/**
	 * Tauscht den Inhalt dieser Instanz aus.
	 * 
	 * @return void
	 * @param mixed
	 */
	public function exchangeArray( $data );
	
	/**
	 * Verwirft den Inhalt dieser Collection.
	 *
	 * @return void
	 */
	public function clear();
	
	/**
	 * Gibt den Wert zu dem übergebenen Schlüssel als Referenz zurück.
	 * Ist der Wert noch nicht vorhanden, so wird der Wert $default
	 * zurückgegeben.
	 * 
	 * @return mixed&
	 * @param mixed&
	 */
	public function &getRef( $offset, &$default=null );
	
	/**
	 * Setzt den Wert zu dem übergebenen Schlüssel.
	 * 
	 * @return mixed
	 * @param mixed
	 * @param mixed
	 */
	public function set( $offset, $value );
	
	/**
	 * Setzt den Wert zu dem übergebenen Schlüssel als Referenz.
	 * 
	 * @return mixed&
	 * @param mixed
	 * @param mixed&
	 */
	public function &setRef( $offset, &$value );
	
	/**
	 * Entfernt den übergebenen Schlüssel aus dieser Collection.
	 * 
	 * @return void
	 * @param mixed
	 */
	public function remove( $offset );
	
	/**
	 * Fügt dieser Collection einen neuen Wert hinzu.
	 * 
	 * @return void
	 * @param mixed
	 */
	public function append( $value );
	
	/**
	 * Fügt ein Element an diese Collection an.
	 * 
	 * @return Curly_Collection
	 * @param mixed
	 */
	public function push($value);
	
	/**
	 * Entfernt das letzte Element aus dieser Collection und gibt es zurück. Ist
	 * die Collection leer, wird NULL zurückgegeben.
	 * 
	 * @return mixed
	 */
	public function pop();
	
	/**
	 * Fügt ein Element vorne an diese Collection an.
	 * 
	 * @return Curly_Collection
	 * @param mixed
	 */
	public function unshift($value);
	
	/**
	 * Entfernt ein Element vorne von dieser Collection.
	 * 
	 * @return mixed
	 */
	public function shift();
	
	/**
	 * Wendet auf jedes Element dieser Collection die übergebene
	 * Callback-Funktion an.
	 * 
	 * @see http://php.net/manual/function.array-walk.php
	 * @return Curly_Types_Collection
	 * @param callback
	 */
	public function walk($callback, $userdata=NULL);
	
}