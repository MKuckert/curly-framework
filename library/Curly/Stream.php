<?php

/**
 * Curly_Stream
 * 
 * Wrapperklasse um einen PHP-Datenstrom.
 *
 * In der {@link getHandle Handle}-Eigenschaft ist ein Datenstrom abgelegt,
 * auf dem bequem die Datenstromfunktionen angewendet werden können. Bei
 * der Erstellung einer Instanz wird dazu dem Konstruktor entweder eine
 * gültige Stream-Ressource übergeben oder der Name zu einer Datei, die mit
 * der fopen-Funktion geöffnet werden kann. Der zweite Parameter wird dabei
 * für den Öffnungsmodus verwendet.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly
 * @since 1.0 - 26.04.2008
 * @version 1.2
 */
class Curly_Stream {

	/**
	 * @desc Gibt den Anfang eines Datenstromes an.
	 */
	const OriginBegin = SEEK_SET;

	/**
	 * @desc Gibt die aktuelle Position innerhalb eines Datenstromes an.
	 */
	const OriginCurrent = SEEK_CUR;

	/**
	 * @desc Gibt das Ende eines Datenstromes an.
	 */
	const OriginEnd = SEEK_END;

	/**
	 * @desc Gibt an, das mehrere Prozesse gleichzeitig auf den Datenstrom
	 * zugreifen können, allerdings nur lesende Operationen ausführen
	 * sollten.
	 */
	const LockShared = LOCK_SH;

	/**
	 * @desc Gibt an, das für einen Zugriff gesperrt werden soll.
	 * Andere Prozesse werden so lange gesperrt, bis der Zugriff wieder
	 * freigegeben wird.
	 * Bestehen andere Locks, auch im Shared-Modus, wird so lange gewartet,
	 * bis alle Sperren wieder aufgehoben wurden.
	 * Dieser Modus ist für schreibende Operationen gedacht.
	 */
	const LockExclusive = LOCK_EX;

	/**
	 * @desc Gibt an, das bei bereits vorhandenen Locks nicht auf das Lösen
	 * dieser gewartet werden soll, sondern zum Skript zurückgekehrt werden
	 * soll und kein Lock angefordert wird.
	 * Dieser Parameter kann mit LockShard oder LockExclusiv "verordert"
	 * werden.
	 */
	const LockNoBlocking = LOCK_NB;

	/**
	 * @desc Gibt an, dass die Byte-Reihenfolge für Lese-/
	 * Schreiboperationen nach Little-Endian Byte-Reihenfolge verwendet
	 * werden soll.
	 */
	const LittleEndian = 1;

	/**
	 * @desc Gibt an, dass die Byte-Reihenfolge für Lese-/
	 * Schreiboperationen nach Big-Endian Byte-Reihenfolge verwendet werden
	 * soll.
	 */
	const BigEndian = 1;

	/**
	 * @var resource Referenz auf den zu verwendenden Datenstrom.
	 */
	private $handle = NULL;

	/**
	 * Gibt die Referenz auf den Datenstrom dieser Instanz zurück.
	 * Vorsicht: Der Rückgabewert sollte nicht geschlossen werden, wenn mit
	 * diesem Objekt weitergearbeitet werden soll.
	 *
	 * @return resource
	 */
	public function getHandle() {
		return( $this->handle );
	}

	/**
	 * Ändert den Datenstrom dieser Instanz auf die übergebene Referenz.
	 * Ist der übergebene Parameter kein gültiger Datenstrom, wird eine
	 * InvalidArgumentException geworfen.
	 *
	 * @throws InvalidArgumentException
	 * @return void
	 * @param resource
	 */
	public function setHandle( $handle ) {
		if( $handle===NULL or $this->isValidHandle( $handle ) ) {
			$this->handle = $handle;
		}
		else {
			throw new InvalidArgumentException( 'The given resource is no valid stream resource' );
		}
	}

	/**
	 * @var string Die zu verwendende Zeichenkette in der
	 * {@link writeLine}-Methode.
	 */
	private $newline = PHP_EOL;
	
	/**
	 * Gibt die Zeichenkette zurück, die in der {@link writeLine}-Methode
	 * verwendet wird.
	 *
	 * @return string
	 */
	public function getNewline() {
		return( $this->newline );
	}

