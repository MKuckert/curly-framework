<?php
	
	/**
	 * FileStream
	 * Macht einen Stream um eine Datei verfügbar, wobei synchrone Lese- und
	 * Schreibvorgänge unterstützt werden.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	class FileStream extends Stream {
		
		/**
		 * @var resource Interner Resourcenhandle auf die Datei
		 */
		private $_handle = null;
		
		/**
		 * @var integer Die Zugriffsoptionen, mit der dieser Stream geöffnet
		 * wurde.
		 */
		private $_access = 0;
		
		/**
		 * @var string Der Pfad zu der Datei dieses Streams
		 */
		private $_path = '';
		
		/**
		 * Konstruktor
		 * Öffnet einen Stream auf eine Datei mit den übergebenen FileMode und
		 * FileAccess-Optionen.
		 * 
		 * @throws InvalidArgumentException $path ist eine leere Zeichenfolge
		 *  oder enthält mindestens ein ungültiges Zeichen oder $path verweist
		 *  auf ein Gerät, das keine Datei ist. Die Exception wird auch
		 *  geworfen, wenn die $mode-$access-Kombination ungültig ist.
		 * @throws FileNotFoundException Die Datei kann nicht gefunden werden.
		 *  Dies ist z. B. der Fall, wenn mode auf FileMode.Truncate oder
		 *  FileMode.Open festgelegt wurde und die durch $path angegebene Datei
		 *  nicht vorhanden ist. Die Datei muss in diesen Modi bereits
		 *  vorhanden sein.
		 * @throws IOException Es ist ein Ein-/Ausgabefehler aufgetreten,
		 *  beispielsweise wurde FileMode.CreateNew angegeben, und die durch
		 *  $path angegebene Datei ist bereits vorhanden.
		 * @param string Ein relativer oder absoluter Pfad zu der Datei, die
		 *  vom aktuellen FileStream-Objekt gekapselt wird.
		 * @param int Eine FileMode-Konstante oder Kombination aus diesen, die
		 *  bestimmt, auf welche Weise die Datei geöffnet oder erstellt werden
		 *  soll.
		 * @param int Eine FileAccess-Konstante, die bestimmt, welcher Zugriff
		 *  auf die Datei für das FileStream-Objekt zulässig ist. Dies ruft die
		 *  CanRead-Eigenschaft und die CanWrite-Eigenschaft des
		 *  FileStream-Objekts ab. CanSeek ist true, wenn path eine
		 *  Datenträgerdatei angibt.
		 */
		public function __construct( $path, $mode, $access ) {
			// $path überprüfen
			$path = strval( $path );
			if( $path=='' ) {
				throw new InvalidArgumentException( 'The parameter $path is an empty string!' );
			}
			else if( !Path::isValidPathName( $path ) ) {
				throw new InvalidArgumentException( 'The parameter $path contains invalid characters!' );
			}
			else if( file_exists( $path ) and !is_file( $path ) ) {
				throw new InvalidArgumentException( 'The parameter $path does not point to a file!' );
			}
			
			// $mode überprüfen
			if( !( $mode==FileMode::Append
				or $mode==FileMode::Create
				or $mode==FileMode::CreateNew
				or $mode==FileMode::Open
				or $mode==FileMode::OpenOrCreate
				or $mode==FileMode::Truncate
			) ) {
				throw new InvalidArgumentException( 'The parameter $mode is not valid!' );
			}
			
			// $access überprüfen
			if( !( ( ( $access & FileAccess::Read ) == FileAccess::Read )
				or ( ( $access & FileAccess::Write ) == FileAccess::Write )
			) ) {
				throw new InvalidArgumentException( 'The parameter $access is not valid!' );
			}
			
			$openMode = '';
			
			// Append
			if( $mode == FileMode::Append ) {
				if( ( $access & FileAccess::Write ) != FileAccess::Write ) {
					throw new InvalidArgumentException( 'FileMode::Append requires FileAccess::Write!' );
				}
				else if( ( $access & FileAccess::Read ) == FileAccess::Read ) {
					$openMode = 'a+';
				}
				else {
					$openMode = 'a';
				}
			}
			// Create
			else if( $mode == FileMode::Create ) {
				if( ( $access & FileAccess::Write ) != FileAccess::Write ) {
					throw new InvalidArgumentException( 'FileMode::Create requires FileAccess::Write!' );
				}
				else if( ( $access & FileAccess::Read ) == FileAccess::Read ) {
					$openMode = 'w+';
				}
				else {
					$openMode = 'w';
				}
			}
			// CreateNew
			else if( $mode == FileMode::CreateNew ) {
				if( ( $access & FileAccess::Write ) != FileAccess::Write ) {
					throw new InvalidArgumentException( 'FileMode::CreateNew requires FileAccess::Write!' );
				}
				else if( ( $access & FileAccess::Read ) == FileAccess::Read ) {
					$openMode = 'w+';
				}
				else {
					$openMode = 'w';
				}
				
				if( File::exists( $path ) ) {
					throw new IOException( 'The file '.$path.' already exists!' );
				}
			}
			// Open
			else if( $mode == FileMode::Open ) {
				// TODO
				if( ( $access & FileAccess::Write ) == FileAccess::Write ) {
					$openMode = 'r+';
				}
				else {
					$openMode = 'r';
				}
				
				if( !File::exists( $path ) ) {
					throw new FileNotFoundException( 'The file '.$path.' does not exist!' );
				}
			}
			// OpenOrCreate
			else if( $mode == FileMode::OpenOrCreate ) {
				if( File::exists( $path ) ) {
					if( ( $access & FileAccess::Write ) == FileAccess::Write ) {
						$openMode = 'r+';
					}
					else {
						$openMode = 'r';
					}
				}
				else {
					if( ( $access & FileAccess::Read ) == FileAccess::Read ) {
						$openMode = 'w+';
					}
					else {
						$openMode = 'w';
					}
				}
			}
			// Truncate
			else {
				if( !File::exists( $path ) ) {
					throw new FileNotFoundException( 'The file '.$path.' does not exist!' );
				}
				
				$openMode = 'w';
			}
			
			$this->_access = $access;
			$this->_handle = @fopen( $path, $openMode );
			if( !is_resource( $this->_handle ) ) {
				$this->_handle = null;
				throw new IOException( 'Unable to open or create the file $path!' );
			}
			$this->_path = $path;
		}
		
		/**
		 * Löscht sämtliche Puffer für diesen Stream und veranlasst die Ausgabe
		 * aller gepufferten Daten an das Dateisystem.
		 * 
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @return void
		 */
		public function flush() {
			
		}
		
		/**
		 * Gibt alle dem aktuellen Stream zugeordneten Ressourcen frei
		 * (z. B. Sockets und Dateihandles).
		 * 
		 * @return void
		 */
		public function dispose() {
			parent::dispose();
			fclose( $this->_handle );
		}
		
		/**
		 * Verhindert, dass andere Prozesse den FileStream ändern, während
		 * gleichzeitig Lesezugriff gewährt wird.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @return void
		 */
		public function lock() {
			if( $this->getHasDisposed() ) {
				throw new BadMethodCallException( 'The filehandle has already been closed!' );
			}
			
			flock( $this->_path );
		}
		
		/**
		 * Liest einen Byteblock aus dem Stream und schreibt die Daten in den
		 * angegebenen Puffer.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws OutOfRangeException $count ist negativ
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Lesevorgänge
		 * @throws InvalidArgumentException $count ist kein Integer.
		 * @return int Die Gesamtanzahl der in den Puffer gelesenen Bytes. Dies
		 *  kann weniger als die Anzahl der angeforderten Bytes sein, wenn diese
		 *  Anzahl an Bytes derzeit nicht verfügbar ist, oder 0, wenn das Ende
		 *  des Streams erreicht ist.
		 * @param string& Nach dem Beenden dieser Methode enthält der Puffer
		 *  count Bytes, aus der aktuellen Quelle.
		 */
		public function read( &$buffer, $count ) {
			if( $this->getHasDisposed() ) {
				throw new BadMethodCallException( 'The filehandle has already been closed!' );
			}
			
			if( !is_int( $count ) ) {
				throw new InvalidArgumentException( 'The parameter $count has to be of type integer!' );
			}
			else if( $count<0 ) {
				throw new OutOfRangeException( 'The parameter $count has to be positiv!' );
			}
			
			if( $count==0 ) {
				$buffer = '';
				return( 0 );
			}
			
			// TODO
			$buffer = fread( $this->_handle, $count );
			return( strlen( $buffer ) );
		}
		
		/**
		 * Legt die aktuelle Position dieses Streams auf den angegebenen Wert
		 * fest.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Suchvorgänge. Dies ist beispielsweise der Fall, wenn der Stream aus
		 *  einer Pipe- oder Konsolenausgabe erstellt wird.
		 * @throws InvalidArgumentException $offset oder $origin ist kein
		 *  Integer, negativ oder $origin ist keine Konstante von SeekOrigin.
		 * @return int Die neue Position innerhalb des aktuellen Streams.
		 * @param int Ein Byteoffset relativ zum origin-Parameter.
		 * @param int Ein Wert aus den Konstanten der Klasse {@link SeekOrigin},
		 *  der den Bezugspunkt angibt, von dem aus die neue Position ermittelt
		 *  wird.
		 */
		public function seek( $offset, $origin ) {
			if( $this->getHasDisposed() ) {
				throw new BadMethodCallException( 'The filehandle has already been closed!' );
			}
			
		}
		
		/**
		 * Legt die Länge dieses Streams auf den angegebenen Wert fest.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Lese- oder Schreibvorgänge. Dies ist beispielsweise der Fall, wenn
		 *  der Stream aus einer Pipe- oder Konsolenausgabe erstellt wird.
		 * @throws InvalidArgumentException $value ist kein Integer oder
		 *  negativ.
		 * @return void
		 * @param int Die gewünschte Länge des aktuellen Streams in Bytes.
		 */
		public function setLength( $value ) {
			
		}
		
		/**
		 * Ermöglicht anderen Prozessen den Zugriff auf die Datei, die zuvor
		 * gesperrt war.
		 * 
		 * @return void
		 */
		public function unlock() {
			
		}
		
		/**
		 * Schreibt einen Block von Bytes unter Verwendung von gepufferten
		 * Daten in diesen Stream.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Schreibvorgänge.
		 * @throws InvalidArgumentException $buffer ist kein String.
		 * @return void
		 * @param string Die Eingabedaten.
		 */
		public function write( $buffer ) {
			fwrite( $this->_handle, $buffer );
		}
		
		/**
		 * Ruft einen Wert ab, der angibt, ob der aktuelle Stream Lesevorgänge
		 * unterstützt.
		 * 
		 * @return boolean true, wenn der Stream Lesevorgänge unterstützt,
		 *  false, wenn der Stream geschlossen ist oder ausschließlich mit
		 *  Schreibzugriff geöffnet wurde.
		 */
		public function getCanRead() {
			return( $this->_access & FileAccess::Read == FileAccess::Read );
		}
		
		/**
		 * Ruft einen Wert ab, der angibt, ob der aktuelle Stream Suchvorgänge
		 * unterstützt.
		 * 
		 * @return boolean true, wenn der Stream Suchvorgänge unterstützt,
		 * false, wenn der Stream geschlossen ist oder der FileStream von einem
		 * Betriebssystemhandle, z. B. einem Pipe oder einer Ausgabe in der
		 * Konsole, erstellt wurde.
		 */
		public function getCanSeek() {
			return( true );
		}
		
		/**
		 * Ruft einen Wert ab, der angibt, ob der aktuelle Stream
		 * Schreibvorgänge unterstützt.
		 * 
		 * @return boolean true, wenn der Stream Schreibvorgänge unterstützt,
		 * false, wenn der Stream geschlossen ist oder mit schreibgeschütztem
		 * Zugriff geöffnet wurde.
		 */
		public function getCanWrite() {
			return( $this->_access & FileAccess::Write == FileAccess::Write );
		}
		
		/**
		 * Ruft das Dateihandle des Betriebssystems für die Datei ab, die durch
		 * das aktuelle FileStream-Objekt gekapselt wird.
		 * 
		 * @return resource Das Dateihandle des Betriebssystems für die vom
		 * FileStream-Objekt gekapselte Datei oder -1, wenn dieser FileStream
		 * geschlossen wurde.
		 */
		public function getHandle() {
			return( $this->_handle );
		}
		
		/**
		 * Ruft die Länge des Streams in Bytes ab.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Suchvorgänge.
		 * @return int Ein Wert, der die Länge des Streams in Bytes darstellt.
		 */
		public function getLength() {
			if( $this->getHasDisposed() ) {
				throw new BadMethodCallException( 'The filehandle has already been closed!' );
			}
			
			return( filesize( $this->_path ) );
		}
		
		/**
		 * Ruft die Position im aktuellen Stream ab.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Suchvorgänge.
		 * @return int Die aktuelle Position innerhalb des Streams.
		 */
		public function getPosition() {
			if( $this->getHasDisposed() ) {
				throw new BadMethodCallException( 'The filehandle has already been closed!' );
			}
			
			return( fpos( $this->_handle ) );
		}
		
		/**
		 * Legt die Position im aktuellen Stream fest.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Suchvorgänge.
		 * @throws InvalidArgumentException $value ist kein Integer oder
		 *  negativ.
		 * @return int Die aktuelle Position innerhalb des Streams.
		 * @param int Die gewünschte Position.
		 */
		public function setPosition( $value ) {
			
		}
		
	}
	
?>