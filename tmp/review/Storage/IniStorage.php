<?php

/**
 * IniStorage
 * Implementiert {@link load Lade}- und {@link store Speicher}-Operationen
 * für die {@link ArrayAdapterStorage}-Klasse auf Basis von INI-Dateien.
 *
 * Über die {@link getFile File}-Eigenschaft kann der Dateiname gesetzt
 * werden, in dem die Speicherdaten abgelegt werden sollen bzw. aus der
 * gelesen wird.
 *
 * Die Speicherdaten werden im Speicher gehalten. Erst durch aufrufen der
 * store-Methode werden alle Daten in die Datei geschrieben.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Storage
 * @since 1.0 - 02.05.2008
 * @version 1.0
 */
class IniStorage {

	/**
	 * @var string Die zu verwendende Datei für Lese- und Schreiboperationen.
	 */
	private $_file = '';

}

?>