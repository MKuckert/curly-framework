<?php
	
	/**
	 * FileMode
	 * Gibt an, wie das Betriebssystem eine Datei öffnen soll.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	class FileMode {
		
		/**
		 * Positioniert den Dateizeiger auf das Ende der Datei. Wenn die Datei
		 * nicht existiert, wird versucht sie anzulegen.
		 * Kann mit FileAccess.Write und FileAccess.ReadWrite kombiniert
		 * werden.
		 */
		const Append = 1;
		
		/**
		 * Gibt an, dass eine neue Datei erstellt werden soll. Wenn die Datei
		 * bereits vorhanden ist, wird diese überschrieben.
		 * Kann mit FileAccess.Write und FileAccess.ReadWrite kombiniert
		 * werden.
		 */
		const Create = 2;
		
		/**
		 * Gibt an, dass eine neue Datei erstellt werden soll. Wenn die Datei
		 * bereits vorhanden ist, wird eine IOException ausgelöst
		 * Kann mit FileAccess.Write und FileAccess.ReadWrite kombiniert
		 * werden.
		 */
		const CreateNew = 3;
		
		/**
		 * Gibt an, dass eine vorhandene Datei geöffnet werden soll. Die
		 * Möglichkeit, die Datei zu öffnen, hängt vom durch FileAccess
		 * angegebenen Wert ab. Wenn die Datei nicht vorhanden ist, wird eine
		 * FileNotFoundException ausgelöst.
		 * Kann mit FileAccess.Read, FileAccess.Write und FileAccess.ReadWrite
		 * kombiniert werden.
		 */
		const Open = 4;
		
		/**
		 * Gibt an, dass eine vorhandene Datei geöffnet werden soll, sofern
		 * diese vorhanden ist, oder andernfalls eine neue Datei erstellen
		 * soll.
		 * Kann mit FileAccess.Write und FileAccess.ReadWrite kombiniert
		 * werden.
		 */
		const OpenOrCreate = 5;
		
		/**
		 * Gibt an, dass eine vorhandene Datei geöffnet werden soll. Nachdem
		 * die Datei geöffnet wurde, sollte diese abgeschnitten werden, sodass
		 * ihre Größe 0 Bytes beträgt.
		 * Kann nur mit FileAccess.Write kombiniert werden.
		 */
		const Truncate = 6;
		
	}
	
?>