	/**
	 * Setzt die Zeichenkette, die in der {@link writeLine}-Methode
	 * verwendet wird.
	 *
	 * @return void
	 * @param string
	 */
	public function setNewline( $newline ) {
		$this->newline = strval( $newline );
	}

	/**
	 * @var integer Die Byte-Reihenfolge für Lese-/Schreibvorgänge in
	 * diesem Datenstrom.
	 */
	private $endian = self::LittleEndian;

	/**
	 * Gibt die aktuelle Byte-Reihenfolge für Lese-/Schreiboperationen in
	 * diesem Datenstrom zurück.
	 *
	 * @return integer Entweder Curly_Stream::LittleEndian oder Curly_Stream::BigEndian
	 */
	public function getEndian() {
		return( $this->endian );
	}

	/**
	 * Setzt die zu verwendende Byte-Reihenfolge für
	 * Lese-/Schreiboperationen in diesem Datenstrom.
	 *
	 * @throws InvalidArgumentException Wenn ein ungültiger Wert übergeben
	 *  wird.
	 * @return void
	 * @param integer Entweder Curly_Stream::LittleEndian oder Curly_Stream::BigEndian
	 */
	public function setEndian( $endian ) {
		if( $endian==self::LittleEndian ) {
			$this->endian = self::LittleEndian;
		}
		else if( $endian==self::BigEndian ) {
			$this->endian = self::BigEndian;
		}
		else {
			throw new InvalidArgumentException( 'The given value is no valid endian' );
		}
	}

	/**
	 * Konstruktor
	 *
	 * Der Datenstrom dieser Instanz wird hier direkt festgelegt.
	 *
	 * Es kann als einziges Argument ein Datenstrom übergeben werden,
	 * welcher der {@link getHandle Handle}-Eigenschaft zugewiesen wird.
	 *
	 * Alternativ kann die URI einer Datenquelle angegeben werden. Mit
	 * Hilfe der fopen-Funktion wird ein Datenstrom zu dieser Quelle
	 * erzeugt. Der Zugriff auf diese Quelle wird über den zweiten
	 * Parameter gesteuert.
	 *
	 * @throws UnexpectedValueException Die open-Methode kann keinen
	 *  Datenstrom auf die übergebene URI erzeugen.
	 * @throws BadMethodCallException Es wurde eine falsche Kombination von
	 * Parametern übergeben.
	 * @param mixed Datenstrom, der in dieser Instanz verwendet werden
	 * soll oder die URI auf die zu verwendende Datenquelle.
	 * @param string Zugriffsmodus auf die Datenquelle.
	 */
	public function __construct( $res, $mode=NULL ) {
		if( is_string( $res ) and $mode!==NULL ) {
			$handle = $this->open( $res, $mode );
			if( $this->isValidHandle( $handle ) ) {
				$this->handle = $handle;
			}
			else {
				throw new UnexpectedValueException( 'Unable to open a stream to the uri "'.$res.'"' );
			}
		}
		else if( $this->isValidHandle( $res ) ) {
			$this->handle = $res;
		}
		else {
			throw new BadMethodCallException( 'Expected a resource parameter or two string parameter' );
		}
	}
	
	/**
	 * Öffnet einen Dateihandle auf die übergebene Ressource.
	 * 
	 * @return ressource
	 * @param string URI auf die zu verwendende Datenquelle.
	 * @param string Zugriffsmodus auf die Datenquelle.
	 */
	protected function open( $url, $mode ) {
		return( fopen( $url, $mode ) );
	}
	
	/**
	 * Schließt diesen Datenstrom.
	 * 
	 * Hinweis: Der Datenstrom darf anschließend weder lesend noch schreibend
	 * verwendet werden.
	 * 
	 * @return void
	 */
	public function close() {
		fclose( $this->handle );
		$this->handle = NULL;
	}
	
	/**
	 * Überprüft, ob dieser Datenstrom geschlossen wurde.
	 * 
	 * @return boolean
	 */
	public function closed() {
		return( $this->handle===NULL );
	}

	/**
	 * Destruktor
	 * Der interne Datenstrom wird geschlossen.
	 *
	 * @return void
	 */
	public function __destruct() {
		if(!$this->closed()) {
			$this->close();
		}
	}

	/**
	 * Überprüft, ob der übergebene Parameter ein gültiger Datenstrom ist.
	 *
	 * @return boolean
	 * @param mixed $handle
	 */
	public function isValidHandle( $handle ) {
		return( $handle===NULL or (is_resource( $handle ) and get_resource_type( $handle )=='stream') );
	}

