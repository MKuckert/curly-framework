<?php

/**
 * Curly_Event_Parameter_Interface
 * 
 * Diese Klasse dient als Basisklasse für alle Objekte, die einem Ereignis
 * der {@link Curly_Event_Dispatcher_Interface}-Klasse als Parameter übergeben werden
 * können.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Event.Parameter
 * @since 1.0 - 07.05.2008
 * @version 1.0
 */
interface Curly_Event_Parameter_Interface {
	
	/**
	 * Gibt die Objektinstanz zurück, von der dieses Event ausgelöst wurde.
	 * 
	 * @return mixed
	 */
	public function getSource();
	
}

?>