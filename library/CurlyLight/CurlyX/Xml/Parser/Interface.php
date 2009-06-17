<?php

/**
 * CurlyX_Xml_Parser_Interface
 *
 * Basis-Interface für einen Xml-Parser, der der Simple API for XML (SAX) folgt.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Xml.Parser
 * @since 1.0 - 05.12.2008
 * @version 1.0
 */
interface CurlyX_Xml_Parser_Interface {

	/**#@+
	 * Mögliche Optionen, die mit getOption und setOption beeinflusst werden
	 * können.
	 */
	const OPTION_CASE_FOLDING=XML_OPTION_CASE_FOLDING;
	const OPTION_TARGET_ENCODING=XML_OPTION_TARGET_ENCODING;
	const OPTION_SKIP_START=XML_OPTION_SKIP_TAGSTART;
	const OPTION_SKIP_WHITE=XML_OPTION_SKIP_WHITE;
	/**#@-*/

	/**#@+
	 * Mögliche Encoding-Werte
	 */
	const ENCODING_ISO88591='ISO-8859-1';
	const ENCODING_USASCII='US-ASCII';
	const ENCODING_UTF8='UTF-8';
	/**#@-*/

	/**
	 * Gibt einen Optionswert zurück.
	 *
	 * @return mixed
	 * @param integer
	 */
	public function getOption($option);

	/**
	 * Setzt einen Optionswert,
	 *
	 * @return void
	 * @param integer
	 * @param mixed
	 */
	public function setOption($option, $value);

	/**
	 * Parst die übergebene Zeichenkette als XML.
	 *
	 * @throws CurlyX_Xml_Parser_Exception Wenn ein Fehler während des parsens
	 *  auftritt
	 * @return boolean
	 * @param string Die Eingabezeichenkette
	 * @param boolean Flag, ob diese Daten die letzten Daten waren.
	 */
	public function parse($input, $isFinal=false);
	
}