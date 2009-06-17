<?php

/**
 * CurlyX_Module_Abstract
 * 
 * Abstrakte Basis-Klasse für ein Modul
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Module
 * @since 1.0 - 17.12.2008
 * @version 1.0
 */
abstract class CurlyX_Module_Abstract {
	
	/**
	 * @var boolean Flag, ob dieses Modul bereits initialisiert wurde.
	 */
	
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
	public function startUp() {
		
	}
	
}
