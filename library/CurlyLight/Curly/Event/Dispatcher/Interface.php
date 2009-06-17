<?php

/**
 * Curly_Event_Dispatcher_Interface
 * 
 * Dieses Interface muss von Klassen implementiert werden, die Ereignisse
 * auslösen sollen.
 *
 * Einer EventDispatcher-Klasse können mit Hilfe der Methode
 * {@link addEventListener} der Instanz Callbacks für bestimmte Eventtypen
 * angefügt werden.
 *
 * Ein Ereignis wird durch {@link dispatchEvent} ausgelöst, wodurch alle
 * für dieses Ereignis registrierten Callbacks aufgerufen werden.
 *
 * Mit Hilfe der {@link hasEventListener}-Methode kann überprüft werden, ob
 * zu einem Ereignis Callbacks vorhanden sind.
 *
 * Soll ein Callback nicht länger auf Ereignisse horchen, kann dieser mit
 * der {@link removeEventListener}-Methode wieder von diesem Objekt
 * entfernt werden.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Event.Dispatcher
 * @since 1.0 - 07.05.2008
 * @version 1.0
 */
interface Curly_Event_Dispatcher_Interface {

	/**
	 * Fügt diesem Ereignisauslöser den Callback $callback für das Event
	 * $type hinzu.
	 *
	 * @return void
	 * @param string
	 * @param callback
	 */
	public function addEventListener( $type, $callback );

	/**
	 * Löst das Ereignis vom Typ $type aus. Dadurch werden alle
	 * registrierten Callbacks aufgerufen und diesem die EventParameter-
	 * Instanz $event übergeben.
	 *
	 * @return void
	 * @param string
	 * @param EventParameter
	 */
	public function dispatchEvent( $type, Curly_Event_Parameter_Interface $event = null );

	/**
	 * Überprüft, ob zu dem Ereignis vom Typ $type Callbacks registriert
	 * sind und gibt entsprechend einen boolschen Wert zurück.
	 *
	 * @return boolean
	 * @param string
	 */
	public function hasEventListener( $type );

	/**
	 * Entfernt den Callback $callback von dem Ereignis vom Typ $type.
	 *
	 * @return void
	 * @param string
	 * @param callback
	 */
	public function removeEventListener( $type, $callback );

}

?>