<?php

/**
 * CurlyX_Stream_Wrapper
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Stream
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
abstract class CurlyX_Stream_Wrapper {
	
	/**
	 * @var resource Wrapper-Kontext.
	 */
	public $context=NULL;
	
	/**
	 * Registriert eine Klasse als Streamwrapper.
	 * 
	 * @return boolean
	 * @param string Der Protokoll-Name
	 * @param string Die zu registrierende Klasse
	 */
	static public function register($protocol, $class=__CLASS__) {
		return(stream_wrapper_register($protocol, $class));
	}
	
	/**
	 * Öffnet diesen Wrapper.
	 * 
	 * @return boolean true, wenn der Stream erfolgreich geöffnet wurde.
	 * @param string Der zu verwendende Pfad
	 * @param string Der Modus, mit dem dieser Wrapper geöffnet wurde
	 * @param integer Weitere Optionen als Bitfeld. Folgende Werte können
	 *  enthalten sein:
	 * 	 - STREAM_USE_PATH: Wenn $path relativ ist, soll die
	 * 						include_path-Konfiguration zum Auflösen
	 * 						verwendet werden. Wurde der Stream korrekt
	 * 						geöffnet, soll der 4. Parameter auf den vollen Pfad
	 * 						gesetzt werden.
	 *   - STREAM_REPORT_ERRORS: Wenn dieses Flag gesetzt ist, sollen bei
	 * 						Fehlern mit Hilfe von {@link trigger_error}
	 * 						Fehlermeldungen ausgegeben werden. Sonst nicht.
	 * @param string 
	 */
	public function stream_open($path, $mode, $options, $opened_path) {
		
	}
	
	/**
	 * Schließt diesen StreamWrapper.
	 * 
	 * @return void
	 */
	public function stream_close() {
		
	}
	
	/**
	 * Ließt Maximal $count Bytes aus dem Stream und gibt diese zurück. Die
	 * aktuelle Position im Stream muss ebenfalls aktualisiert werden.
	 * Sind keine Daten mehr vorhanden, muss false oder eine leere Zeichenkette
	 * zurückgegeben werden.
	 * 
	 * @return string
	 * @param integer Maximal zu lesende Bytes
	 */
	public function stream_read($count) {
		
	}
	
	/**
	 * Schreibt die übergebenen Daten in diesen Strom und gibt die Anzahl
	 * geschriebener Bytes zurück. Können keine Daten geschrieben werden, so
	 * muss 0 zurückgegeben werden.
	 * 
	 * @return integer Anzahl geschriebener Bytes
	 * @param string Zu schreibende Daten
	 */
	public function stream_write($data) {
		
	}
	
	/**
	 * Gibt einen boolschen Wert zurück, ob die Lese-/Schreibposition am Ende
	 * des Datenstroms steht.
	 * 
	 * @return boolean
	 */
	public function stream_eof() {
		
	}
	
	/**
	 * Gibt die aktuelle Lese-/Schreibposition zurück.
	 * 
	 * @return integer
	 */
	public function stream_tell() {
		
	}
	
	/**
	 * Ändert die aktuelle Lese-/Schreibposition auf den mit den
	 * Übergabeparametern definierten Wert.
	 * 
	 * @return boolean true bei Erfolg.
	 * @param integer Offset-Wert
	 * @param integer Referenz-Position
	 */
	public function stream_seek($offset, $whence) {
		
	}
	
	/**
	 * Leert alle Datenpuffer.
	 * 
	 * @return boolean true bei Erfolg.
	 */
	public function stream_flush() {
		
	}
	
	/**
	 * Gibt statistische Daten zu diesem Stream zurück.
	 * 
	 * @return array
	 */
	public function stream_stat() {
		
	}
	
	/**
	 * Analoger Aufruf zu der stream_select Methode. Als Rückgabe muss die
	 * unterliegende Stream-Ressource oder false zurückgegeben werden.
	 * 
	 * Hinweis: Diese Methode kann erst ab 5.3.0 aufgerufen werden.
	 * 
	 * @return resource
	 * @param integer Entweder STREAM_CAST_FOR_SELECT, wenn von stream_select
	 *  aufgerufen, sonst STREAM_CAST_AS_STREAM.
	 */
	public function stream_cast($cast_as) {
		
	}
	
	/**
	 * Setzt Optionen an diesem Wrapper.
	 * 
	 * Die Parameter können folgende Werte annehmen:
	 *  - STREAM_OPTION_BLOCKING: Wird von stream_set_blocking aufgerufen.
	 * 						$arg1 ist der gewünschte Blocking-Modus.
	 *  - STREAM_OPTION_READ_TIMEOUT: Wird von stream_set_timeout aufgerufen.
	 * 						$arg1 ist der Timeoutwert in Sekunden, $arg2 in
	 * 						Mikrosekunden.
	 *  - STREAM_OPTION_WRITE_BUFFER: Wird von stream_set_write_buffer
	 * 						aufgerufen. $arg1 ist der Buffer-Modus
	 * 						(STREAM_BUFFER_NONE oder STREAM_BUFFER_FULL) und
	 * 						$arg2 ist die gewünschte Buffergröße.
	 * 
	 * Hinweis: Diese Methode kann erst ab 5.3.0 aufgerufen werden.
	 * 
	 * @return boolean true bei Erfolg, false bei Fehler oder wenn nicht
	 *  implementiert.
	 * @param integer Option
	 * @param integer Argument 1
	 * @param integer Argument 2
	 */
	public function stream_set_option($option, $arg1, $arg2) {
		
	}
	
	/**
	 * Löscht das Objekt mit dem übergebenen Pfad.
	 * 
	 * Soll dieser Wrapper kein Löschen von Elementen unterstützen, so ist diese
	 * Methode zu entfernen.
	 * 
	 * @return boolean true bei Erfolg
	 * @param string
	 */
	public function unlink($path) {
		
	}
	
	/**
	 * Benennt das Objekt mit dem übergebenen Pfad um.
	 * 
	 * Soll dieser Wrapper kein Umbenennen von Elementen unterstützen, so ist
	 * diese Methode zu entfernen.
	 * 
	 * @return boolean true bei Erfolg
	 * @param string
	 * @param string
	 */
	public function rename($path_from, $path_to) {
		
	}
	
	/**
	 * Erstellt ein Containerobjekt mit dem übergebenen Pfad.
	 * 
	 * Soll dieser Wrapper keine Containererzeugung unterstützen, so ist diese
	 * Methode zu entfernen.
	 * 
	 * @return boolean true bei Erfolg
	 * @param string
	 * @param integer Zu verwendenden Modus
	 * @param integer Mögliche Werte sind STREAM_REPORT_ERRORS und
	 *  STREAM_MKDIR_RECURSIVE
	 */
	public function mkdir($path, $mode, $options) {
		
	}
	
	/**
	 * Entfernt ein Containerobjekt unter dem übergebenen Pfad.
	 * 
	 * Soll dieser Wrapper keine Containerentfernung unterstützen, so ist diese
	 * Methode zu entfernen.
	 * 
	 * @return boolean true bei Erfolg
	 * @param string
	 * @param integer Möglicher Wert ist STREAM_REPORT_ERRORS
	 */
	public function rmdir($path, $mode, $options) {
		
	}
	
	/**
	 * Wird nach dem Öffnen dieses Wrappers als Container aufgerufen
	 * 
	 * @return boolean true bei Erfolg
	 * @param string Der verwendete Pfad
	 * @param integer Optionen
	 */
	public function dir_opendir($path, $options) {
		
	}
	
	/**
	 * Gibt statistische Informationen zu diesem Wrapper über einen URL-Aufruf
	 * zurück.
	 * 
	 * @return array Diese Parameter sollten den Standardparametern möglichst
	 *  ähnlich sein.
	 * @param string Der verwendete Pfad
	 * @param integer Flags aus der Stream-API als Bitfeld. Mögliche Werte sind:
	 * 	- STREAM_URL_STAT_LINK: Die Rückgabe sollte ausschließlich Informationen
	 *		zu dem Link enthalten. Dient als Aufrufziel für lstat, is_link und
	 *		filetype.
	 *  - STREAM_URL_STAT_QUIET: Ist dieses Flag gesetzt, sollen keine Fehler
	 *  	ausgelöst werden. Andernfalls sollten Fehler über trigger_error
	 *  	gemeldet werden.
	 */
	public function url_stat($path, $flags) {
		
	}
	
	/**
	 * Ließt den nächsten Namen aus diesem Container. Dient als Ziel für die
	 * readdir-Funktion.
	 * 
	 * @return string
	 */
	public function dir_readdir() {
		
	}
	
	/**
	 * Setzt den internen Zeiger zurück, so dass der nächste dir_readdir-Aufruf
	 * wieder das erste Element zurückgibt. Wird von der rewinddir-Funktion
	 * aufgerufen 
	 * 
	 * @return boolean
	 */
	public function dir_rewinddir() {
		
	}
	
	/**
	 * Schließt diesen Container-Stream. Jegliche Ressourcen sollten freigegeben
	 * werden. Wird von der closedir-Funktion aufgerufen.
	 * 
	 * @return boolean
	 */
	public function dir_closedir() {
		
	}
	
}
