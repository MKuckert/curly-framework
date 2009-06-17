<?php

require_once "CurlyX/Xml/Parser/Exception.php";
require_once "CurlyX/Xml/Parser/Interface.php";

/**
 * CurlyX_Xml_Parser_Abstract
 *
 * Basis-Implementation des CurlyX_Xml_Parser_Interface auf Basis der
 * XML Parser-Extension von PHP. Die eigentlichen Methoden verfügen über keine
 * Funktion, es wird ausschließlich die Basisfunktionalität der Extension
 * adaptiert.
 * 
 * Es gibt einen Bug in der PHP-XMLParser-Erweiterung. Bei diesem wird die
 * EndNamespaceDeclaration-Funktion unter der libxml nicht aufgerufen. Daher
 * wurde diese nicht in diese Klasse aufgenommen.
 * Der Bug ist {@link http://bugs.php.net/bug.php?id=30834 hier} einzusehen.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Xml
 * @since 1.0 - 05.12.2008
 * @version 1.0
 */
abstract class CurlyX_Xml_Parser_Abstract implements CurlyX_Xml_Parser_Interface {
	
	const OPTION_ERRORMODE=200;
	
	const ERRORMODE_SILENT=1;
	const ERRORMODE_WARNINGS=2;
	const ERRORMODE_EXCEPTIONS=3;
	
	/**#@+
	 * Ein möglicher Fehlerwert, der von der {@link getErrorCode}-Methode
	 * zurückgegeben wird.
	 */
	const ERROR_NONE=XML_ERROR_NONE;
	const ERROR_NO_MEMORY=XML_ERROR_NO_MEMORY;
	const ERROR_SYNTAX=XML_ERROR_SYNTAX;
	const ERROR_NO_ELEMENTS=XML_ERROR_NO_ELEMENTS;
	const ERROR_INVALID_TOKEN=XML_ERROR_INVALID_TOKEN;
	const ERROR_UNCLOSED_TOKEN=XML_ERROR_UNCLOSED_TOKEN;
	const ERROR_PARTIAL_CHAR=XML_ERROR_PARTIAL_CHAR;
	const ERROR_TAG_MISMATCH=XML_ERROR_TAG_MISMATCH;
	const ERROR_DUPLICATE_ATTRIBUTE=XML_ERROR_DUPLICATE_ATTRIBUTE;
	const ERROR_JUNK_AFTER_DOC_ELEMENT=XML_ERROR_JUNK_AFTER_DOC_ELEMENT;
	const ERROR_PARAM_ENTITY_REF=XML_ERROR_PARAM_ENTITY_REF;
	const ERROR_UNDEFINED_ENTITY=XML_ERROR_UNDEFINED_ENTITY;
	const ERROR_RECURSIVE_ENTITY_REF=XML_ERROR_RECURSIVE_ENTITY_REF;
	const ERROR_ASYNC_ENTITY=XML_ERROR_ASYNC_ENTITY;
	const ERROR_BAD_CHAR_REF=XML_ERROR_BAD_CHAR_REF;
	const ERROR_BINARY_ENTITY_REF=XML_ERROR_BINARY_ENTITY_REF;
	const ERROR_ATTRIBUTE_EXTERNAL_ENTITY_REF=XML_ERROR_ATTRIBUTE_EXTERNAL_ENTITY_REF;
	const ERROR_MISPLACED_XML_PI=XML_ERROR_MISPLACED_XML_PI;
	const ERROR_UNKNOWN_ENCODING=XML_ERROR_UNKNOWN_ENCODING;
	const ERROR_INCORRECT_ENCODING=XML_ERROR_INCORRECT_ENCODING;
	const ERROR_UNCLOSED_CDATA_SECTION=XML_ERROR_UNCLOSED_CDATA_SECTION;
	const ERROR_EXTERNAL_ENTITY_HANDLING=XML_ERROR_EXTERNAL_ENTITY_HANDLING;
	/**#@-*/
	
	/**
	 * @var resource Die interne XmlParser-Resource
	 */
	private $parser;
	
	/**
	 * @var integer Der aktuelle Errormode
	 */
	private $errorMode=self::ERRORMODE_SILENT;
	
	/**
	 * Gibt einen Optionswert zurück.
	 *
	 * @return mixed
	 * @param integer
	 */
	public function getOption($option) {
		if($option==self::OPTION_ERRORMODE) {
			return($this->errorMode);
		}
		return(xml_parser_get_option($this->parser, $option));
	}

	/**
	 * Setzt einen Optionswert,
	 * 
	 * @throws InvalidArgumentException Wenn die Option nicht vorhanden ist.
	 * @return void
	 * @param integer
	 * @param mixed
	 */
	public function setOption($option, $value) {
		if($option==self::OPTION_ERRORMODE) {
			if(
				$value==self::ERRORMODE_SILENT
				or $value==self::ERRORMODE_WARNINGS
				or $value==self::ERRORMODE_EXCEPTIONS
			) {
				$this->errorMode=(int)$value;
			}
			else {
				throw new InvalidArgumentException('The given errormode value is not valid');
			}
		}
		else if(
			$option==self::OPTION_CASE_FOLDING
			or $option==self::OPTION_SKIP_START
			or $option==self::OPTION_SKIP_WHITE
			or $option==self::OPTION_TARGET_ENCODING
		) {
			xml_parser_set_option($this->parser, $option, $value);
		}
		else {
			throw new InvalidArgumentException('The requested option is not defined');
		}
	}