	/**
	 * Liest einen Byteblock aus dem Datenstrom und gibt diesen zurück.
	 *
	 * @throws OutOfRangeException $length ist negativ
	 * @throws InvalidArgumentException $length ist kein Integer.
	 * @throws Curly_Stream_Exception Wenn ein Lesefehler auftritt.
	 * @return string Die gelesenen Daten.
	 * @param int Die Anzahl zu lesender Bytes. Wird kein Wert übergeben,
	 *  wird 1024 verwendet.
	 */
	public function read( $length=1024 ) {
		if( !is_int( $length ) ) {
			throw new InvalidArgumentException( 'The parameter $length has to be of type integer' );
		}
		else if( $length<0 ) {
			throw new OutOfRangeException( 'The parameter $length has to be positiv' );
		}
			
		if( $length==0 ) {
			$buffer = '';
			return( 0 );
		}
		
		$buffer = $this->_read( $length );
		if( !is_string( $buffer ) ) {
			throw new Curly_Stream_Exception( 'Unable to read from the stream' );
		}
		return( $buffer );
	}

	/**
	 * Liest ein Byte aus dem Datenstrom und erhöht die aktuelle
	 * Leseposition im Datenstrom um ein Byte, oder gibt -1 zurück, wenn
	 * das Ende des Datenstroms erreicht ist.
	 *
	 * @throws Curly_Stream_Exception Wenn ein Lesefehler auftritt.
	 * @return int Das Byte ohne Vorzeichen, oder -1, wenn das Ende des
	 *  Datenstroms erreicht ist.
	 */
	public function readByte() {
		$buffer = $this->read( 1 );
		if( strlen( $buffer )!=1 ) {
			return( -1 );
		}
		else {
			$tmp = unpack( 'C', $buffer );
			return( reset( $tmp ) );
		}
	}

	/**
	 * Liest ein Integer aus dem Datenstrom und erhöht die aktuelle
	 * Leseposition im Datenstrom um die Länge eines Integer, oder gibt
	 * NULL zurück, wenn das Ende des Datenstroms erreicht ist.
	 *
	 * Die einzelnen Bytes werden entsprechend der gesetzten
	 * Byte-Reihenfolge in ein Integer umgewandelt. Die Reihenfolge kann
	 * über die {@link setEndian Endian}-Eigenschaft geändert werden.
	 *
	 * @throws Curly_Stream_Exception Wenn ein Lesefehler auftritt.
	 * @return int Der gelesene Integer, oder NULL, wenn das Ende des
	 *  Datenstroms erreicht ist.
	 */
	public function readInteger() {
		$buffer = $this->read( 4 );
		if( strlen( $buffer )!=4 ) {
			return( null );
		}
			
		$ret = 0;
		if( $this->getEndian()==self::BigEndian ) {
			$ret = unpack( 'N', $buffer );
		}
		else {
			$ret = unpack( 'V', $buffer );
		}
		return( reset( $ret ) );
	}

	/**
	 * Liest eine Zeile aus dem Datenstrom und erhöht die aktuelle
	 * Leseposition im Datenstrom entsprechend. Die gelesene Zeile wird
	 * zurückgegeben.
	 *
	 * Das Ende einer Zeile wird durch {@getNewline Newline} markiert und
	 * ist nicht im Rückgabewert enthalten.
	 *
	 * Durch den Parameter $autodetectNewline wird die Newline-Eigenschaft
	 * ignoriert und Rücksicht auf die PHP-Konfigurationseinstellung
	 * auto_detect_line_endings genommen. Das Zeilenumbruchzeichen ist dann
	 * im Rückgabewert enthalten.
	 *
	 * @throws OutOfRangeException $length ist negativ
	 * @throws InvalidArgumentException $length ist kein Integer.
	 * @throws Curly_Stream_Exception Wenn ein Lesefehler auftritt.
	 * @return string Die gelesenen Daten.
	 * @param int Die Anzahl höchstens zu lesender Bytes. Wird diesem
	 *  Parameter kein Wert übergeben, wird 1024 verwendet.
	 * @param boolean true, wenn das Newline-Zeichen automatisch erkannt werden
	 *  soll. Wird false übergeben, so wird {@link getNewline Newline} als
	 *  Zeichen verwendet.
	 */
	public function readLine( $length=1024, $autodetectNewline=false ) {
		if( !is_int( $length ) ) {
			throw new InvalidArgumentException( 'The parameter $length has to be of type integer' );
		}
		else if( $length<0 ) {
			throw new OutOfRangeException( 'The parameter $length has to be positiv' );
		}
			
		if( $length==0 ) {
			$buffer = '';
			return( 0 );
		}
			
		$buffer = $this->_readline( $length, $autodetectNewline );
			
		if( !is_string( $buffer ) ) {
			throw new Curly_Stream_Exception( 'Unable to read from the stream' );
		}
		return( $buffer );
	}

