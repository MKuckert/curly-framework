<?php
	
	/**
	 * Path
	 * Führt Operationen für String-Instanzen aus, die Datei- oder
	 * Verzeichnispfadinformationen enthalten. Diese Operationen werden
	 * plattformübergreifend durchgeführt.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	final class Path {
		
		/**
		* @var array Die in Pfadnamen nicht zulässigen Zeichen.
		* @ignore
		*/
		static private $_invalidPathChars = null;
		
		/**
		* @var array Die in Dateinamen nicht zulässigen Zeichen.
		* @ignore
		*/
		static private $_invalidFileNameChars = null;
		
		/**
		 * Stellt ein plattformspezifisches Zeichen bereit, das zur Trennung
		 * von Verzeichnisebenen in einer Pfadzeichenfolge verwendet wird und
		 * eine hierarchische Dateisystemorganisation wiedergibt.
		 * 
		 * @return string
		 */
		static public function getDirectorySeparatorChar() {
			return( '/' );
		}
		
		/**
		 * Stellt ein alternatives plattformspezifisches Zeichen bereit, das
		 * zur Trennung von Verzeichnisebenen in einer Pfadzeichenfolge
		 * verwendet wird und eine hierarchische Dateisystemorganisation
		 * wiedergibt.
		 * 
		 * @return string
		 */
		static public function getAltDirectorySeparatorChar() {
			return( '\\' );
		}
		
		/**
		 * Ruft ein Array ab, das die Zeichen enthält, die in Pfadnamen nicht
		 * zulässig sind.
		 * Es ist nicht gewährleistet, dass das von dieser Methode
		 * zurückgegebene Array die vollständige Menge der in Datei- und
		 * Verzeichnisnamen ungültigen Zeichen enthält. Die vollständige Menge
		 * ungültiger Zeichen kann von Dateisystem zu Dateisystem
		 * unterschiedlich sein. Auf Windows-basierten Desktopplattformen
		 * können beispielsweise die folgenden Zeichen für Pfadnamen ungültig
		 * sein:
		 *  - ASCII/Unicode-Zeichen 1 bis 31
		 *  - Anführungszeichen (")
		 *  - Kleiner-als-Zeichen (<)
		 *  - Größer-als-Zeichen (>)
		 *  - der senkrechte Balken (|)
		 *  - Rücktastenzeichen (\b)
		 *  - NULL (\0)
		 *  - Tabulator (\t)
		 * 
		 * @return array
		 */
		static public function getInvalidPathChars() {
			if( is_null( self::$_invalidPathChars ) ) {
				self::$_invalidPathChars = array(
					chr( 0 ), chr( 1 ), chr( 2 ), chr( 3 ), chr( 4 ), chr( 5 ), chr( 6 ),
					chr( 7 ), chr( 8 ), chr( 9 ), chr( 10 ), chr( 11 ), chr( 12 ), chr( 13 ),
					chr( 14 ), chr( 15 ), chr( 16 ), chr( 17 ), chr( 18 ), chr( 19 ), chr( 20 ),
					chr( 21 ), chr( 22 ), chr( 23 ), chr( 24 ), chr( 25 ), chr( 26 ), chr( 27 ),
					chr( 28 ), chr( 29 ), chr( 30 ), chr( 31 ),
					'"', '<', '>', '|', "\b", "\0", "\t"
				);
			}
			return( self::$_invalidPathChars );
		}
		
		/**
		 * Ruft ein Array ab, das die Zeichen enthält, die in Dateinamen nicht
		 * zulässig sind.
		 * Es ist nicht gewährleistet, dass das von dieser Methode
		 * zurückgegebene Array die vollständige Menge der in Datei- und
		 * Verzeichnisnamen ungültigen Zeichen enthält. Die vollständige Menge
		 * ungültiger Zeichen kann von Dateisystem zu Dateisystem
		 * unterschiedlich sein. Auf Windows-basierten Desktopplattformen
		 * können beispielsweise die folgenden Zeichen für Dateinamen ungültig
		 * sein:
		 *  - ASCII/Unicode-Zeichen 1 bis 31
		 *  - Anführungszeichen (")
		 *  - Kleiner-als-Zeichen (<)
		 *  - Größer-als-Zeichen (>)
		 *  - der senkrechte Balken (|)
		 *  - Rücktastenzeichen (\b)
		 *  - NULL (\0)
		 *  - Tabulator (\t)
		 * 
		 * @return array
		 */
		static public function getInvalidFileNameChars() {
			if( is_null( self::$_invalidFileNameChars ) ) {
				self::$_invalidFileNameChars = array(
					chr( 0 ), chr( 1 ), chr( 2 ), chr( 3 ), chr( 4 ), chr( 5 ), chr( 6 ),
					chr( 7 ), chr( 8 ), chr( 9 ), chr( 10 ), chr( 11 ), chr( 12 ), chr( 13 ),
					chr( 14 ), chr( 15 ), chr( 16 ), chr( 17 ), chr( 18 ), chr( 19 ), chr( 20 ),
					chr( 21 ), chr( 22 ), chr( 23 ), chr( 24 ), chr( 25 ), chr( 26 ), chr( 27 ),
					chr( 28 ), chr( 29 ), chr( 30 ), chr( 31 ),
					'"', '<', '>', '|', "\b", "\0", "\t"
				);
			}
			return( self::$_invalidFileNameChars );
		}
		
		/**
		 * Ein plattformspezifisches Trennzeichen, das zur Trennung von
		 * Pfadzeichenfolgen in Umgebungsvariablen verwendet wird.
		 * 
		 * Auf Desktopplattformen auf Grundlage von Windows ist der Wert dieses
		 * Felds in der Standardeinstellung das Semikolon (;), andere
		 * Plattformen können jedoch ein anderes Zeichen verwenden. So ist unter
		 * Linux der Doppelpunkt (:) üblig.
		 * 
		 * @return string
		 */
		static public function getPathSeparator() {
			return( ';' );
		}
		
		/**
		 * Ändert die Erweiterung einer Pfadzeichenfolge.
		 * 
		 * @throws InvalidArgumentException $path enthält mindestens eines der
		 * in getInvalidPathChars definierten ungültigen Zeichen.
		 * @return string Eine Zeichenfolge, die die geänderten
		 *  Pfadinformationen enthält.
		 * @param string Die zu ändernden Pfadinformationen. Der Pfad darf
		 *  keines der in GetInvalidPathChars definierten Zeichen enthalten.
		 * @param string Die neue Erweiterung (mit führendem Punkt). Geben Sie
		 *  NULL an, um eine vorhandene Erweiterung aus $path zu entfernen.
		 */
		static public function changeExtension( $path, $extension ) {
			if( !self::isValidPathName( $path ) ) {
				throw new InvalidArgumentException( 'The given argument contains at least one invalid character' );
			}
			
			if( is_null( $path ) or !is_string( $path ) ) {
				return( $path );
			}
			else if( trim( $path )=='' ) {
				return( '' );
			}
			
			// Nach dem letzten Separator suchen
			$lastPos = max(
				strrpos( $path, self::getDirectorySeparatorChar() ),
				strrpos( $path, self::getAltDirectorySeparatorChar() )
			);
			
			$pos = strrpos( $path, '.', $lastPos );
			if( intval( $pos )<=$lastPos ) {
				return( $path.$extension );
			}
			else {
				return( substr( $path, 0, $pos ) . $extension );
			}
		}
		
		/**
		 * Kombiniert zwei Pfadangaben.
		 * Wenn $path1 nicht mit einem in getDirectorySeparatorChar oder
		 * getAltDirectorySeparatorChar definierten gültigen Trennzeichen endet,
		 * wird getDirectorySeparatorChar vor der Verkettung an $path1 angefügt.
		 * 
		 * Wenn $path2 kein Stammverzeichnis enthält (wenn $path2
		 * beispielsweise nicht mit einem Trennzeichen oder einer
		 * Laufwerkangabe beginnt), ist das Ergebnis eine Verkettung der beiden
		 * Pfade mit einem dazwischen eingefügten Trennzeichen. Wenn $path2 ein
		 * Stammverzeichnis enthält, wird $path2 zurückgegeben.
		 * 
		 * Nicht alle für Verzeichnis- und Dateinamen ungültigen Zeichen werden
		 * von der combine-Methode als nicht akzeptabel interpretiert, da diese
		 * Zeichen als Platzhalterzeichen bei der Suche verwendet werden
		 * können. Während beispielsweise Path::combine( "c:/", "*.txt" )
		 * ungültig sein kann, wenn Sie daraus eine Datei erstellen möchten,
		 * ist dies als Suchzeichenfolge gültig. Sie wird daher von der
		 * combine-Methode erfolgreich interpretiert.
		 * 
		 * @return string Eine Zeichenfolge mit dem kombinierten Pfad. Wenn
		 *  einer der beiden angegebenen Pfade eine Zeichenfolge der Länge 0
		 *  ist, gibt diese Methode den anderen Pfad zurück. Wenn $path2 einen
		 *  absoluten Pfad enthält, gibt diese Methode $path2 zurück.
		 * @param string Der erste Pfad.
		 * @param string Der zweite Pfad. 
		 */
		static public function combine( $path1, $path2 ) {
			if( self::isPathRooted( $path2 ) ) {
				return( $path2 );
			}
			
			$sep = self::getDirectorySeparatorChar();
			$sepAlt = self::getAltDirectorySeparatorChar();
			
			$lastChar = substr( $path1, -1 );
			if( $lastChar!=$sep and $lastChar!=$sepAlt ) {
				$path1 .= $sep;
			}
			
			return( $path1 . $path2 );
		}
		
		/**
		 * Gibt die Verzeichnisinformationen für die angegebene
		 * Pfadzeichenfolge zurück.
		 * 
		 * In den meisten Fällen besteht die von dieser Methode zurückgegebene
		 * Zeichenfolge aus allen Zeichen des Pfades bis zum, aber nicht
		 * einschließlich des letzten DirectorySeparatorChar oder
		 * AltDirectorySeparatorChar. Wenn der Pfad aus einem Stammverzeichnis
		 * besteht, z. B. "c:\", wird NULL zurückgegeben.
		 * Da der zurückgegebene Pfad weder DirectorySeparatorChar noch
		 * AltDirectorySeparatorChar am Ende enthält, führt die Übergabe des
		 * zurückgegebenen Pfads an die getDirectoryName-Methode zum
		 * Abschneiden einer Ordnerebene der Ergebniszeichenfolge pro
		 * nachfolgendem Aufruf. Wenn z. B. der Pfad
		 * "C:\Directory\SubDirectory\test.txt" an die getDirectoryName-Methode
		 *  übergeben wird, wird "C:\Directory\SubDirectory" zurückgegeben.
		 *  Wenn diese Zeichenfolge erneut an getDirectoryName übergeben wird,
		 * führt dies zu "C:\Directory".
		 * 
		 * @throws InvalidArgumentException Der $path-Parameter enthält
		 *  ungültige Zeichen, ist leer oder enthält nur Leerräume.
		 * @return string Eine String, der Verzeichnisinformationen für $path
		 *  enthält, oder NULL, wenn $path ein Stammverzeichnis bezeichnet,
		 *  eine leere Zeichenfolge ("") oder NULL ist. Gibt "" zurück, wenn
		 *  $path keine Verzeichnisinformationen enthält.
		 * @param string Der Pfad einer Datei oder eines Verzeichnisses.
		 */
		static public function getDirectoryName( $path ) {
			if( !self::isValidPathName( $path )
				or is_null( $path )
				or trim( $path )==''
			) {
				throw new InvalidArgumentException( 'The given argument contains invalid path characters, is null or contains no non-whitespace-characters' );
			}
			return( dirname( $path ) );
		}
		
		/**
		 * Gibt die Erweiterung der angegebenen Pfadzeichenfolge zurück.
		 * 
		 * @throws InvalidArgumentException $path enthält mindestens eines der
		 *  in getInvalidPathChars definierten ungültigen Zeichen
		 * @return string Ein String, der die Erweiterung des angegebenen
		 *  Pfades (einschließlich "."), NULL oder "" enthält. Wenn $path NULL
		 *  ist, gibt getExtension den Wert NULL zurück. Wenn $path keine
		 *  Informationen über die Erweiterung enthält, gibt getExtension ""
		 *  zurück.
		 * @param string Die Pfadzeichenfolge, aus der die Erweiterung
		 *  abgerufen werden soll.
		 */
		static public function getExtension( $path ) {
			if( !self::isValidPathName( $path ) ) {
				throw new InvalidArgumentException( 'The given argument contains at least one invalid character' );
			}
			
			if( is_null( $path ) or !is_string( $path ) ) {
				return( $path );
			}
			else if( trim( $path )=='' ) {
				return( '' );
			}
			
			// Nach dem letzten Separator suchen
			$lastPos = max(
				strrpos( $path, self::getDirectorySeparatorChar() ),
				strrpos( $path, self::getAltDirectorySeparatorChar() )
			);
			
			if( $lastPos!=0 ) {
				$path = substr( $path, $lastPos+1 );
			}
			$pos = strrpos( $path, '.' );
			if( intval( $pos )<=0 or substr( $path, -1 )=='.' ) {
				return( '' );
			}
			else {
				return( substr( $path, $pos+1 ) );
			}
		}
		
		/**
		 * Gibt den Dateinamen und die Erweiterung der angegebenen
		 * Pfadzeichenfolge zurück.
		 * 
		 * @throws InvalidArgumentException $path enthält mindestens eines der
		 *  in getInvalidPathChars definierten ungültigen Zeichen.
		 * @return string Ein String, bestehend aus den Zeichen nach dem
		 *  letzten Verzeichniszeichen in $path. Wenn das letzte Zeichen von
		 *  $path ein Verzeichnis- bzw. Volumetrennzeichen ist, gibt diese
		 *  Methode "" zurück. Wenn $path NULL ist, gibt diese Methode NULL
		 *  zurück.
		 * @param string Die Pfadzeichenfolge, aus der der Dateiname und die
		 *  Erweiterung abgerufen werden sollen.
		 */
		static public function getFileName( $path ) {
			if( !self::isValidPathName( $path ) ) {
				throw new InvalidArgumentException( 'The given argument contains at least one invalid character' );
			}
			
			if( is_null( $path ) or !is_string( $path ) ) {
				return( $path );
			}
			else if( trim( $path )=='' ) {
				return( '' );
			}
			
			// Nach dem letzten Separator suchen
			$lastPos = max(
				strrpos( $path, self::getDirectorySeparatorChar() ),
				strrpos( $path, self::getAltDirectorySeparatorChar() )
			);
			
			if( $lastPos==0 ) {
				return( $path );
			}
			
			return( substr( $path, $lastPos+1 ) );
		}
		
		/**
		 * Gibt den absoluten Pfad für die angegebene Pfadzeichenfolge zurück.
		 * 
		 * @throws InvalidArgumentException $path enthält mindestens eines der
		 *  in getInvalidPathChars definierten ungültigen Zeichen oder der
		 *  absolute Pfad konnte nicht ermittelt werden.
		 * @return string Eine Zeichenfolge, die den voll gekennzeichneten
		 *  Speicherort von $path enthält, z. B. "C:\MyFile.txt".
		 * @param string Die Datei oder das Verzeichnis, für das die absoluten
		 *  Pfadinformationen abgerufen werden sollen.
		 */
		static public function getFullPath( $path ) {
			if( !self::isValidPathName( $path ) ) {
				throw new InvalidArgumentException( 'The given argument contains at least one invalid character' );
			}
			else if( self::isPathRooted( $path ) ) {
				return( $path );
			}
			
			// Pfad in einzelne Teile splitten und relative Angaben auflösen
			$sep = self::getDirectorySeparatorChar();
			$sepAlt = self::getAltDirectorySeparatorChar();
			
			$cwd = getcwd();
			if( $cwd===false ) {
				throw new InvalidArgumentException( 'Unable to determine the absolute path' );
			}
			
			// Backslash escapen!
			$sep = addslashes( $sep );
			$sepAlt = addslashes( $sepAlt );
			
			// Pfad anhand der Separatorzeichen trennen
			$regex = '=['.$sep.$sepAlt.']+=';
			$split = preg_split( $regex, $path );
			$newPath = preg_split( $regex, $cwd );
			
			// Die einzelnen Pfadteile auflösen
			for( $i=0, $n=count( $split ); $i<$n; $i++ ) {
				if( $split[$i]=='.' or $split[$i]=='' ) {
					continue;
				}
				else if( $split[$i]=='..' ) {
					array_pop( $newPath );
				}
				else {
					$newPath[] = $split[$i];
				}
			}
			
			return( implode( $sep, $newPath ) );
		}
		
		/**
		 * Ruft die Informationen über das Stammverzeichnis des angegebenen
		 * Pfades ab.
		 * Diese Methode überprüft nicht, ob der Pfad oder Dateiname vorhanden
		 * ist.
		 * 
		 * Mögliche Muster für die Zeichenfolge, die von dieser Methode
		 * zurückgegeben wird, lauten wie folgt:
		 *  - Eine leere Zeichenfolge ($path gibt einen relativen Pfad für das
		 *  aktuelle Laufwerk bzw. Volume an).
		 *  - "/" ($path gibt einen absoluten Pfad auf dem aktuellen Laufwerk
		 *  an).
		 *  - "X:/" ($path gibt einen absoluten Pfad auf einem angegebenen
		 *  Laufwerk an).
		 * 
		 * @throws InvalidArgumentException $path enthält mindestens eines der
		 *  in getInvalidPathChars definierten ungültigen Zeichen oder der
		 *  absolute Pfad konnte nicht ermittelt werden.
		 * @return string Eine Zeichenfolge, die das Stammverzeichnis von $path
		 *  enthält, z. B. "C:\", oder eine leere Zeichenfolge, wenn $path
		 *  keine Informationen über das Stammverzeichnis enthält.
		 * @param string Der Pfad, von dem Informationen über das
		 *  Stammverzeichnis abgerufen werden sollen.
		 */
		static public function getPathRoot( $path ) {
			if( !self::isValidPathName( $path ) ) {
				throw new InvalidArgumentException( 'The given argument contains at least one invalid character' );
			}
			else if( !self::isPathRooted( $path ) ) {
				return( '' );
			}
			
			$first = substr( $path, 0, 1 );
			// Beginnt mit einem Separator
			if( $first==self::getDirectorySeparatorChar()
				or
				$first==self::getAltDirectorySeparatorChar()
			) {
				return( $first );
			}
			// Beginnt mit einem Laufwerksbuchstaben
			else {
				return( substr( $path, 0, 3 ) );
			}
		}
		
		/**
		 * Gibt einen zufälligen Ordnernamen oder Dateinamen zurück.
		 * Diese Methode erzeugt im Gegensatz zu getTempFileName() keine Datei.
		 * 
		 * @return string
		 */
		static public function getRandomFileName() {
			return( base_convert( md5( uniqid( rand(), true ) ), 16, 36 ) );
		}
		
		/**
		 * Erstellt eine eindeutig benannte temporäre Datei auf dem Datenträger
		 * mit einer Größe von 0 Byte und gibt den vollständigen Pfad dieser
		 * Datei zurück.
		 * 
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @return string Eine Zeichenkette, die den vollständigen Pfad der
		 *  temporären Datei enthält.
		 */
		static public function getTempFileName() {
			$temp = tempnam( self::getTempPath(), self::getRandomFileName() );
			if( $temp===false ) {
				throw new IOException( 'Unable to create a temporary file' );
			}
			return( $temp );
		}
		
		/**
		 * Gibt den Pfad des temporären Ordners des aktuellen Systems zurück.
		 * 
		 * @return string Eine Zeichenkette, die die Pfadinformationen eines
		 *  temporären Verzeichnisses enthält.
		 */
		static public function getTempPath() {
			if( !function_exists( 'sys_get_temp_dir' ) ) {
		        if( !empty( $_ENV['TMP'] ) ) {
		            return( realpath( $_ENV['TMP'] ) );
		        }
		        else if( !empty( $_ENV['TMPDIR'] ) ) {
		            return( realpath( $_ENV['TMPDIR'] ) );
		        }
		        else if( !empty( $_ENV['TEMP'] ) ) {
		            return( realpath( $_ENV['TEMP'] ) );
		        }
				else {
		            return( self::getRandomFileName() );
		        }
			}
			else {
				return( sys_get_temp_dir() );
			}
		}
		
		/**
		 * Bestimmt, ob ein Pfad eine Dateinamenerweiterung enthält.
		 * 
		 * @return boolean true, wenn die Zeichen, die auf das letzte
		 *  Verzeichnistrennzeichen (\\ oder /) oder Volumetrennzeichen (:) im
		 *  Pfad folgen, einen Punkt (.) enthalten, gefolgt von einem oder mehr
		 *  Zeichen, andernfalls false.
		 * @param string Der Pfad, in dem nach einer Erweiterung gesucht werden
		 *  soll.
		 */
		static public function hasExtension( $path ) {
			// Nach dem letzten Separator suchen
			$lastPos = max(
				strrpos( $path, self::getDirectorySeparatorChar() ),
				strrpos( $path, self::getAltDirectorySeparatorChar() )
			);
			
			if( $lastPos!=0 ) {
				$path = substr( $path, $lastPos+1 );
			}
			
			return( substr( $path, -1 )!='.'
				and
				intval( strrpos( $path, '.' ) )>0
			);
		}
		
		/**
		 * Überprüft, ob auf den übergebene Pfad zugegriffen werden darf, ohne
		 * gegen die open_basedir-Einstellungen zu verstoßen.
		 * 
		 * @return boolean true, wenn auf den Pfad zugegriffen werden kann, ohne
		 * gegen die open_basedir-Einstellungen zu verstoßen
		 * @param string Der zu prüfende Pfad
		 */
		static public function isInBasedir( $path ) {
			$basedir = strval( ini_get( 'open_basedir' ) );
			
			// Keine Einstellungen gesetzt
			if( trim( $basedir )=='' ) {
				return( true );
			}
			
			
		}
		
		/**
		 * Ruft einen Wert ab, der angibt, ob die angegebene Pfadzeichenfolge
		 * absolute oder relative Pfadinformationen enthält.
		 * Diese Methode überprüft nicht, ob der Pfad oder Dateiname vorhanden
		 * ist.
		 * isPathRooted gibt true für $path-Zeichenfolgen wie 
		 * "\\MyDir\\MyFile.txt" und "C:\\MyDir" zurück. Sie gibt false für
		 * $path-Zeichenfolgen wie "MyDir" zurück.
		 * 
		 * @throws InvalidArgumentException $path enthält mindestens eines der
		 *  in getInvalidPathChars definierten ungültigen Zeichen oder der
		 *  absolute Pfad konnte nicht ermittelt werden.
		 * @return boolean true, wenn path einen absoluten Pfad enthält,
		 *  andernfalls false.
		 * @param string Der zu testende Pfad.
		 */
		static public function isPathRooted( $path ) {
			$sep = self::getDirectorySeparatorChar();
			$sepAlt = self::getAltDirectorySeparatorChar();
			
			$first = substr( $path, 0, 1 );
			// Erstes Zeichen ist ein Separator
			if(
				( $first==$sep )
				or
				( $first==$sepAlt )
			) {
				return( true );
			}
			
			$second = substr( $path, 1, 1 );
			if( (
					// Erstes Zeichen ist ein Buchstabe
					( $first >= 'A' and $first <= 'Z' )
					or
					( $first >= 'a' and $first <= 'z' )
				)
				and
				$second==':'
			) {
				// Es ist ein drittes Zeichen vorhanden
				if( strlen( $path ) > 2 ) {
					$first = substr( $path, 2, 1 );
					return( $first==$sep or $first==$sepAlt );
				}
				return( true );
			}
			
			return( false );
		}
		
		/**
		 * Überprüft, ob der übergebene Pfad nur gültige Zeichen enthält.
		 * Diese Zeichen sind die Zeichen in dem Array
		 * {@link getInvalidPathChars}. Ist eines dieser Zeichen in dem
		 * übergebenen Pfad enthalten, wird false zurückgegeben, andernfalls
		 * true.
		 * 
		 * @return boolean true, wenn der übergebene Pfad ausschließlich gültige
		 *  Zeichen enthält.
		 * @param string Der zu überprüfende Pfad.
		 */
		static public function isValidPathName( $path ) {
			foreach( self::getInvalidPathChars() as $char ) {
				if( strpos( $path, $char )!==false ) {
					return( false );
				}
			}
			return( true );
		}
		
	}
	
?>