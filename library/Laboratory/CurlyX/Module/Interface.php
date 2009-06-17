<?php

/**
 * CurlyX_Module_Interface
 * 
 * Interface, welches von allen Module-Klassen zu implementieren ist.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Module
 * @since 1.0 - 17.12.2008
 * @version 1.0
 */
interface CurlyX_Module_Interface {
	
	/**
	 * Wird bei dem Startup dieses Modules ausgeführt, d.h. kurz nach der
	 * Instanziierung.
	 * 
	 * Die Nutzung dieser Methode sollte dem Konstruktor vorgezogen werden, da
	 * zum Zeitpunkt der Auslösung dieses Systems bereits alle Basiskomponenten
	 * initialisiert sein sollten.
	 * 
	 * @return void
	 */
	public function startUp();
	
	/**
	 * Wird ausgeführt, nachdem ein Update für dieses Module installiert wurde.
	 * 
	 * @return void
	 */
	public function afterUpdate();
	
}
