<?php

/**
 * Curly_Types_Callback_Interface
 * 
 * Basisinterface für ein Callback.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types
 * @since 1.0 - 08.03.2009
 * @version 1.0
 */
interface Curly_Types_Callback_Interface {
	
	/**
	 * Diese Methode führt diesen Callback mit den übergebenen
	 * Argumenten aus. Es kann auch anstatt eines Arrays eine Liste von
	 * mehreren Argumenten verwendet werden, allerdings darf der erste
	 * Parameter dann kein Array mehr sein.
	 *
	 * Der Rückgabewert ist der des Callbacks.
	 *
	 * @return mixed
	 * @param array
	 */
	public function invoke( $arguments=array() );
	
	/**
	 * Vergleicht dieses Objekt mit dem übergebenen Objekt und gibt true
	 * zurück, wenn es sich dabei um das gleiche Objekt handelt, d.h. beide
	 * Instanzen auf den gleichen Callback verweisen.
	 *
	 * @return boolean
	 * @param Object
	 */
	public function equals( $that );
	
	/**
	 * Gibt ein Array zurück, welches für Funktionen wie call_user_func
	 * verwendet werden kann, also einen PHP-Callback repräsentiert.
	 * 
	 * @return array
	 */
	public function caller();

}