	/**
	 * Parst die übergebene Zeichenkette als XML.
	 *
	 * @throws CurlyX_Xml_Parser_Exception Wenn ein Fehler während des parsens
	 *  auftritt und die Option OPTION_ERRORMODE auf ERRORMODE_EXCEPTIONS
	 *  eingestellt ist.
	 * @return boolean
	 * @param string Die Eingabezeichenkette
	 * @param boolean Flag, ob diese Daten die letzten Daten waren.
	 */
	public function parse($input, $isFinal=false) {
		if(!xml_parse($this->parser, $input, (boolean)$isFinal)) {
			switch($this->errorMode) {
				case self::ERRORMODE_EXCEPTIONS:
					throw new CurlyX_Xml_Parser_Exception($this->getErrorString($ec=$this->getErrorCode()), $ec);
				case self::ERRORMODE_WARNINGS:
					trigger_error($this->getErrorString(), E_USER_WARNING);
				case self::ERRORMODE_SILENT:
				default:
					return(false);
			}
		}
		return(true);
	}
	
	/**
	 * Konstruktor
	 * 
	 * @throws CurlyX_Xml_Parser_Exception Wenn der Encoding-Parameter ungültig
	 *  ist.
	 * @param string Der zu verwendende Zeichensatzencoding
	 * @param boolean true, wenn Namensraumunterstützung aktiviert werden soll.
	 * @param string Zu verwendendes Separatorzeichen für die
	 *  Namensraumunterstützung.
	 */
	public function __construct($encoding=self::ENCODING_UTF8, $useNS=false, $separator=';') {
		if(!(
			$encoding==self::ENCODING_UTF8
			or $encoding==self::ENCODING_ISO88591
			or $encoding==self::ENCODING_USASCII
		)) {
			throw new CurlyX_Xml_Parser_Exception('The given encoding value is not valid');
		}
		
		if($useNS) {
			$this->parser=xml_parser_create_ns($encoding, $separator);
		}
		else {
			$this->parser=xml_parser_create($encoding);
		}
		
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, array($this, '_startElementHandler'), array($this, '_endElementHandler'));
		xml_set_character_data_handler($this->parser, array($this, '_cdataHandler'));
		xml_set_processing_instruction_handler($this->parser, array($this, '_processingInstructionHandler'));
		xml_set_default_handler($this->parser, array($this, '_defaultHandler'));
		xml_set_unparsed_entity_decl_handler($this->parser, array($this, '_unparsedEntityDeclarationHandler'));
		xml_set_notation_decl_handler($this->parser, array($this, '_notationDeclarationHandler'));
		xml_set_external_entity_ref_handler($this->parser, array($this, '_externalEntityReferenceHandler'));
		xml_set_start_namespace_decl_handler($this->parser, array($this, '_startNamespaceDeclarationHandler'));
		
		$this->setOption(self::OPTION_CASE_FOLDING, false);
		$this->setOption(self::OPTION_TARGET_ENCODING, $encoding);
	}
	
	/**
	 * Destruktor
	 */
	public function __destruct() {
		xml_parser_free($this->parser);
	}
	
	/**
	 * Gibt den zuletzt aufgetretenen Fehlercode zurück.
	 * 
	 * @return integer
	 */
	public function getErrorCode() {
		return(xml_get_error_code($this->parser));
	}
	
	/**
	 * Gibt die Nachricht zu dem übergebenen Fehlercode zurück. Wird kein Wert
	 * übergeben, so wird der zuletzt aufgetretene Fehlercode verwendet.
	 * 
	 * @return string
	 * @param integer
	 */
	public function getErrorString($code=-1) {
		if($code<0) {
			$code=$this->getErrorCode();
		}
		return(xml_error_string($code));
	}
	
	/**
	 * Gibt den aktuellen Index des XML Parsers in Bytes zurück.
	 * 
	 * @return integer
	 */
	public function getCurrentByteIndex() {
		return(xml_get_current_byte_index($this->parser));
	}
	
	/**
	 * Gibt die aktuelle Spaltennummer des XML Parsers zurück.
	 * 
	 * @return integer
	 */
	public function getCurrentColumnNumber() {
		return(xml_get_current_column_number($this->parser));
	}
	
	/**
	 * Gibt die aktuelle Zeilennummer des XML Parsers zurück.
	 * 
	 * @return integer
	 */
	public function getCurrentLineNumber() {
		return(xml_get_current_line_number($this->parser));
	}
	
	/**
	 * @return void
	 * @param resource
	 * @param string
	 * @param array
	 */
	public function _startElementHandler($parser, $name, array $attribs) {
		
	}
	
	/**
	 * @return void
	 * @param resource
	 * @param string
	 */
	public function _endElementHandler($parser, $name) {
		
	}
	
	/**
	 * @return void
	 * @param resource
	 * @param string
	 */
	public function _cdataHandler($parser, $data) {
		
	}
	
	/**
	 * @return void
	 * @param resource
	 * @param string
	 * @param string
	 */
	public function _processingInstructionHandler($parser, $target, $data) {
		
	}
	
	/**
	 * @return void
	 * @param string
	 */
	public function _defaultHandler($parser, $data) {
		
	}
	
	/**
	 * @return void
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function _unparsedEntityDeclarationHandler($parser, $entityName, $base, $systemID, $publicID, $notationName) {
		
	}
	
	/**
	 * @return void
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function _notationDeclarationHandler($parser, $notationName, $base, $systemID, $publicID) {
		
	}
	
	/**
	 * @return void
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function _externalEntityReferenceHandler($parser, $openEntityNames, $base, $systemID, $publicID) {
		
	}
	
	/**
	 * @return void
	 * @param string
	 * @param string
	 * @param string
	 */
	public function _startNamespaceDeclarationHandler($parser, $prefix, $uri) {
		
	}
	
}
