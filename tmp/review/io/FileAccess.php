<?php
	
	/**
	 * FileAccess
	 * Gibt an, mit welchen Zugriffsrechten eine Datei geöffnet werden soll
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	class FileAccess {
		
		/**
		 * Öffnet die Datei zum Lesen.
		 */
		const Read = 1;
		
		/**
		 * Öffnet die Datei zum Schreiben.
		 */
		const Write = 2;
		
		/**
		 * Öffnet die Datei zum Lesen und Schreiben.
		 */
		const ReadWrite = 3;
		
	}
	
?>