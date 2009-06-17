<?php
if( !defined( "_CLASS_FILEOBJECT_" ) ) {
	define( "_CLASS_FILEOBJECT_", 1 );
	
	/**
	 * Definiert ein abstraktes Interface zum Zugriff auf eine Datei
	 * 
	 * @since 0.03 - 19.03.2007
	 */
	class FileObject {
		
		/**
		 * Systemabhängiges Verzeichnis-Trennzeichen. Unter Unix "/", unter Windows "\\"
		 * 
		 * @type string
		 * @since 0.03 - 19.03.2007
		 */
		static protected $pathSeparator = null;
		
		/**
		 * Pfad zu diesem Objekt
		 * 
		 * @type string
		 * @since 0.03 - 19.03.2007
		 */
		private $pathName;
		
		/**
		 * Flag, ob es sich bei dem aktuellem Element um ein Verzeichnis oder eine Datei handelt
		 * 
		 * @type bool
		 */
		private $isDir = false;
		
		/**
		 * Lesezugriff auf Membervariablen
		 * 
		 * @return mixed					Null, wenn Variable nicht gefunden
		 * @param string		var			Name der Membervariablen
		 * @since 0.03 - 19.03.2007
		 */
		public function __get( $var ) {
			if( $var=='pathSeparator' ) {
				return( self::$pathSeparator );
			}
		}
		
		/**
		 * Möglichkeit 1)
		 * Erzeugt ein neues Objekt mit dem übergebenen Pfadnamen.
		 * Der Pfadname wird in ein abstraktes Format konvertiert.
		 * 
		 * @param string		Pfadname
		 * 
		 * Möglichkeit 2)
		 * Erzeugt ein neues Objekt als Kind des übergebenen FileObject-Objektes. Parameter 2
		 * ist dabei der Pfad vom Eltern-Objekt zu diesem Objekt.
		 * 
		 * @param FileObject	Eltern-Objekt
		 * @param string		Pfad zum Kind-Objekt
		 * 
		 * Möglichkeit 3)
		 * Erzeugt ein neues Objekt aus Pfad zum Elternverzeichnis und Pfad zum eigenen Objekt,
		 * relativ zum Elternverzeichnis
		 * 
		 * @param string		Pfad zum Eltern-Objekt
		 * @param string		Pfad zum Kind-Objekt
		 * 
		 * @since 0.03 - 19.03.2007
		 */
		public function __construct( $arg1, $arg2=null ) {
			// Verzeichnis-Trennzeichen setzen
			if( is_null( self::$pathSeparator ) ) {
				if( defined( "DIRECTORY_SEPARATOR" ) ) {
					self::$pathSeparator = DIRECTORY_SEPARATOR;
				}
				else if( CORE_OS=='w' ){
					self::$pathSeparator = '\\';
				}
				else {
					self::$pathSeparator = '/';
				}
			}
			
			// Möglichkeit 1)
			if( is_null( $arg2 ) ) {
				$this->setPath( $arg1 );
			}
			// Möglichkeit 2)
			else if( $arg1 instanceof FileObject ) {
				$this->setPath( $arg1->getPath() . self::convertPathToAbstract( $arg2 ) );
			}
			// Möglichkeit 3)
			else {
				$this->setPath( self::convertPathToAbstract( $arg1 . self::$pathSeparator . $arg2 ) );
			}
			
			// ist eindeutig ein Verzeichnis?
			if( $this->isDirectory() and !$this->isFile() ) {
				$this->isDir = true;
			}
			
			// evtl. '/' am Ende entfernen
			if( substr( $this->pathName, -1 )=='/' ) {
				$this->isDir = true;
				$this->setPath( substr( $this->pathName, 0, -1 ) );
			}
		}
		
		/**
		 * Setzt den Pfad dieses Dateiobjektes neu.
		 * 
		 * @return void
		 * @param string
		 * @since 0.03 - 10.04.2007
		 */
		public function setPath( $path ) {
			$this->pathName = self::convertPathToAbstract( $path );
		}
		
		/**
		 * Konvertiert einen Pfadnamen in das intern verwendete abstrakte Format für Pfade
		 * 
		 * @return string
		 * @param string
		 * @since 0.03 - 19.03.2007
		 */
		static protected function convertPathToAbstract( $path ) {
			$path = str_replace( "\\", "/", $path );
			return( $path );
		}
		
		/**
		 * Konvertiert einen Pfadnamen in das extern verwendete System-Format für Pfade
		 * Wichtig: Diese Methode ist so definiert, das sie trotzdem das abstrakte Format
		 * zurück gibt. Dieses Format entspricht genau dem Unix-Format. Es ist also sicher,
		 * das ein Unix-Format zurückgegeben wird.
		 * 
		 * @return string
		 * @param string
		 * @since 0.03 - 19.03.2007
		 */
		static protected function convertPathToSystem( $path ) {
			//$path = str_replace( "/", self::$pathSeparator, $path );
			return( $path );
		}
		
		/**
		 * Gibt den Namen des Objektes zurück. Dies ist lediglich der letzte Teil des Pfadnamens
		 * und somit der Name der Datei bzw. des Verzeichnisses.
		 * 
		 * @return string
		 * @since 0.03 - 19.03.2007
		 */
		public function getName() {
			// Nach '/' vor dem Namen suchen
			if( ( $pos = strrpos( $this->pathName, '/' ) )===false ) {
				return( $this->pathName );
			}
			else {
				return( substr( $this->pathName, $pos+1 ) );
			}
		}
		
		/**
		 * Gibt den Pfad zu diesem Objekt zurück
		 * 
		 * @return string
		 * @since 0.03 - 19.03.2007
		 */
		public function getPath() {
			return( self::convertPathToSystem( $this->pathName . ( $this->isDir ? '/' : '' ) ) );
		}
		
		/**
		 * Gibt den Pfad zum Elternobjekt zurück. Gibt es kein Elternobjekt über diesem Element mehr,
		 * wird null zurückgegeben.
		 * 
		 * @return string
		 * @since 0.03 - 19.03.2007
		 */
		public function getParent() {
			// evtl. '/' am Ende abtrennen
			if( substr( $this->pathName, -1 )=='/' ) {
				$path = substr( $this->pathName, 0, -1 );
			}
			else {
				$path = $this->pathName;
			}
			
			// Nach '/' vor dem Namen suchen
			if( ( $pos = strrpos( $path, '/' ) )===false ) {
				return( null );
			}
			else {
				return( self::convertPathToSystem( substr( $path, 0, $pos ) ) );
			}
		}
		
		/**
		 * Testet, ob es sich bei diesem Objekt um einen absoluten Pfad handelt.
		 * Dieser Test ist Systemabhängig. Unter Unix ist ein Pfad absolut, wenn dieser mit "/" beginnt.
		 * Unter Windows ist ein Pfad absolut, wenn er mit einem Laufwerksbuchstaben und ":\\" oder "\\\\" beginnt.
		 * 
		 * @return bool
		 * @since 0.03 - 19.03.2007
		 */
		public function isAbsolute() {
			// Ist Windows
			if( CORE_OS=='w' ) {
				// Laufwerksbuchstaben gefunden
				if( ctype_alpha( substr( $this->pathName, 0, 1 ) ) and substr( $this->pathName, 1, 2 )==':/' ) {
					return( true );
				}
				// Netzwerkadresse gefunden
				else if( substr( $this->pathName, 0, 2 )=='//' ) {
					return( true );
				}
			}
			// Ist Unix
			else {
				// Beginnt mit Root
				if( substr( $this->pathName, 0, 1 )=='/' ) {
					return( true );
				}
			}
			
			// Bis jetzt nichts absolutes gefunden, also relativ
			return( false );
		}
		
		/**
		 * Ermittelt den absoluten Pfad zu diesem Objekt. Ob ein Pfad absolut ist, ist Systemabhängig. Siehe dazu
		 * die Methode isAbsolute()
		 * 
		 * @return string
		 * @since 0.03 - 19.03.2007
		 */
		public function getAbsolutePath() {
			if( $this->isAbsolute() ) {
				return( self::convertPathToSystem( $this->pathName ) );
			}
			
			$curdir = self::convertPathToAbstract( getcwd() );
	
			// evtl. Verzeichnis-Trennzeichen anhängen
			if( substr( $curdir, -1 )=='/' ) {
				$curdir = substr( $curdir, 0, -1 );
			}
			if( substr( $this->pathName, 0, 1 )=='/' ) {
				$path = substr( $this->pathName, 1 );
			}
			
			// Absoluten Pfad erzeugen
			$path = $curdir . "/" . $this->pathName;
			
			// Verweise zu Elternverzeichnissen entfernen
			$pathArr = explode( "/", $path );
			for( $i=0,$max=count( $pathArr ); $i<$max; $i++ ) {
				if( $pathArr[$i]=='..' and $i>0 ) {
					unset( $pathArr[$i] );
					unset( $pathArr[$i-1] );
				}
			}
			
			return( self::convertPathToSystem( implode( "/", $pathArr ) ) );
		}
		
		/**
		 * Überprüft, ob das Objekt gelesen werden kann
		 * 
		 * @return bool
		 * @since 0.03 - 19.03.2007
		 */
		public function isReadable() {
			return( $this->exists() and is_readable( $this->pathName ) );
		}
		
		/**
		 * Überprüft, ob das Objekt geschrieben werden kann
		 * 
		 * @return bool
		 * @since 0.03 - 19.03.2007
		 */
		public function isWriteable() {
			return( $this->exists() and is_writeable( $this->pathName ) );
		}
		
		/**
		 * Überprüft, ob das Objekt ausgeführt werden kann
		 * 
		 * @return bool
		 * @since 0.03 - 19.03.2007
		 */
		public function isExecutable() {
			return( $this->exists() and is_executable( $this->pathName ) );
		}
		
		/**
		 * Überprüft, ob das Objekt existiert
		 * 
		 * @return bool
		 * @since 0.03 - 19.03.2007
		 */
		public function exists() {
			return( file_exists( $this->pathName ) );
		}
		
		/**
		 * Überprüft, ob das Objekt ein Verzeichnis ist
		 * 
		 * @return bool
		 * @since 0.03 - 19.03.2007
		 */
		public function isDirectory() {
			return( $this->exists() and is_dir( $this->pathName ) );
		}
		
		/**
		 * Überprüft, ob das Objekt eine Datei ist
		 * 
		 * @return bool
		 * @since 0.03 - 19.03.2007
		 */
		public function isFile() {
			return( $this->exists() and is_file( $this->pathName ) );
		}
		
		/**
		 * Überprüft, ob das Objekt ein verstecktes Objekt ist.
		 * Ob ein Objekt versteckt ist, ist Systemabhängig. Unter Unix beginnt ein verstecktes Objekt mit einem Punkt.
		 * Unter Windows muss ein Objekt als versteckt markiert sein.
		 * 
		 * @return bool
		 * @since 0.03 - 19.03.2007
		 */
		public function isHidden() {
			// ist Windows
			if( CORE_OS=='w' ) {
				$rights = $this->getRights();
				return( $rights!==false and strpos( $rights, 'H' )!==false );
			}
			// ist Unix
			else {
				if( substr( $this->getName(), 0, 1 )=='.' ) {
					return( true );
				}
			}
			
			return( false );
		}
		
		/**
		 * Gibt den Zeitpunkt der letzten Änderung an der Datei zurück.
		 * 
		 * @return int
		 * @since 0.03 - 19.03.2007
		 */
		public function lastModified() {
			return( filemtime( $this->pathName ) );
		}
		
		/**
		 * Gibt die Grösse des Objektes zurück.
		 * Kann die Grösse nicht ermittelt werden, weil das Objekt beispielsweise nicht existiert, 
		 * wird 0 zurückgegeben!
		 * Vorsicht: Ein Verzeichnis wird traversiert und die Grösse aller Unterobjekte ermittelt.
		 * 
		 * @return int
		 * @since 0.03 - 19.03.2007
		 */
		public function size() {
			// ist Datei
			if( $this->isFile() ) {
				// Dateigröße einfach auslesen
				$f = filesize( $this->pathName );
				if( $f!==false ) {
					return( $f );
				}
			}
			// ist Verzeichnis
			else if( $this->isDirectory() ) {
				// Liste der Objekte im Verzeichnis holen
				$list = $this->getFileList();
				$size = 0;
				foreach( $list as $elem ) {
					$s = $elem->size();
					// Fehler bei Größenermittlung eines Unterobjektes
					if( $s<0 ) {
						return( 0 );
					}
					// Größe der Unterobjekte aufakkumulieren
					$size += $s;
				}
				return( $size );
			}
			
			// Objekt existiert nicht oder es ist ein Fehler aufgetreten
			return( 0 );
		}
		
		/**
		 * Legt eine neue Datei mit diesem Pfad an.
		 * Existiert diese Datei bereits oder tritt ein Fehler auf, wird false zurückgegeben, sonst true.
		 * 
		 * @return bool
		 * @param bool		Ist dieser Parameter true (Standard), werden auch alle fehlenden Elternverzeichnisse erzeugt
		 * @since 0.03 - 19.03.2007
		 */
		public function createNewFile( $createParents=true ) {
			// Existiert diese Datei bereits?
			if( $this->isFile() ) {
				return( false );
			}
			
			// Verzeichnisse evtl. bis hier anlegen
			if( $createParents ) {
				$pathArr = explode( "/", dirname( $this->pathName ) );
				$path = '';
				foreach( $pathArr as $obj ) {
					$path .= $obj .'/';
					if( $path=='/' ) {
						continue;
					}
					// Verzeichnis existiert noch nicht
					if( !file_exists( $path ) or !is_dir( $path ) ) {
						// ... also anlegen
						if( !mkdir( $path ) ) {
							return( false );
						}
					}
				}
			}
			
			// Datei anlegen
			$fh = fopen( $this->pathName, "ab" );
			if( $fh===false ) {
				return( false );
			}
			return( true );
		}
		
		/**
		 * Legt ein neues Verzeichnis mit diesem Pfad an.
		 * Existiert dieses Verzeichnis bereits oder tritt ein Fehler auf, wird false zurückgegeben
		 * 
		 * @return bool
		 * @param bool		Ist dieser Parameter true, werden auch alle fehlenden Elternverzeichnisse erzeugt
		 * @since 0.03 - 19.03.2007
		 */
		public function createNewDirectory( $createParents=true ) {
			// Existiert dieses Verzeichnis bereits?
			if( $this->isDirectory() ) {
				return( false );
			}
			
			// Verzeichnisse evtl. bis hier anlegen
			if( $createParents ) {
				$pathArr = explode( "/", dirname( $this->pathName ) );
				$path = '';
				foreach( $pathArr as $obj ) {
					$path .= $obj . '/';
					// Verzeichnis existiert noch nicht
					if( !@file_exists( $path ) or !@is_dir( $path ) ) {
						// ... also anlegen
						if( CORE_OS=='w' ) {
							$exec = 'mkdir "'.$path.'"';
							exec( $exec );
						}
						else {
							if( !@mkdir( $path ) ) {
								return( false );
							}
						}
					}
				}
			}
			
			// Verzeichnis anlegen
			if( CORE_OS=='w' ) {
				$exec = 'mkdir "'.$this->pathName.'"';
				$ans = exec( $exec );
				return( trim( $ans )=='' );
			}
			else {
				if( !mkdir( $this->pathName ) ) {
					return( false );
				}
			}
			return( true );
		}
		
		/**
		 * Löscht dieses Objekt.
		 * Ist das Objekt ein Verzeichnis, muss dieser leer sein oder der Parameter clean auf true gesetzt werden.
		 * Gibt bei Erfolg true zurück, sonst false
		 * 
		 * @return bool
		 * @param bool		Flag, ob Verzeichnisse beim Löschen geleert werden sollen (false ist Standard)
		 * @since 0.03 - 19.03.2007
		 */
		public function delete( $clean=false ) {
			// ist Datei
			if( $this->isFile() ) {
				return( unlink( $this->pathName ) );
			}
			// ist Verzeichnis
			else if( $this->isDirectory() ) {
				// evtl. Objekte im Verzeichnis entfernen
				if( $clean ) {
					// Liste der Unterobjekte holen
					$list = $this->getFileList();
					foreach( $list as $obj ) {
						// Objekt kann nicht gelöscht werden
						if( !$obj->delete() ) {
							return( false );
						}
					}
				}
				// Dieses Objekt entfernen
				return( rmdir( $this->pathName ) );
			}
			
			return( false );
		}
		
		/**
		 * Gibt eine Liste aller Objekte in diesem Objekt zurück.
		 * Ist dieses Objekt kein Verzeichnis, ist die Liste leer. Ist dieses Objekt ein Verzeichnis, wird der Inhalt des
		 * Verzeichnisses ausgelesen. Dabei werden die kompletten Pfade in dem Array übergeben
		 * 
		 * @return array
		 * @param mixed		Filter, mit dem nur passende Objekte ausgewählt werden. Dieser Parameter kann entweder
		 * 					ein Array aus String oder ein String sein.
		 * @since 0.03 - 19.03.2007
		 */
		public function getList( $filter='*' ) {
			// Ist kein Verzeichnis oder existiert noch nicht
			if( !$this->isDirectory() ) {
				return( array() );
			}
			
			// Objekte aus dem Verzeichnis filtern
			if( is_array( $filter ) ) {
				$content = array();
				foreach( $filter as $f ) {
					$content[] = glob( $this->pathName."/".$f );
				}
				$content = call_user_func_array( 'array_merge', $content );
			}
			else {
				$content = glob( $this->pathName."/".$filter );
			}
			
			// Pfade umwandeln
			foreach( $content as $key=>$value ) {
				$content[$key] = self::convertPathToSystem( $value );
			}
			
			// Gefundene Objekte zurückgeben
			return( $content );
		}
		
		/**
		 * Gibt eine Liste aller Objekte in diesem Objekt zurück.
		 * Der einzige Unterschied zu der Methode getList ist der, das kein Array von String sondern ein Array von
		 * FileObject-Objekten zurückgegeben wird
		 * 
		 * @return array
		 * @param mixed		Filter, mit dem nur passende Objekte ausgewählt werden. Dieser Parameter kann entweder
		 * 					ein Array aus String oder ein String sein.
		 * @since 0.03 - 19.03.2007
		 */
		public function getFileList( $filter='*' ) {
			// Liste aller Objekte holen
			$list = $this->getList( $filter );
			// Jeden Pfad durch FileObject ersetzen
			foreach( $list as $key=>$elem ) {
				$list[$key] = new FileObject( $elem );
			}
			return( $list );
		}
		
		/**
		 * Benennt dieses Objekt um.
		 * Sollte das umbenennen nicht möglich sein oder tritt ein Fehler auf, wird false zurückgegeben.
		 * 
		 * @return bool
		 * @param string	Der neue Name für das Objekt
		 * @since 0.03 - 19.03.2007
		 */
		public function rename( $newname ) {
			if( $this->exists() and rename( $this->pathName, $newname ) ) {
				$this->pathName = $newname;
				return( true );
			}
			return( false );
		}
		
		/**
		 * Kopiert dieses Objekt
		 * Vorsicht: Unter Windows ist das Kopieren aufgrund von PHP-Problemen nicht ohne weiteres möglich
		 * 
		 * @return bool
		 * @param string/FileObject		Ziel
		 * @since 0.10 - 26.04.2007
		 */
		public function copy( $destination ) {
			if( is_string( $destination ) ) {
				$destination = new FileObject( $destination );
			}
			
			if( substr( $destination->getPath(), -1 )!='/' ) {
				$destination->setPath( $destination->getPath() . '/' );
			}
			
			if( !$destination->isDirectory() and !$destination->createNewDirectory() ) {
				return( false );
			}
			
			$name = $this->getName();
			$dest = clone $destination;
			$dest->setPath( $destination->getPath() . $name );
			if( $dest->exists() ) {
				$n = "Kopie von " . $name;
				$dest->setPath( $destination->getPath() . $n );
				$i = 2;
				while( $dest->exists() ) {
					$n = "Kopie (" . $i++ .") von " . $name;
					$dest->setPath( $destination->getPath() . $n );
				}
				$name = $n;
			}
			
			if( $this->isFile() ) {
				// Ist Windows
				if( CORE_OS=='w' ) {
					/*$exec = 'COPY /Y "' . $this->getPath() . '" "' . $destination->getPath() . $this->getName() . '"';
					$res = exec( $exec );
					// Rückgabe ist entweder "1 Datei(en) kopiert" oder "Das System kann die angegebene Datei nicht finden."
					$res = ltrim( $res );
					$res = intval( $res );
					return( $res==1 );*/
					$dest = new FileObject( $destination->getPath() . $name );
					if( !$dest->createNewFile() ) {
						return( false );
					}
					try {
						$writer = new FileWriter( $dest );
						$reader = new FileReader( $this );
						do {
							$row = $reader->read( 1024 );
							$writer->write( $row );
						}
						while( $reader->moreData() );
					}
					catch( Exception $ex ) {
						return( false );
					}
					return( true );
				}
				else {
					return( copy( $this->getPath(), $destination->getPath() . $name ) );
				}
			}
			else if( $this->isDirectory() ) {
				foreach( $this->getFileList() as $element ) {
					// Ist Datei
					if( $element->isFile() ) {
						if( !$element->copy( $destination ) ) {
							return( false );
						}
					}
					// Ist Verzeichnis
					else if( $element->isDirectory() ) {
						if( $element->getPath() == $destination->getPath() ) {
							continue;
						}
						if( !$element->copy( $destination->getPath() . $element->getName() ) ) {
							return( false );
						}
					}
				}
				return( true );
			}
			
			return( false );
		}
		
		/**
		 * Setzt die Rechte für den Zugriff auf dieses Objekt.
		 * Der Parameter für diese Methode ist Systemabhängig. Aus diesem Grund sollte diese Methode nur
		 * direkt verwendet werden, wenn das System bekannt ist.
		 * 
		 * @return bool		Gibt bei Erfolg true zurück, sonst false
		 * @param mixed		neuer Zugriffsparameter
		 * @param bool		Flag, ob bei Verzeichnissen alle Unterobjekte auch mit diesen Zugriffrechten
		 * 					konfiguriert werden sollen (false ist Standard)
		 * @since 0.03 - 19.03.2007
		 */
		public function setRights( $rights, $recursive=false ) {
			if( !$this->exists() ) {
				return( false );
			}
			
			// ist Windows
			if( CORE_OS=='w' ) {
				exec( 'attrib ' . $rights . ' "' . $this->pathName.'"' );
				if( $recursive and $this->isDirectory() ) {
					foreach( $this->getFileList() as $obj ) {
						$obj->setRights( $rights, $recursive );
					}
				}
				return( true );
			}
			// ist Unix
			else {
				if( !chmod( $this->pathName, $rights ) ) {
					return( false );
				}
				if( $recursive and $this->isDirectory() ) {
					foreach( $this->getFileList() as $obj ) {
						if( !$obj->setRights( $rights, $recursive ) ) {
							return( false );
						}
					}
				}
				return( true );
			}
		}
		
		/**
		 * Gibt die Rechte für den Zugriff auf dieses Objekt.
		 * Der Parameter für diese Methode ist Systemabhängig. Aus diesem Grund sollte diese Methode nur
		 * direkt verwendet werden, wenn das System bekannt ist.
		 * 
		 * @return mixed	aktueller Zugriffsparameter
		 * @since 0.03 - 19.03.2007
		 */
		public function getRights() {
			if( !$this->exists() ) {
				return( false );
			}
			
			// ist Windows
			if( CORE_OS=='w' ) {
				// Unter Windows kann mit dem Kommando attrib abgefragt werden, ob das Objekt unsichtbar ist
				$result = exec( "attrib " . $this->pathName );
				
				// Pfad vom Ergebnis wegschneiden
				$pos = strpos( $result, self::convertPathToSystem( $this->pathName ) );
				$result = substr( $result, 0, $pos );
				
				// Entfernen von Leerzeichen
				$result = str_replace( array( " ", "\t" ), "", $result );
				return( $result );
			}
			// ist Unix
			else {
				$result = fileperms( $this->pathName );
				return( $result );
			}
		}
		
		/**
		 * Macht dieses Objekt schreibgeschützt (nur noch Lesezugriff erlaubt).
		 * 
		 * @return bool
		 * @param bool		Flag, ob bei Verzeichnissen alle Unterobjekte auch mit diesen Zugriffrechten
		 * 					konfiguriert werden sollen (false ist Standard)
		 * @since 0.03 - 19.03.2007
		 */
		public function setReadOnly( $recursive=false ) {
			if( !$this->exists() ) {
				return( false );
			}
			
			// ist Windows
			if( CORE_OS=='w' ) {
				return( $this->setRights( "+R", $recursive ) );
			}
			// ist Unix
			else {
				return( $this->setRights( 0444, $recursive ) );
			}
		}
		
		/**
		 *  Macht dieses Objekt unsichtbar
		 * 
		 * @return bool
		 * @since 0.03 - 20.03.2007
		 */
		public function setHidden() {
			if( !$this->exists() ) {
				return( false );
			}
			
			// ist Windows
			if( CORE_OS=='w' ) {
				return( $this->setRights( "+H" ) );
			}
			// ist Unix
			else {
				$pos = strrpos( $this->pathName, "/" );
				if( $pos===false ) {
					return( false );
				}
				// '.' vor Dateinamen?
				if( substr( $this->pathName, $pos+1, 1 )!='.' ) {
					// wenn nicht, dann diesen hinzufügen
					$this->pathName = substr( $this->pathName, 0, $pos+1 ) . '.' . substr( $this->pathName, $pos+1 );
				}
				return( true );
			}
		}
		
		/**
		 * Entfernt jeglichen Schreibschutz von dem Objekt
		 * 
		 * @return bool
		 * @since 0.03 - 19.03.2007
		 */
		public function setAllAccess() {
			if( !$this->exists() ) {
				return( false );
			}
			
			// ist Windows
			if( CORE_OS=='w' ) {
				return( $this->setRights( "-H -R" ) );
			}
			// ist Unix
			else {
				return( $this->setRights( 0775 ) );
			}
		}
		
		/**
		 * Erzeugt eine temporäre Datei und gibt ein FileObject-Objekt für diese zurück.
		 * Diese Datei wird nach Beendigung des Skriptes NICHT automatisch gelöscht.
		 * 
		 * @return FileObject			Oder null bei Fehlern
		 * @since 0.03 - 19.03.2007
		 */
		public function createTempFile() {
			$name = tempnam( "", "TMPFILE_" );
			if( $name===false ) {
				return( null );
			}
			else {
				return( new FileObject( $name ) );
			}
		}
		
		/**
		 * Öffnet einen Handle auf dieses Objekt.
		 * Ist dieses Objekt eine Datei, wird ein filehandle zurückgegeben.
		 * Ist dieses Objekt ein Verzeichnis, wird ein Directory-Objekt zurückgegeben.
		 * Ist dieses Objekt noch nicht vorhanden oder tritt ein Fehler auf, wird null zurückgegeben.
		 * 
		 * @return mixed
		 * @param string		Beim Öffnen einer Datei der Modus zum Öffnen des Handle
		 * @since 0.03 - 19.03.2007
		 */
		public function open( $mode=null ) {
			// ist Datei
			if( $this->isFile() ) {
				if( is_null( $mode ) ) {
					return( null );
				}
				// Bei Windows binär öffnen
				if( CORE_OS=='w' and stripos( $mode, 'b' )===false ) {
					$mode.='b';
				}
				// Datei öffnen
				$handle = fopen( $this->pathName, $mode );
				if( $handle===false ) {
					return( null );
				}
				else {
					return( $handle );
				}
			}
			// ist Verzeichnis
			else if( $this->isDirectory() ) {
				// Verzeichnis öffnen
				$handle = dir( $this->pathName );
				if( $handle instanceof Directory ) {
					return( $handle );
				}
				else {
					return( null );
				}
			}
			
			return( null );
		}
		
		/**
		 * Bindet diese Datei ein
		 * 
		 * @return void
		 * @since 0.03 - 10.04.2007
		 */
		public function import() {
			include $this->pathName;
		}
		
		/**
		 * "Berührt" diese Datei und setzt somit die letzte Änderung auf dieses Datum
		 * 
		 * @return void
		 * @since 0.10 - 01.05.2007
		 */
		public function touch() {
			@touch( $this->pathName );
		}
		
		/**
		 * Sendet Header zum Client, so dass dieser zum Download aufgefordert wird. Nach senden der Datei wird die Skriptausführung
		 * beendet.
		 * Dies erfordert, das bisher keine Ausgaben zum Client gesendet wurden.
		 * Wirft OutOfRangeException bei fehlerhaftem speed-Wert
		 * 
		 * @return void
		 * @param float			Maximale Downloadgeschwindigkeit in Kilobyte (Wert=0 bedeutet volle Geschwindigkeit)
		 * @since 0.10 - 05.05.2007
		 */
		public function forceDownload( $speed=0.0, $exit=true ) {
			settype( $speed, 'float' );
			if( $speed<0 ) {
				throw new OutOfRangeException( "Der Parameter speed muss vom Typ Float und positiv sein!" );
			}
			
			// Header senden
			header( "Cache-Control: must-revalidate, pre-check=0, post-check=0" );
			header( "Content-disposition: attachment; filename=".$this->getName() );
			header( "Content-type: application/force-download" );
			header( "Pragma: no-cache" );
			header( "Expires: 0" );
			
			// Datei auslesen
			$reader = new FileReader( $this );
			set_time_limit( 0 );
			
			// volle Downloadgeschwindigkeit
			if( $speed==0 ) {
				while( !$reader->end() ) {
					echo $reader->read( $this->size() );
				}
			}
			// begrenzte Downloadgeschwindigkeit
			else {
				while( !$reader->end() ) {
					echo $reader->read( $speed * 1024 );
					sleep( 1 );
				}
			}
			if( $exit ) {
				exit;
			}
		}
		
		/**
		 * Lädt eine ini-Datei
		 * Wirft InvalidArgumentException, wenn der Parameter file nicht vom Typ string ist
		 * Wirft FileException, wenn die Datei file nicht gefunden wird
		 * 
		 * @return array		null bei nicht finden einer Sektion
		 * @param string		Dateiname
		 * @param mixed			true, wenn die ini-Datei mit Sektionen geladen werden soll
		 * 						oder der Name einer Sektion, um nur diese zurückzugeben
		 * @since 0.20 - 30.07.2007
		 */
		static public function loadIni( $file, $process_sections=false ) {
			if( !is_string( $file ) ) {
				throw new InvalidArgumentException( "Der Parameter file muss vom Typ string sein!" );
			}
			
			// Datei nicht gefunden
			if( !is_file( $file ) ) {
				if( is_file( ROOT.$file ) ) {
					$file = ROOT.$file;
				}
				else {
					throw new FileException( "Datei nicht gefunden!" );
				}
			}
			
			if( is_bool( $process_sections ) ) {
				return( parse_ini_file( $file, $process_sections ) );
			}
			else {
				$ini = parse_ini_file( $file, true );
				if( isset( $ini[$process_sections] ) ) {
					return( $ini[$process_sections] );
				}
				else {
					return( null );
				}
			}
		}
		
		/**
		 * Lädt eine Listen-Datei (Jede Zeile stellt einen Wert da)
		 * Wirft InvalidArgumentException, wenn der Parameter file nicht vom Typ string ist
		 * Wirft FileException, wenn die Datei file nicht gefunden wird
		 * 
		 * @return array
		 * @param string		Dateiname
		 * @since 0.21 - 11.08.2007
		 */
		static public function loadList( $file ) {
			if( !is_string( $file ) ) {
				throw new InvalidArgumentException( "Der Parameter file muss vom Typ string sein!" );
			}
			
			// Datei nicht gefunden
			if( !is_file( $file ) ) {
				if( is_file( ROOT.$file ) ) {
					$file = ROOT.$file;
				}
				else {
					throw new FileException( "Datei nicht gefunden!" );
				}
			}
			
			$fo = new FileObject( $file );
			$reader = new FileReader( $fo );
			$out = array();
			
			while( $reader->moreData() ) {
				$buf = trim( $reader->readLine() );
				if( $buf!==false and $buf!='' and substr( $buf, 0, 1 )!=';' ) {
					$out[] = $buf;
				}
			}
			
			return( $out );
		}
	};
}
?>