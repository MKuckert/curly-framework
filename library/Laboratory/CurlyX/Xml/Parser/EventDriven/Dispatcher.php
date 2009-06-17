<?php

require_once "Curly/Event/Dispatcher.php";

/**
 * CurlyX_Xml_Parser_EventDriven_Dispatcher
 * 
 * Event-Dispatcher für die CurlyX_Xml_Parser_EventDriven-Klasse
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Xml
 * @since 1.0 - 09.12.2008
 * @version 1.0
 */
class CurlyX_Xml_Parser_EventDriven_Dispatcher extends Curly_Event_Dispatcher {
	
	/**
	 * @return void
	 * @param CurlyX_Xml_Parser_Event
	 */
	public function onStartElement(CurlyX_Xml_Parser_Event $event) {
		return(true);
	}
	
	/**
	 * @return void
	 * @param CurlyX_Xml_Parser_Event
	 */
	public function onEndElement(CurlyX_Xml_Parser_Event $event) {
		return(true);
	}
	
	/**
	 * @return void
	 * @param CurlyX_Xml_Parser_Event
	 */
	public function onCdata(CurlyX_Xml_Parser_Event $event) {
		return(true);
	}
	
	/**
	 * @return void
	 * @param CurlyX_Xml_Parser_Event
	 */
	public function onProcessingInstruction(CurlyX_Xml_Parser_Event $event) {
		return(true);
	}
	
	/**
	 * @return void
	 * @param CurlyX_Xml_Parser_Event
	 */
	public function onDefault(CurlyX_Xml_Parser_Event $event) {
		return(true);
	}
	
	/**
	 * @return void
	 * @param CurlyX_Xml_Parser_Event
	 */
	public function onUnparsedEntityDeclaration(CurlyX_Xml_Parser_Event $event) {
		return(true);
	}
	
	/**
	 * @return void
	 * @param CurlyX_Xml_Parser_Event
	 */
	public function onNotationDeclaration(CurlyX_Xml_Parser_Event $event) {
		return(true);
	}
	
	/**
	 * @return void
	 * @param CurlyX_Xml_Parser_Event
	 */
	public function onExternalEntityReference(CurlyX_Xml_Parser_Event $event) {
		return(true);
	}
	
	/**
	 * @return void
	 * @param CurlyX_Xml_Parser_Event
	 */
	public function onStartNamespaceDeclaration(CurlyX_Xml_Parser_Event $event) {
		return(true);
	}
	
	/**
	 * @return void
	 * @param CurlyX_Xml_Parser_Event
	 */
	public function onEndNamespaceDeclaration(CurlyX_Xml_Parser_Event $event) {
		return(true);
	}
	
}