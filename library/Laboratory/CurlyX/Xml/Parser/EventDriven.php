<?php

require_once "CurlyX/Xml/Parser/Abstract.php";
require_once "CurlyX/Xml/Parser/EventDriven/Dispatcher.php";

/**
 * CurlyX_Xml_Parser_EventDriven
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Xml
 * @since 1.0 - 05.12.2008
 * @version 1.0
 */
class CurlyX_Xml_Parser_EventDriven extends CurlyX_Xml_Parser_Abstract implements Curly_Event_Dispatcher_Interface {
	
	const EVENT_START_ELEMENT='StartElement';
	const EVENT_END_ELEMENT='EndElement';
	const EVENT_CDATA='Cdata';
	const EVENT_PROCESSING_INSTR='ProcessingInstruction';
	const EVENT_DEFAULT='Default';
	const EVENT_UNPARSED_ENTITY_DECL='UnparsedEntityDeclaration';
	const EVENT_NOTATION_DECL='NotationDeclaration';
	const EVENT_EXT_ENTITY_REF='ExternalEntityReference';
	const EVENT_START_NS_DECL='StartNamespaceDeclaration';
	
	/**
	 * @var Curly_Event_Dispatcher_Interface Die intern verwendende Event-Dispatcher-Instanz
	 */
	private $_dispatcher=NULL;
	
	/**
	 * Gibt die intern verwendete Event-Dispatcher-Instanz zurück.
	 * 
	 * @return Curly_Event_Dispatcher_Interface
	 */
	public function getEventDispatcher() {
		if($this->_dispatcher==NULL) {
			$this->_dispatcher=$this->createDefaultEventDispatcher();
		}
		return($this->_dispatcher);
	}
	
	/**
	 * Setzt die intern verwendete Event-Dispatcher-Instanz.
	 * 
	 * @return void
	 * @param Curly_Event_Dispatcher_Interface
	 */
	public function setEventDispatcher(Curly_Event_Dispatcher_Interface $dispatcher) {
		$this->_dispatcher=$dispatcher;
	}
	
	/**
	 * Erstellt eine Standard-Event-Dispatcher-Instanz.
	 * 
	 * @return Curly_Event_Dispatcher_Interface
	 */
	protected function createDefaultEventDispatcher() {
		return(new CurlyX_Xml_Parser_EventDriven_Dispatcher());
	}

	/**
	 * Fügt diesem Ereignisauslöser den Callback $callback für das Event
	 * $type hinzu.
	 *
	 * @return void
	 * @param string
	 * @param callback
	 */
	public function addEventListener($type, $callback) {
		$this->getEventDispatcher()->addEventListener($type, $callback);
	}

	/**
	 * Löst das Ereignis vom Typ $type aus. Dadurch werden alle
	 * registrierten Callbacks aufgerufen und diesem die EventParameter-
	 * Instanz $event übergeben.
	 *
	 * @return void
	 * @param string
	 * @param EventParameter
	 */
	public function dispatchEvent($type, Curly_Event_Parameter_Interface $event=null) {
		$this->getEventDispatcher()->dispatchEvent($type, $event);
	}

	/**
	 * Überprüft, ob zu dem Ereignis vom Typ $type Callbacks registriert
	 * sind und gibt entsprechend einen boolschen Wert zurück.
	 *
	 * @return boolean
	 * @param string
	 */
	public function hasEventListener($type) {
		return($this->getEventDispatcher()->hasEventListener($type));
	}
	
	/**
	 * Entfernt den Callback $callback von dem Ereignis vom Typ $type.
	 *
	 * @return void
	 * @param string
	 * @param callback
	 */
	public function removeEventListener($type, $callback) {
		$this->getEventDispatcher()->removeEventListener($type, $callback);
	}
	
	/**
	 * Erstellt eine Event-Instanz
	 * 
	 * @return CurlyX_Xml_Parser_Event
	 * @param array Eventparameter
	 */
	protected function createEvent($attributes) {
		$event=new CurlyX_Xml_Parser_Event($this, array_keys($attributes));
		
		foreach($attributes as $key=>$value) {
			$event->$key=$value;
		}
		
		return($event);
	}
	
	/**
	 * @return void
	 * @param resource
	 * @param string
	 * @param array
	 */
	public function _startElementHandler($parser, $name, array $attribs) {
		$this->dispatchEvent(self::EVENT_START_ELEMENT, $this->createEvent(array(
			'name'=>$name,
			'attributes'=>$attribs
		)));
	}
	
	/**
	 * @return void
	 * @param resource
	 * @param string
	 */
	public function _endElementHandler($parser, $name) {
		$this->dispatchEvent(self::EVENT_END_ELEMENT, $this->createEvent(array(
			'name'=>$name
		)));
	}
	
	/**
	 * @return void
	 * @param resource
	 * @param string
	 */
	public function _cdataHandler($parser, $data) {
		$this->dispatchEvent(self::EVENT_CDATA, $this->createEvent(array(
			'data' => $data
		)));
	}
	
	/**
	 * @return void
	 * @param resource
	 * @param string
	 * @param string
	 */
	public function _processingInstructionHandler($parser, $target, $data) {
		$this->dispatchEvent(self::EVENT_PROCESSING_INSTR, $this->createEvent(array(
			'target' => $target,
			'data' => $data
		)));
	}
	
	/**
	 * @return void
	 * @param string
	 */
	public function _defaultHandler($parser, $data) {
		$this->dispatchEvent(self::EVENT_DEFAULT, $this->createEvent(array(
			'data' => $data
		)));
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
		$this->dispatchEvent(self::EVENT_UNPARSED_ENTITY_DECL, $this->createEvent(array(
			'entityName' => $entityName,
			'base' => $base,
			'systemID' => $systemID,
			'publicID' => $publicID,
			'notationName' => $notationName
		)));
	}
	
	/**
	 * @return void
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function _notationDeclarationHandler($parser, $notationName, $base, $systemID, $publicID) {
		$this->dispatchEvent(self::EVENT_NOTATION_DECL, $this->createEvent(array(
			'notationName' => $notationName,
			'base' => $base,
			'systemID' => $systemID,
			'publicID' => $publicID
		)));
	}
	
	/**
	 * @return void
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function _externalEntityReferenceHandler($parser, $openEntityNames, $base, $systemID, $publicID) {
		$this->dispatchEvent(self::EVENT_EXT_ENTITY_REF, $this->createEvent(array(
			'openEntityNames' => $openEntityNames,
			'base' => $base,
			'systemID' => $systemID,
			'publicID' => $publicID
		)));
	}
	
	/**
	 * @return void
	 * @param string
	 * @param string
	 * @param string
	 */
	public function _startNamespaceDeclarationHandler($parser, $prefix, $uri) {
		$this->dispatchEvent(self::EVENT_START_NS_DECL, $this->createEvent(array(
			//'userData' => $userData,
			'prefix' => $prefix,
			'uri' => $uri
		)));
	}
	
}