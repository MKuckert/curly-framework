<?php

require_once "Curly/Event/Parameter.php";

/**
 * CurlyX_Xml_Parser_Event
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Xml
 * @since 1.0 - 05.12.2008
 * @version 1.0
 */
class CurlyX_Xml_Parser_Event extends Curly_Event_Parameter {
	
	/**
	 * @var array Verfügbare Parameter
	 */
	private $_availableParams=array();
	
	/**
	 * @var string Der Name des aktuellen Elements
	 */
	public $name='';
	
	/**
	 * @var array Die Attribute des aktuellen Elements
	 */
	public $attributes=array();
	
	/**
	 * @var string Zeichendaten
	 */
	public $data='';
	
	/**
	 * @var string Target einer ProcessingInstruction
	 */
	public $target='';
	
	/**
	 * @var string Der Name einer Entität
	 */
	public $entityName='';
	
	/**
	 * @var string Die Basis einer Entität
	 */
	public $base='';
	
	/**
	 * @var string Die System-ID im aktuellen Kontext
	 */
	public $systemID='';
	
	/**
	 * @var string Die öffentliche ID im aktuellen Kontext
	 */
	public $publicID='';
	
	/**
	 * @var string Der Name einer Notation
	 */
	public $notationName='';
	
	/**
	 * @var unknown Offene Entitätennamen
	 * @todo Welcher Datentyp?
	 */
	public $openEntityNames;
	
	/**
	 * @var unknown
	 * @todo Welcher Datentyp?
	 */
	public $userData;
	
	/**
	 * @var string Präfix eines Namensraumes
	 */
	public $prefix;
	
	/**
	 * @var string URI eines Namensraumes
	 */
	public $uri;
	
	/**
	 * Gibt ein Array mit den verfügbaren Parametern zurück.
	 * 
	 * @return array
	 */
	public function getAvailableParams() {
		return($this->_availableParams);
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param mixed Objektinstanz, von der dieses Event ausgelöst wurde.
	 * @param array In diesem Event verfügbare Parameter
	 */
	public function __construct($source, array $params) {
		parent::__construct($source);
		
		foreach($params as $prop) {
			if($prop[0]!='_' and property_exists($this, $prop)) {
				$this->_availableParams[]=$prop;
			}
		}
	}
	
}