	/**
	 * Liest alle noch verfügbaren Daten aus dem Datenstrom aus.
	 *
	 * @throws Curly_Stream_Exception Wenn ein Lesefehler auftritt.
	 * @return string Die gelesenen Daten.
	 */
	public function readToEnd() {
		$buffer = '';
		while( $this->getHasMore() ) {
			$buffer .= $this->read();
		}
		return( $buffer );
	}

	/**
	 * Schreibt einen Block von Bytes in diesen Datenstrom.
	 *
	 * @throws InvalidArgumentException $buffer ist kein String.
	 * @throws Curly_Stream_Exception Der Schreibvorgang ist fehlgeschlagen oder wurde
	 *  nicht korrekt ausgeführt.
	 * @return void
	 * @param string Die Eingabedaten.
	 */
	public function write( $value ) {
		$value = Curly_Types_Ensure::string( $value );	
		if( !$this->_write( $value ) ) {
			throw new Curly_Stream_Exception( 'Unable to successfully write all data to the datastream' );
		}
	}

	/**
	 * Schreibt ein Byte an die aktuellen Position im Datenstrom und erhöht
	 * die aktuelle Position im Datenstrom um ein Byte.
	 *
	 * @throws OutOfRangeException $value ist kein Byte (negativ oder
	 *  größer als 255).
	 * @throws InvalidArgumentException $value ist kein Integer.
	 * @throws Curly_Stream_Exception Der Schreibvorgang ist fehlgeschlagen oder wurde
	 *  nicht korrekt ausgeführt.
	 * @return void
	 * @param int Das Byte, das in den Stream geschrieben werden soll.
	 */
	public function writeByte( $value ) {
		$value = Curly_Types_Ensure::int( $value );
		if( $value<0 or $value>255 ) {
			throw new OutOfRangeException( 'The parameter $value has to be in range 0-255' );
		}
			
		$this->write( pack( 'C', $value  ) );
	}

	/**
	 * Schreibt ein Integer in dem Datenstrom und erhöht die aktuelle
	 * Leseposition im Datenstrom um die Länge eines Integer.
	 *
	 * Der Integer wird entsprechend der gesetzten Byte-Reihenfolge in ein
	 * Integer umgewandelt. Die Reihenfolge kann über die
	 * {@link setEndian Endian}-Eigenschaft geändert werden.
	 *
	 * @throws InvalidArgumentException $value ist kein Integer.
	 * @throws Curly_Stream_Exception Der Schreibvorgang ist fehlgeschlagen oder wurde
	 *  nicht korrekt ausgeführt.
	 * @return void
	 * @param int Der Integer, der in den Stream geschrieben werden soll.
	 */
	public function writeInteger( $value ) {
		$value = Curly_Types_Ensure::int( $value );
			
		if( $this->getEndian()==self::BigEndian ) {
			$this->write( pack( 'N', $value  ) );
		}
		else {
			$this->write( pack( 'V', $value  ) );
		}
	}

	/**
	 * Schreibt die Zeichenkette $value in diesen Datenstrom und fügt
	 * {@link getNewline Newline} an.
	 *
	 * @throws InvalidArgumentException $buffer ist kein String.
	 * @throws Curly_Stream_Exception Der Schreibvorgang ist fehlgeschlagen oder wurde
	 *  nicht korrekt ausgeführt.
	 * @return void
	 * @param string Wird dieser Parameter nicht übergeben, wird lediglich
	 *  Newline geschrieben.
	 */
	public function writeLine( $value='' ) {
		$value = Curly_Types_Ensure::string( $value );
			
		$this->write( $value.$this->getNewline() );
	}
	
