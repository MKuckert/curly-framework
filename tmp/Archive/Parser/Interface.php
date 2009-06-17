<?php

/**
 * CurlyX_Archive_Parser_Interface
 *
 * Basis-Interface für einen Archiv-Parser.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Parser
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
interface CurlyX_Archive_Parser_Interface {
	
	/**
	 * Parst das Archiv mit der übergebenen URL. Diese Methode extrahiert alle
	 * Basisinformationen aus dem Archiv, damit Aufrufe der
	 * {@link load}-Methode folgen können.
	 *
	 * @return boolean
	 * @param string
	 */
	public function parse( $file );
	
	/**
	 * Lädt den Eintrag mit dem übergebenen Namen aus dem Archiv und gibt den
	 * zugehörigen Wert zurück.
	 *
	 * @return string
	 * @param string
	 */
	public function load( $name );
	
	/**
	 * Gibt einen boolschen Wert zurück, ob die Namen der verfügbaren Einträge
	 * im zurückgegeben werden kann. Dies ist zum Beispiel nicht der Fall, wenn
	 * die Datei verschlüsselt ist.
	 *
	 * @return boolean
	 */
	public function canGetEntryNames();
	
	/**
	 * Gibt ein Array mit den Namen der verfügbaren Einträge zurück.
	 *
	 * @return array
	 */
	public function getEntryNames();
	
}