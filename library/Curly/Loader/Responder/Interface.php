<?php

/**
 * Curly_Loader_Responder_Interface
 * 
 * Dieses Interface wird von der {@link ClassLoadDispatcher}-Klasse
 * verwendet. Wird eine Klasse geladen, die dieses Interface implementiert,
 * so wird die {@link __loaded}-Methode dieser Klasse aufgerufen.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Loader.Responder
 * @since 1.0 - 08.06.2008
 * @version 1.0
 */
interface Curly_Loader_Responder_Interface {

	/**
	 * Dieser Methode wird von der ClassLoadDispatcher-Klasse beim Laden
	 * der implementierenden Klasse aufgerufen.
	 *
	 * Diese Methode kann dazu verwendet werden, bestimmte Aufgaben bei dem
	 * Laden einer Klasse auszuführen, beispielsweise die Registrierung bei
	 * anderen Klassen oder das Laden weiterer Ressourcen.
	 *
	 * @return void
	 */
	static public function __loaded();

}

?>