	/**
	 * Schreibt den Inhalt des übergebenen Streams.
	 *
	 * @throws Curly_Stream_Exception Der Schreibvorgang ist fehlgeschlagen oder wurde
	 *  nicht korrekt ausgeführt.
	 * @return void
	 * @param mixed Curly_Stream-Instanz oder Dateiname.
	 */
	public function writeStream( $value ) {
		if( !( $value instanceof Curly_Stream ) ) {
			$value = new Curly_Stream( Curly_Types_Ensure::string( $value ), 'rb' );
		}
		
		$this->write( $value->readToEnd() );
	}

	/**
	 * Löscht sämtliche Puffer für diesen Datenstrom und veranlasst die
	 * Ausgabe aller gepufferten Daten an das Dateisystem.
	 *
	 * @return void
	 */
	public function flush() {
		fflush( $this->handle );
	}

	/**
	 * Kürzt diesen Datenstrom auf $length Bytes.
	 * Hinweis: Die Position des Dateizeigers wird nicht verändert, so dass
	 * es zu unerwarteten Ergebnissen kommen könnte.
	 *
	 * @throws OutOfRangeException $length ist negativ
	 * @throws InvalidArgumentException $length ist kein Integer.
	 * @throws Curly_Stream_Exception Wenn die Methode fehlschlägt.
	 * @return void
	 * @param int Wird dieser Wert nicht angegeben, wird 0 angenommen.
	 */
	public function truncate( $length=0 ) {
		if( !is_int( $length ) ) {
			throw new InvalidArgumentException( 'The parameter $length has to be of type integer' );
		}
		else if( $length<0 ) {
			throw new OutOfRangeException( 'The parameter $length has to be positiv' );
		}
		
		if( !$this->_truncate( $length ) ) {
			throw new Curly_Stream_Exception( 'Unable to truncate the stream' );
		}
	}

	/**
	 * Sperrt den Zugriff auf diesen Datenstrom für Schreib- oder
	 * Leseoperationen.
	 *
	 * @throws InvalidArgumentException Wenn der $mode-Parameter nicht
	 *  gültig ist.
	 * @return boolean
	 * @param int Sperrmodus. Eine der Lock*-Konstanten.
	 * @param boolean Kann keine Sperre angefordert werden und ist dieser
	 *  Wert true, wird die Skriptausführung nicht bis zur Sperrenerlangung
	 *  blockiert sondern kehrt direkt zurück.
	 */
	public function lock( $mode, $block=true ) {
		if( $mode==self::LockShared or $mode==self::LockExclusive ) {
			if( !$block ) {
				$mode |= self::LockNoBlocking;
			}
			return( flock( $this->handle, $mode ) );
		}
		else {
			throw new InvalidArgumentException( 'The parameter $mode is not valid' );
		}
	}

	/**
	 * Gibt eine Zugriffssperre auf diesen Datenstrom wieder frei.
	 *
	 * @return boolean
	 */
	public function unlock() {
		return( flock( $this->handle, LOCK_UN ) );
	}

	/**
	 * Diese Methode gibt die aktuelle Position des Dateizeigers im
	 * Datenstrom zurück.
	 *
	 * @throws UnexpectedValueException Beim Ermitteln der aktuellen
	 *  Position ist ein Fehler aufgetreten.
	 * @return int
	 */
	public function getPosition() {
		$pos = $this->_tell();
		if( $pos===false ) {
			throw new UnexpectedValueException( 'Unable to determine the actual datastream position' );
		}
		return( $pos );
	}

	/**
	 * Diese Methode versucht die aktuelle Position des Dateizeigers im
	 * Datenstrom zu ändern.
	 *
	 * @throws InvalidArgumentException Es wurde ein ungültiger Parameter
	 *  übergeben.
	 * @throws RuntimeException Dieser Datenstrom erlaubt nicht das
	 *  verändern der Dateizeigerposition.
	 * @return boolean true, wenn die Position erfolgreich gesetzt wurde.
	 * @param int Der zu verwendende Offset, relativ zu der mit $origin
	 *  angegebenen Position.
	 * @param int Die Position, zu der relativ die neue Position gesetzt
	 *  werden soll. Der Wert entspricht einer der Origin*-Konstanten
	 *  dieser Klasse. Wird kein Wert übergeben, wird OriginBegin
	 *  verwendet.
	 */
	public function setPosition( $offset, $origin=self::OriginBegin ) {
		if( !$this->isSeekable() ) {
			throw new RuntimeException( 'This stream does not allow setting of the position' );
		}
			
		if( !is_int( $offset ) ) {
			throw new InvalidArgumentException( 'The parameter $offset has to be an integer' );
		}
		else if(
			!is_int( $origin ) or
			!( $origin==self::OriginBegin or $origin==self::OriginCurrent or $origin==self::OriginEnd )
		) {
			throw new InvalidArgumentException( 'The parameter $origin is not valid' );
		}
			
		return( $this->_seek( $offset, $origin ) );
	}

