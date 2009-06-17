<?php
	
	/**
	 * File
	 * Stellt statische Methoden zum Erstellen, Kopieren, Löschen, Verschieben
	 * und Öffnen von Dateien bereit und unterstützt das Erstellen von
	 * FileStream-Objekten.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	class File {
		
		/**
		 * Fügt die angegebene Zeichenfolge an die Datei an und erstellt die
		 * Datei, wenn sie nicht bereits vorhanden ist.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws FileNotFoundException Die in $path angegebene Datei wurde
		 *  nicht gefunden.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException $path gibt eine
		 *  schreibgeschützte Datei an oder $path gibt ein Verzeichnis an.
		 * @return void
		 * @param string Die Datei, an die die angegebene Zeichenfolge angefügt
		 *  werden soll.
		 * @param string Die Zeichenfolge, die an die Datei angefügt werden
		 *  soll.
		 */
		static public function appendAllText( $path, $contents ) {
			$stream = $this->appendText( $path );
			$stream->write( $contents );
			$stream->close();
		}
		
		/**
		 * Erstellt einen StreamWriter, der den Text an eine vorhandene Datei
		 * anfügt.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws FileNotFoundException Die in $path angegebene Datei wurde
		 *  nicht gefunden.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException $path gibt eine
		 *  schreibgeschützte Datei an oder $path gibt ein Verzeichnis an.
		 * @return StreamWriter Ein StreamWriter auf die Datei
		 * @param string Der Pfad zu der Datei, an die angefügt wird.
		 */
		static public function appendText( $path ) {
			return( new FileStream( $path, FileMode::Append, FileAccess::Write ) );
		}
		
		/**
		 * Kopiert eine vorhandene Datei in eine neue Datei.
		 * Wird $overwrite auf true gesetzt, wird eine gleichnamige Datei
		 * überschrieben.
		 * 
		 * @throws InvalidArgumentException $sourcePath oder $destPath ist eine
		 *  Zeichenfolge der Länge 0, besteht nur aus Leerraum oder enthält ein
		 *  oder mehr durch {@link Path::getInvalidPathChars} definierte
		 *  ungültige Zeichen.
		 * @throws FileNotFoundException Die in $sourcePath angegebene Datei
		 *  wurde nicht gefunden.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException $destPath gibt eine
		 *  schreibgeschützte Datei an.
		 * @return void
		 * @param string Die zu kopierende Datei. 
		 * @param string Der Name der Zieldatei.
		 */
		static public function copy( $sourcePath, $destPath, $overwrite=false ) {
			
		}
		
		/**
		 * Erstellt oder überschreibt eine Datei im angegebenen Pfad.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws FileNotFoundException Der in $path angegebene Pfad wurde
		 *  nicht gefunden.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException $path gibt eine
		 *  schreibgeschützte Datei an oder $path gibt ein Verzeichnis an.
		 * @return FileStream Ein FileStream, der Lese- und Schreibzugriff auf
		 *  die in $path angegebene Datei bereitstellt.
		 * @param string Der Pfad und der Name der zu erstellenden Datei.
		 */
		static public function create( $path ) {
			
		}
		
		/**
		 * Erstellt oder öffnet eine Datei zum Schreiben von Text.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws FileNotFoundException Der in $path angegebene Pfad wurde
		 *  nicht gefunden.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException $path gibt eine
		 *  schreibgeschützte Datei an oder $path gibt ein Verzeichnis an.
		 * @return StreamWriter Ein StreamWriter, der in die angegebene Datei
		 *  schreibt.
		 * @param string Der Pfad und der Name der zu erstellenden Datei.
		 */
		static public function createText( $path ) {
			
		}
		
		/**
		 * Löscht die angegebene Datei. Wenn die angegebene Datei nicht
		 * vorhanden ist, wird keine Ausnahme ausgelöst.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException $path gibt eine
		 *  schreibgeschützte Datei an oder $path gibt ein Verzeichnis an.
		 * @throws FileNotFoundException Die Datei kann nicht gefunden werden.
		 * @return void
		 * @param string Der Name der zu löschenden Datei.
		 */
		static public function delete( $path ) {
			
		}
		
		/**
		 * Bestimmt, ob die angegebene Datei vorhanden ist.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @return boolean true, wenn der Aufrufer über die erforderlichen
		 *  Berechtigungen verfügt und path den Namen einer vorhandenen Datei
		 *  enthält, andernfalls false. Diese Methode gibt auch false zurück,
		 *  wenn für $path NULL, ein ungültiger Pfad oder eine Zeichenfolge der
		 *  Länge 0 festgelegt ist. Wenn ein Aufrufer nicht über ausreichende
		 *  Berechtigungen zum Lesen der angegebenen Datei verfügt, wird keine
		 *  Ausnahme ausgelöst, und die Methode gibt false zurück, unabhängig
		 *  vom Vorhandensein von $path.
		 * @param string Die zu überprüfende Datei.
		 */
		static public function exists( $path ) {
			if( trim( $path )==''
				or
				!is_string( $path )
				or
				!Path::isValidPathName( $path )
			) {
				throw new InvalidArgumentException( 'The given argument contains invalid characters or is empty' );
			}
			
			return( file_exists( $path ) );
		}
		
		/**
		 * Gibt das Datum und die Uhrzeit der letzten Änderung an der
		 * angegebenen Datei bzw. dem angegebenen Verzeichnis zurück.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException Die Informationen können
		 *  aufgrund fehlender Rechte nicht ermittelt werden
		 * @throws FileNotFoundException Die Datei kann nicht gefunden werden.
		 * @return DateTime Zeitpunktobjekt für das angegebene Verzeichnis oder
		 *  die angegebene Datei.
		 * @param string Die Datei oder das Verzeichnis, für die bzw. für das
		 *  die Informationen über Änderungsdatum und -zeitpunkt abgerufen
		 *  werden sollen.
		 */
		static public function getLastModificationTime( $path ) {
			
		}
		
		/**
		 * Verschiebt eine angegebene Datei an einen neuen Speicherort und
		 * ermöglicht das Angeben eines neuen Dateinamens.
		 * Ist die Zieldatei vorhanden, wird diese überschrieben.
		 * 
		 * @throws InvalidArgumentException $sourcePath oder $destPath ist eine
		 *  Zeichenfolge der Länge 0, besteht nur aus Leerraum oder enthält ein
		 *  oder mehr durch {@link Path::getInvalidPathChars} definierte
		 *  ungültige Zeichen.
		 * @throws FileNotFoundException Die in $sourcePath angegebene Datei
		 *  wurde nicht gefunden.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException $destPath gibt eine
		 *  schreibgeschützte Datei an.
		 * @return void
		 * @param string Der Name der zu verschiebenden Datei.
		 * @param string Der neue Pfad für die Datei.
		 */
		static public function move( $sourcePath, $destPath ) {
			
		}
		
		/**
		 * Öffnet einen {@link FileStream} unter dem angegebenen Pfad, mit dem
		 * angegebenen Modus und Zugriff.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException Der Stream kann nicht geöffnet
		 *  werden oder $path gibt ein Verzeichnis an.
		 * @throws FileNotFoundException Die Datei kann nicht gefunden werden.
		 *  Dies ist z. B. der Fall, wenn mode auf FileMode.Truncate oder
		 *  FileMode.Open festgelegt wurde und die durch $path angegebene Datei
		 *  nicht vorhanden ist. Die Datei muss in diesen Modi bereits
		 *  vorhanden sein.
		 * @throws OutOfRangeException $mode oder $access enthält einen
		 *  ungültigen Wert.
		 * @return FileStream Ein FileStream, der Zugriff im angegebenen Modus
		 *  mit angegebenem Zugriff auf die angegebene Datei bereitstellt.
		 * @param string Die zu öffnende Datei.
		 * @param integer Ein FileMode-Wert, der angibt, ob eine Datei erstellt
		 *  wird, wenn sie nicht vorhanden ist, und bestimmt, ob der Inhalt
		 *  vorhandener Dateien beibehalten oder überschrieben wird.
		 * @param integer Ein FileAccess-Wert, der die Vorgänge angibt, die für
		 *  die Datei ausgeführt werden können.
		 */
		static public function open( $path, $mode, $access ) {
			
		}
		
		/**
		 * Öffnet eine vorhandene Datei zum Lesen.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException Die Datei darf nicht zum Lesen
		 *  geöffnet werden.
		 * @throws FileNotFoundException Die Datei kann nicht gefunden werden.
		 * @return FileStream Ein schreibgeschützter FileStream für den
		 *  angegebenen Pfad.
		 * @param string Die Datei, die zum Lesen geöffnet werden soll.
		 */
		static public function openRead( $path ) {
			
		}
		
		/**
		 * Öffnet eine vorhandene Textdatei zum Lesen.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException Die Datei darf nicht zum Lesen
		 *  geöffnet werden.
		 * @throws FileNotFoundException Die Datei kann nicht gefunden werden.
		 * @return StreamReader Ein StreamReader für den angegebenen Pfad.
		 * @param string Die Datei, die zum Lesen geöffnet werden soll.
		 */
		static public function openText( $path ) {
			
		}
		
		/**
		 * Öffnet eine vorhandene Datei zum Schreiben.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException Die Datei darf nicht zum
		 *  Schreiben geöffnet werden.
		 * @throws FileNotFoundException Die Datei kann nicht gefunden werden.
		 * @return FileStream Ein FileStream-Objekt am angegebenen Pfad mit
		 *  Write-Zugriff.
		 * @param string Die Datei, die zum Schreiben geöffnet werden soll.
		 */
		static public function openWrite( $path ) {
			
		}
		
		/**
		 * Öffnet eine Textdatei, liest alle Zeilen der Datei und schließt dann
		 * die Datei.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException Die Datei darf nicht zum Lesen
		 *  geöffnet werden.
		 * @throws FileNotFoundException Die Datei kann nicht gefunden werden.
		 * @return array Ein Zeichenfolgenarray, das alle Zeilen der Datei
		 *  enthält.
		 * @param string Die Datei, die zum Lesen geöffnet werden soll.
		 */
		static public function readAllLines( $path ) {
			
		}
		
		/**
		 * Öffnet eine Textdatei, liest den Inhalt der Datei und schließt dann
		 * die Datei.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException Die Datei darf nicht zum Lesen
		 *  geöffnet werden.
		 * @throws FileNotFoundException Die Datei kann nicht gefunden werden.
		 * @return string Eine Zeichenkette, die alle Zeichen der Datei
		 *  enthält.
		 * @param string Die Datei, die zum Lesen geöffnet werden soll.
		 */
		static public function readAllText( $path ) {
			
		}
		
		/**
		 * Erstellt eine neue Datei, schreibt die angegebenen Zeichenfolgen in
		 * die Datei und schließt die Datei dann. Ist die Zieldatei bereits
		 * vorhanden, wird sie überschrieben.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException Die Datei kann nicht erstellt
		 *  werden
		 * @return void
		 * @param string Die Datei, in die geschrieben werden soll.
		 * @param array Das Zeichenfolgenarray, das in die Datei geschrieben
		 *  werden soll.
		 */
		static public function writeAllLines( $path, array $lines ) {
			
		}
		
		/**
		 * Erstellt eine neue Datei, schreibt die angegebene Zeichenfolge in
		 * die Datei und schließt die Datei dann. Ist die Zieldatei bereits
		 * vorhanden, wird sie überschrieben.
		 * 
		 * @throws InvalidArgumentException $path ist eine Zeichenfolge der
		 *  Länge 0, besteht nur aus Leerraum oder enthält ein oder mehr durch
		 *  {@link Path::getInvalidPathChars} definierte ungültige Zeichen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws UnauthorizedAccessException Die Datei kann nicht erstellt
		 *  werden
		 * @return void
		 * @param string Die Datei, in die geschrieben werden soll.
		 * @param string Eine Zeichenfolge, die in die Datei geschrieben werden
		 *  soll.
		 */
		static public function writeAllText( $path, $contents ) {
			
		}
		
	}
	
?>