	/**
	 * Diese Methode versucht die aktuelle Position des Dateizeigers im
	 * Datenstrom auf den Beginn des Stromes zurückzusetzen.
	 *
	 * @throws RuntimeException Dieser Datenstrom erlaubt nicht das
	 *  verändern der Dateizeigerposition.
	 * @return boolean true, wenn die Position erfolgreich zurückgesetzt
	 *  wurde.
	 */
	public function resetPosition() {
		if( !$this->isSeekable() ) {
			throw new RuntimeException( 'This stream does not allow setting of the position' );
		}
			
		return( $this->_resetPosition() );
	}

	/**
	 * Überprüft, ob noch weitere Daten in diesem Datenstrom vorhanden
	 * sind, die durch weitere Leseoperationen ausgelesen werden können.
	 *
	 * @return boolean
	 */
	public function getHasMore() {
		return( !feof( $this->handle ) );
	}

	/**
	 * Diese Methode ermittelt, ob dieser Datenstrom erlaubt, die aktuelle
	 * Dateizeigerposition direkt zu verändern. Gibt diese Methode false
	 * zurück, so kann die {@link setPosition}-Methode nicht verwendet
	 * werden.
	 *
	 * @throws UnexpectedValueException Wenn die Metadaten zu diesem
	 *  Datenstrom nicht ermittelt werden können.
	 * @return boolean
	 */
	public function isSeekable() {
		$meta = $this->getMetaData();
		return( $meta['seekable'] );
	}

	/**
	 * Gibt Metadaten zu diesem Datenstrom zurück.
	 *
	 * Das Rückgabearray enthält folgende Werte:
	 *  - timed_out (bool):
	 * 		true, wenn der Datenstrom einen nicht mehr gültig ist, da nach
	 * 		einer vorangegangenen Leseoperation ein Timeout aufgetreten
	 * 		ist.
	 *  - blocked (bool):
	 * 		true wenn der Datenstrom im IO-Blockingmodus ist.
	 * 		Siehe auch http://php.net/manual/function.stream-set-blocking.php
	 * - eof (bool):
	 * 		true wenn der Datenstrom das Ende der Datei erreicht hat. Bei
	 * 		Datenströmen kann dieser Wert auf true gesetzt sein, obwohl
	 * 		unread_bytes nicht Null ist. Ob weitere Daten zum Lesen im
	 * 		Datenstrom zur Verfügung stehen, kann mit der Methode
	 * 		{@link getHasMore} festgestellt werden.
	 * - unread_bytes (int):
	 * 		Die Anzahl Bytes im internen PHP-Buffer dieses Datenstromes.
	 * 		Dieser Wert sollte nur zu Informationszwecken verwendet werden.
	 * - stream_type (string):
	 * 		Der Typ der internen Implementierung des Datenstromes.
	 * 		Z.B.: STDIO oder tcp_socket
	 * - wrapper_type (string):
	 * 		Typ des PHP-Wrappers um die interne Implementierung.
	 * 		Z.B.: plainfile oder http
	 * - wrapper_data (mixed):
	 * 		Zusätzliche Daten des Datenstromes. Diese hängen vom
	 * 		verwendeten Wrapper ab.
	 * - filters (array):
	 * 		Liste aller Filter, die auf den Datenstrom angewendet werden.
	 * - mode (string):
	 * 		Der Modus, mit der ein Datenstrom geöffnet werden muss, um die
	 * 		gleichen Lese-/Schreibeigenschaften wie dieser Datenstrom zu
	 * 		erhalten.
	 * - seekable (bool):
	 * 		Flag ob dieser Datenstrom das Setzen der Dateizeigerposition
	 * 		unterstützt.
	 * - uri (string):
	 * 		Die URI bzw. der Dateiname, zu der dieser Datenstrom gehört.
	 *
	 * @throws UnexpectedValueException Wenn die Metadaten nicht ermittelt
	 *  werden können.
	 * @return array
	 */
	public function getMetaData() {
		$meta = $this->_getMetaData();
		if( !is_array( $meta ) ) {
			throw new UnexpectedValueException( 'Unable to determine the streams metadata' );
		}
		
		// Sicherstellen, das jeder dokumentierte Index vorhanden ist und
		// notfalls durch Standardwerte ergänzen
		if( !isset( $meta['timed_out'] ) ) {
			$meta['timed_out'] = false;
		}
		if( !isset( $meta['blocked'] ) ) {
			$meta['blocked'] = false;
		}
		if( !isset( $meta['eof'] ) ) {
			$meta['eof'] = $this->getHasMore();
		}
		if( !isset( $meta['unread_bytes'] ) ) {
			$meta['unread_bytes'] = 0;
		}
		if( !isset( $meta['stream_type'] ) ) {
			$meta['stream_type'] = 'Stream';
		}
		if( !isset( $meta['wrapper_type'] ) ) {
			$meta['wrapper_type'] = 'Stream';
		}
		if( !isset( $meta['wrapper_data'] ) ) {
			$meta['wrapper_data'] = '';
		}
		if( !isset( $meta['filters'] ) ) {
			$meta['filters'] = array();
		}
		if( !isset( $meta['mode'] ) ) {
			$meta['mode'] = 'a+';
		}
		if( !isset( $meta['seekable'] ) ) {
			$meta['seekable'] = false;
		}
		if( !isset( $meta['uri'] ) ) {
			$meta['uri'] = 'unknown';
		}
			
		return( $meta );
	}
	
	/**************************************************************************
	 * Implementierungen
	 *************************************************************************/
	
	/**
	 * Führt intern die eigentliche Leseoperation durch.
	 * 
	 * @return string Die gelesenen Daten.
	 * @param int Die Anzahl zu lesender Bytes.
	 */
	protected function _read( $length ) {
		return( fread( $this->handle, $length ) );
	}

	/**
	 * Führt intern die eigentliche Leseoperation von einer Zeile durch.
	 * 
	 * @return string Die gelesenen Daten.
	 * @param int Die Anzahl zu lesender Bytes.
	 * @param boolean true, wenn das Newline-Zeichen automatisch erkannt werden
	 *  soll. Wird false übergeben, so wird {@link getNewline Newline} als
	 *  Zeichen verwendet.
	 */
	protected function _readline( $length, $autodetectNewline ) {
		if( $autodetectNewline ) {
			return( fgets( $this->handle, $length ) );
		}
		else {
			return( stream_get_line( $this->handle, $length, $this->getNewline() ) );
		}
	}
	
	/**
	 * Führt intern die eigentliche Schreiboperation durch.
	 * 
	 * @return boolean
	 * @param string Die Eingabedaten.
	 */
	protected function _write( $value ) {
		return( fwrite( $this->handle, $value, strlen( $value ) )!==false );
	}
	
	/**
	 * Gibt die aktuelle Position des Dateizeigers im Datenstrom zurück.
	 * 
	 * @return integer
	 */
	protected function _tell() {
		return( ftell( $this->handle ) );
	}

	/**
	 * Setzt die aktuelle Position des Dateizeigers im Datenstrom um.
	 *
	 * @return boolean
	 * @param int Der zu verwendende Offset, relativ zu der mit $origin
	 *  angegebenen Position.
	 * @param int Die Position, zu der relativ die neue Position gesetzt werden
	 *  soll.
	 */
	protected function _seek( $offset, $origin ) {
		return( fseek( $this->handle, $offset, $origin )==0 );
	}
	
	/**
	 * Gibt intern die Metadaten zurück.
	 * 
	 * @return array
	 */
	protected function _getMetaData() {
		return( stream_get_meta_data( $this->handle ) );
	}
	
	/**
	 * Führt intern die truncate-Operation aus.
	 * 
	 * @return boolean
	 * @param integer
	 */
	protected function _truncate( $length ) {
		return( ftruncate( $this->handle, $length ) );
	}
	
	/**
	 * Führt intern die resetPosition-Operation aus.
	 * 
	 * @return boolean
	 */
	protected function _resetPosition() {
		return(rewind( $this->handle )==1);
	}
	
}

?>