<?php
	
	/**
	 * CurlyX_Db_EPDO
	 * 
	 * Extended PHP Data Objects
	 * 
	 * Stellt einen Wrapper um eine PDO-Instanz dar, damit die PDO-Klasse
	 * durch weitere Funktionen erweitert werden kann.
	 * 
	 * Features, die durch diese Wrapperklasse hinzukommen:
	 *  - Serialisierbarkeit
	 *  - Direktes Setzen und Auslesen von Attributen durch entsprechende
	 *    Methoden
	 *  - Flexibler Auf- und Abbau der Datenbankverbindung
	 * 
	 * Einige Methoden benötigen eine verbundene PDO-Instanz, d.h. bei dem
	 * Aufruf einer solchen Methoden kann es dazu kommen, dass eine
	 * PDOException geworfen wird, wenn die Zugangsdaten noch nicht oder nur
	 * fehlerhaft gesetzt wurden.
	 * 
	 * Beispiel für die Anwendung:
	 * <code>
	 * 	$db = new CurlyX_Db_EPDO( 'mysql:host=localhost;dbname=test', 'root', '' );
	 * 	$db->ErrorMode = PDO::ERRMODE_EXCEPTION;
	 * 	$db->DefaultFetchMode = PDO::FETCH_LAZY;
	 * 	$db->Charset = 'UTF-8';
	 * 	$db->DefaultStatics = array( 'TABLE' => 'channel' );
	 * 	
	 * 	foreach( $db->query( 'select * from $TABLE$' ) as $row ) {
	 * 		echo $row->channel_id.' - '.$row->name."\n";
	 * 	}
	 * 	
	 * 	// Datenbankobjekt kann serialisiert werden.
	 * 	$serial = serialize( $db );
	 * 	$db = unserialize( $serial );
	 * 	
	 * 	foreach( $db->query( 'select * from $TABLE$' ) as $row ) {
	 * 		echo $row->channel_id.' - '.$row->name."\n";
	 * 	}
	 * </code>
	 * 
	 * @author Martin Kuckert
	 */
	class CurlyX_Db_EPDO extends CurlyX_Types_Object {
		
		/**
		 * @desc Zeichenkette, die als Begrenzer von statischen Parametern
		 * verwendet wird.
		 */
		const STATIC_PARAM_DELIMITER = '$';
		
		/**
		 * @var PDO Die aktuelle PDO-Instanz.
		 */
		private $_pdoInstance = null;
		
		/**
		 * @var string Der Name der zu verwendenden Datenquelle.
		 */
		private $_dsn = '';
		
		/**
		 * @var string Der Benutzername, mit dem auf die Datenquelle
		 * zugegriffen werden kann.
		 */
		private $_username = '';
		
		/**
		 * @var string Das Passwort, mit dem auf die Datenquelle zugegriffen
		 * werden kann.
		 */
		private $_password = '';
		
		/**
		 * @var array Treiberspezifische Optionen
		 */
		private $_driverOptions = array();
		
		/**
		 * @var array Für die PDO-Instanz zu verwendende Attribute.
		 */
		private $_attributes = array();
		
		/**
		 * @var string Setzt den zu verwendenden Zeichensatz für die
		 * Datenbankverbindung.
		 */
		private $_charset = '';
		
		/**
		 * @var integer Der Standard-Rückgabemodus
		 */
		private $_defaultFetchMode = PDO::FETCH_ASSOC;
		
		/**
		 * @var array Liste von statischen Standardparametern.
		 */
		private $_defaultStatics = array();
		
		/**
		 * @var array Liste der gültigen Attributnamen.
		 */
		private $ATTRIBUTENAMES = array(
			PDO::ATTR_AUTOCOMMIT,
			PDO::ATTR_CASE,
			PDO::ATTR_CLIENT_VERSION,
			PDO::ATTR_CONNECTION_STATUS,
			PDO::ATTR_CURSOR_NAME,
			PDO::ATTR_CURSOR,
			PDO::ATTR_DRIVER_NAME,
			PDO::ATTR_ERRMODE,
			PDO::ATTR_FETCH_CATALOG_NAMES,
			PDO::ATTR_FETCH_TABLE_NAMES,
			PDO::ATTR_MAX_COLUMN_LEN,
			PDO::ATTR_ORACLE_NULLS,
			PDO::ATTR_PERSISTENT,
			PDO::ATTR_PREFETCH,
			PDO::ATTR_SERVER_INFO,
			PDO::ATTR_SERVER_VERSION,
			PDO::ATTR_STATEMENT_CLASS,
			PDO::ATTR_STRINGIFY_FETCHES,
			PDO::ATTR_TIMEOUT,
			PDO::MYSQL_ATTR_DIRECT_QUERY,
			PDO::MYSQL_ATTR_INIT_COMMAND,
			PDO::MYSQL_ATTR_LOCAL_INFILE,
			PDO::MYSQL_ATTR_MAX_BUFFER_SIZE,
			PDO::MYSQL_ATTR_READ_DEFAULT_FILE,
			PDO::MYSQL_ATTR_READ_DEFAULT_GROUP,
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY
		);
		
		/**
		 * Konstruktor
		 * Die Eigenschaften {@link setDataSourceName DataSourceName},
		 * {@link setUsername Username}, {@link setPassword Password} und
		 * {@link setDriverOptions DriverOptions} können im Konstruktor direkt
		 * gesetzt werden.
		 */
		public function __construct( $dsn='', $username='', $password='', array $driverOptions=array() ) {
			$v = phpversion();
			
			if( version_compare( $v, '5.1.3', '>=' ) ) {
				$this->ATTRIBUTENAMES[] = PDO::ATTR_EMULATE_PREPARES;
			}
			
			if( version_compare( $v, '5.2.0', '>=' ) ) {
				$this->ATTRIBUTENAMES[] = PDO::ATTR_DEFAULT_FETCH_MODE;
			}
			
			$dsn = Ensure::string( $dsn );
			$username = Ensure::string( $username );
			$password = Ensure::string( $password );
			
			if( $dsn!='' ) {
				$this->setDataSourceName( $dsn );
			}
			if( $username!='' ) {
				$this->setUsername( $username );
			}
			if( $password!='' ) {
				$this->setPassword( $password );
			}
			if( count( $driverOptions )>0 ) {
				$this->setDataSourceName( $driverOptions );
			}
		}
		
		/**
		 * Magische Methode, die vor dem Serialisieren ausgeführt wird.
		 * 
		 * @return array
		 */
		public function __sleep() {
			return( array( '_dsn', '_username', '_password', '_driverOptions', '_attributes', '_charset', '_defaultFetchMode', '_defaultStatics' ) );
		}
		
		/**
		 * Gibt die aktuelle PDO-Instanz zurück.
		 * 
		 * @throws PDOException Es kann keine Verbindung zur Datenquelle
		 *  hergestellt werden.
		 * @return PDO
		 */
		public function getPdoInstance() {
			if( is_null( $this->_pdoInstance ) ) {
				$this->connect();
			}
			return( $this->_pdoInstance );
		}
		
		/**
		 * Setzt die aktuelle PDO-Instanz.
		 * 
		 * @return void
		 * @param PDO
		 */
		public function setPdoInstance( PDO $pdo ) {
			$this->_pdoInstance = $pdo;
		}
		
		/**
		 * Löscht die aktuelle PDO-Instanz.
		 * 
		 * @return void
		 */
		public function removePdoInstance() {
			$this->_pdoInstance = null;
		}
		
		/**
		 * Gibt einen boolschen Wert zurück, ob diese Instanz über eine
		 * verbundene PDO-Instanz verfügt.
		 * 
		 * @return boolean
		 */
		public function getIsConnected() {
			return( $this->_pdoInstance instanceof PDO );
		}
		
		/**
		 * Gibt den Namen der zu verwendenden Datenquelle zurück.
		 * 
		 * @return string
		 */
		public function getDataSourceName() {
			return( $this->_dsn );
		}
		
		/**
		 * Setzt den Namen der zu verwendenden Datenquelle.
		 * 
		 * @return void
		 * @param string
		 */
		public function setDataSourceName( $dsn ) {
			$this->_dsn = Ensure::string( $dsn );
		}
		
		/**
		 * Gibt den Benutzernamen, mit dem auf die Datenquelle zugegriffen
		 * werden kann, zurück.
		 * 
		 * @return string
		 */
		public function getUsername() {
			return( $this->_username );
		}
		
		/**
		 * Setzt den Benutzernamen, mit dem auf die Datenquelle zugegriffen
		 * werden kann.
		 * 
		 * @return void
		 * @param string
		 */
		public function setUsername( $username ) {
			$this->_username = Ensure::string( $username );
		}
		
		/**
		 * Gibt das Passwort, mit dem auf die Datenquelle zugegriffen werden
		 * kann, zurück.
		 * 
		 * @return string
		 */
		public function getPassword() {
			return( $this->_password );
		}
		
		/**
		 * Setzt das Passwort, mit dem auf die Datenquelle zugegriffen werden
		 * kann.
		 * 
		 * @return void
		 * @param string
		 */
		public function setPassword( $password ) {
			$this->_password = Ensure::string( $password );
		}
		
		/**
		 * Gibt die treiberspezifischen Optionen zurück.
		 * 
		 * @return array&
		 */
		public function getDriverOptions() {
			return $this->_driverOptions;
		}
		
		/**
		 * Setzt die treiberspezifischen Optionen.
		 * 
		 * @return void
		 * @param array
		 */
		public function setDriverOptions( array $options ) {
			$this->_driverOptions = $options;
		}
		
		/**
		 * Gibt die für die PDO-Instanz zu verwendenden Attributen zurück.
		 * 
		 * @return array&
		 */
		protected function &getAttributes() {
			return $this->_attributes;
		}
		
		/**
		 * Gibt den Wert zu einem Attribut zurück.
		 * 
		 * @return mixed Null, wenn der Wert nicht ermittelt werden kann.
		 * @param int Der Name des Attributes.
		 */
		public function getAttribute( $attribute ) {
			if( $this->getIsConnected() ) {
				return( $this->getPdoInstance()->getAttribute( $attribute ) );
			}
			else if( isset( $this->_attributes[$attribute] ) ) {
				return( $this->_attributes[$attribute] );
			}
			else {
				return( null );
			}
		}
		
		/**
		 * Fügt dieser Instanz ein Attribut hinzu.
		 * 
		 * @return boolean true, wenn der Wert erfolgreich gesetzt wurde.
		 * @param int Der Name des zu setzenden Attributes.
		 * @param mixed Der Wert für dieses Attribut
		 */
		public function setAttribute( $attribute, $value ) {
			if( !in_array( $attribute, $this->ATTRIBUTENAMES, true ) ) {
				return( false );
			}
			
			if( $this->getIsConnected() and !$this->getPdoInstance()->setAttribute( $attribute, $value ) ) {
				return( false );
			}
			
			$this->_attributes[$attribute] = $value;
			return( true );
		}
		
		/**
		 * Gibt den für die Datenbankverbindung zu verwendenden Zeichensatz
		 * zurück.
		 * 
		 * @return string
		 */
		public function getCharset() {
			return( $this->_charset );
		}
		
		/**
		 * Setzt den für die Datenbankverbindung zu verwendenden Zeichensatz.
		 * 
		 * @return void
		 * @param string
		 */
		public function setCharset( $charset ) {
			$this->_charset = Ensure::string( $charset );
		}
		
		/**
		 * Setzt den zu verwendenden Zeichensatz für die aktuelle PDO-Instanz.
		 * 
		 * @return void 
		 */
		protected function applyCharset() {
			$charset = $this->getCharset();
			if( $charset=='' ) {
				return;
			}
			
			switch( $this->getDriverName() ) {
				case 'mysql':
					$this->getPdoInstance()->exec( 'set character set '.$charset );
					break;
			}
		}
		
		/**
		 * Weißt der aktuellen PDO-Instanz die zu verwendenden Attribute zu.
		 * 
		 * @return void
		 */
		protected function applyAttributes() {
			foreach( $this->getAttributes() as $attribute=>$value ) {
				$this->getPdoInstance()->setAttribute( $attribute, $value );
			}
		}
		
		/**
		 * Erstellt eine neue PDO-Instanz und verbindet sich mit der
		 * Datenquelle.
		 * 
		 * @throws PDOException Es kann keine Verbindung zur Datenquelle
		 *  hergestellt werden.
		 * @return void
		 */
		public function connect() {
			$this->setPdoInstance( new PDO( $this->getDataSourceName(), $this->getUsername(), $this->getPassword(), $this->getDriverOptions() ) );
			$this->applyCharset();
			$this->applyAttributes();
		}
		
		/**
		 * Trennt die aktuelle Verbindung zur Datenquelle.
		 * 
		 * @return void
		 */
		public function disconnect() {
			$this->removePdoInstance();
		}
		
		/**
		 * Gibt den Wert der Datenbankeinstellung Autocommit zurück.
		 * 
		 * @return boolean
		 */
		public function getAutoCommit() {
			return( $this->getAttribute( PDO::ATTR_AUTOCOMMIT ) );
		}
		
		/**
		 * Setzt den Wert der Datenbankeinstellung Autocommit.
		 * 
		 * @return boolean
		 * @param boolean Der Standardwert ist false
		 */
		public function setAutoCommit( $autocommit=false ) {
			return( $this->setAttribute( Ensure::boolean( $autocommit ) ) );
		}
		
		/**
		 * Gibt zurück, ob und wie die Groß-/Kleinschreibung von Spaltennamen
		 * konvertiert werden soll und gibt einen der folgenden Werte zurück.
		 * - PDO::CASE_NATURAL: Es wird keine Konvertierung vorgenommen.
		 * - PDO::CASE_LOWER: Spaltennamen werden in Großschreibweise
		 *  zurückgegeben.
		 * - PDO::CASE_UPPER: Spaltennamen werden in Kleinschreibweise
		 *  zurückgegeben.
		 * 
		 * @return int
		 */
		public function getColumnCase() {
			return( $this->getAttribute( PDO::ATTR_CASE ) );
		}
		
		/**
		 * Legt fest, ob und wie Spaltennamen in Groß-/Kleinschreibung
		 * konvertiert werden sollen.
		 * 
		 * @return boolean
		 * @param int Siehe {@link getColumnCase} für die möglichen Werte.
		 */
		public function setColumnCase( $case ) {
			return( $this->setAttribute( PDO::ATTR_CASE, $case ) );
		}
		
		/**
		 * Gibt die Clientversion der aktuellen PDO-Instanz zurück.
		 * 
		 * @return string
		 */
		public function getClientVersion() {
			return( $this->getPdoInstance()->getAttribute( PDO::ATTR_CLIENT_VERSION ) );
		}
		
		/**
		 * Gibt den Verbindungsstatus der aktuellen PDO-Instanz zurück.
		 * 
		 * @return string
		 */
		public function getConnectionStatus() {
			return( $this->getPdoInstance()->getAttribute( PDO::ATTR_CONNECTION_STATUS ) );
		}
		
		/**
		 * Gibt den Treibernamen der aktuellen PDO-Instanz zurück.
		 * 
		 * @return string
		 */
		public function getDriverName() {
			return( $this->getPdoInstance()->getAttribute( PDO::ATTR_DRIVER_NAME ) );
		}
		
		/**
		 * Gibt den Fehlermodus der aktuelle PDO-Instanz zurück.
		 * 
		 * Es wird einer der folgenden Werte zurückgegeben:
		 * - PDO::ERRMODE_SILENT: Es wird keine Fehlermeldung oder Exception
		 *  ausgelöst. Es muss per Hand auf Fehler überprüft werden.
		 * - PDO::ERRMODE_WARNING: Es wird im Fehlerfall eine
		 *  E_WARNING-Fehlermeldung ausgelöst.
		 * - PDO::ERRMODE_EXCEPTION: Es wird im Fehlerfall eine PDOException
		 *  geworfen.
		 * 
		 * @return int
		 */
		public function getErrorMode() {
			return( $this->getAttribute( PDO::ATTR_ERRMODE ) );
		}
		
		/**
		 * Setzt den Fehlermodus der aktuelle PDO-Instanz.
		 * 
		 * @return boolean
		 * @param int Fehlermodus (Siehe {@link getErrorMode} für die
		 *  möglichen Werte).
		 */
		public function setErrorMode( $mode ) {
			return( $this->setAttribute( PDO::ATTR_ERRMODE, $mode ) );
		}
		
		/**
		 * Gibt den Standard-Rückgabemodus für PreparedStatements zurück.
		 * 
		 * @return integer
		 */
		public function getDefaultFetchMode() {
			return( $this->_defaultFetchMode );
		}
		
		/**
		 * Setzt den Standard-Rückgabemodus für PreparedStatements.
		 * 
		 * @return void
		 * @param integer
		 */
		public function setDefaultFetchMode( $mode ) {
			$this->_defaultFetchMode = $mode;
		}
		
		/**
		 * Gibt die Liste von statischen Standardparametern zurück.
		 * 
		 * @return array&
		 */
		public function &getDefaultStatics() {
			return $this->_defaultStatics;
		}
		
		/**
		 * Setzt die Liste von statischen Standardparametern.
		 * 
		 * @return void
		 * @param array&
		 */
		public function setDefaultStatics( $statics ) {
			$this->_defaultStatics = Ensure::arr( $statics );
		}
		
		/**
		 * Überprüft, ob leere Zeichenketten bei dem Laden von Daten zu NULL
		 * konvertiert werden.
		 * 
		 * @return boolean
		 */
		public function getConvertToNull() {
			return( $this->getAttribute( PDO::ATTR_ORACLE_NULLS ) );
		}
		
		/**
		 * Legt fest, ob leere Zeichenketten bei dem Laden von Daten zu NULL
		 * konvertiert werden sollen.
		 * 
		 * @return boolean
		 * @param boolean
		 */
		public function setConvertToNull( $convert=true ) {
			return( $this->setAttribute( PDO::ATTR_ORACLE_NULLS, $convert ) );
		}
		
		/**
		 * Überprüft, ob die aktuelle PDO-Instanz eine persistente Verbindung
		 * darstellt.
		 * 
		 * @return boolean
		 */
		public function getIsPersistent() {
			return( $this->getPdoInstance()->getAttribute( PDO::ATTR_PERSISTENT ) );
		}
		
		/**
		 * Gibt Informationen über die aktuelle Datenbankverbindung zurück.
		 * 
		 * @return string
		 */
		public function getServerInfo() {
			return( $this->getPdoInstance()->getAttribute( PDO::ATTR_SERVER_INFO ) );
		}
		
		/**
		 * Gibt die Serverversion der aktuellen Datenbankverbindung zurück.
		 * 
		 * @return string
		 */
		public function getServerVersion() {
			return( $this->getPdoInstance()->getAttribute( PDO::ATTR_SERVER_VERSION ) );
		}
		
		/**
		 * Gibt den Timeout-Wert für die Kommunikation mit dem Datenbankserver
		 * zurück.
		 * 
		 * @return int 
		 */
		public function getTimeout() {
			return( $this->getAttribute( PDO::ATTR_TIMEOUT ) );
		}
		
		/**
		 * Setzt den Timeout-Wert für die Kommunikation mit dem
		 * Datenbankserver.
		 * 
		 * @return boolean
		 * @param int 
		 */
		public function setTimeout( $timeout ) {
			return( $this->setAttribute( PDO::ATTR_TIMEOUT, Ensure::int( $timeout ) ) );
		}
		
		/**
		 * Gibt ein Array mit dem Fehlercode und der Fehlermeldung des letzten
		 * Fehlers der aktuellen PDO-Instanz zurück.
		 * 
		 * Das Rückgabearray enthält folgende Daten:
		 * - 0: SQLSTATE Fehlercode (Fünf-Zeichen-Identifier nach ANSI-SQL-Standard).
		 * - 1: Treiberspezifischer Fehlercode
		 * - 2: Treiberspezifische Fehlermeldung
		 * 
		 * Ist noch kein Fehler aufgetreten, wird null zurückgegeben.
		 * 
		 * @return array
		 */
		public function getLastError() {
			$info = $this->getPdoInstance()->errorInfo();
			if( !is_array( $info ) or !isset( $info[0] ) or $info[0]=='' ) {
				return null;
			}
			else {
				return( $info );
			}
		}
		
		/**
		 * Beginnt eine neue Transaktion.
		 * 
		 * Die Transaktion kann durch die {@link commitTransaction}-Methode
		 * beendet oder durch die {@link rollBackTransaction}-Methode
		 * rückgängig gemacht werden.
		 * 
		 * @return boolean true, wenn die Transaktion erfolgreich gestartet
		 *  wurde.
		 */
		public function beginTransaction() {
			return( $this->getPdoInstance()->beginTransaction() );
		}
		
		/**
		 * Beendet die aktuelle Transaktion und übernimmt alle Änderungen.
		 * 
		 * @return boolean true, wenn die Transaktion erfolgreich beendet
		 *  wurde.
		 */
		public function commitTransaction() {
			return( $this->getPdoInstance()->commit() );
		}
		
		/**
		 * Beendet die aktuelle Transaktion und macht alle Änderungen
		 * rückgängig.
		 * 
		 * @return boolean true, wenn die Transaktion erfolgreich beendet
		 *  wurde.
		 */
		public function rollBackTransaction() {
			return( $this->getPdoInstance()->rollBack() );
		}
		
		/**
		 * Führt ein SQL-Statement aus und gibt die Anzahl beeinflusster Zeilen
		 * zurück.
		 * 
		 * @return int
		 */
		public function exec( $statement ) {
			return( $this->getPdoInstance()->exec( $statement ) );
		}
		
		/**
		 * Gibt die ID der zuletzt eingefügten Zeile oder Sequenznummer zurück.
		 * 
		 * Je nach PDO-Treiber ist es nötig, den Namen des Sequenzobjektes mit
		 * anzugeben. Unterstützt der PDO-Treiber keine Werte mit automatisch
		 * fortlaufender Nummer, ist der Rückgabewert ohne Bedeutung.
		 * 
		 * @return int
		 * @param string Name des Sequenzobjektes.
		 */
		public function getLastInsertId( $sequenceName='' ) {
			return( $this->getPdoInstance()->lastInsertId( $sequenceName ) );
		}
		
		/**
		 * Bereitet ein SQL-Statement als PDOStatement-Objekt vor und gibt
		 * dieses zurück.
		 * 
		 * Innerhalb eines PDOStatement können Platzhalter in der Form :name
		 * oder als ? (Fragezeichen) markiert werden. Diese können anschließend
		 * über eine der bind-Methoden des PDOStatement-Objektes ersetzt
		 * werden.
		 * 
		 * Diese Klasse erweitert das erstellen von PreparedStatements um
		 * statische Parameter. Diese werden vor dem Erstellen des
		 * PDOStatement-Objektes durch entsprechende Werte ersetzt.
		 * Ein statischer Parameter muss im SQL-Statement in der Form $name$
		 * angegeben werden. Mit dem Parameter $statics können solchen
		 * Parametern Werte zugewiesen werden.
		 * 
		 * Beispiel für die Verwendung von statischen Parametern:
		 * <code>
		 * 	$epdo = new EPDO( $dsn, $username, $password );
		 *  
		 *  $query = 'select $rows$ from $table$';
		 *  $params = array(
		 *      'rows'  => 'id, name, value',
		 * 		'table' => 'mytable'
		 *  );
		 *  $statement = $epdo->prepare( $query, $params );
		 * </code>
		 * 
		 * @throws PDOException Der Datenbanktreiber unterstützt keine
		 *  PreparedStatements
		 * @return PDOStatement
		 * @param string Das vorzubereitende SQL-Statement
		 * @param array Statische Parameter, die im SQL-Statement ersetzt
		 *  werden sollen.
		 * @param array Treiberspezifische Optionen
		 */
		public function prepare( $statement, array $statics=array(), array $driver_options=array() ) {
			$statement = $this->replaceStaticParameter( $statement, $statics );
			$stm = $this->getPdoInstance()->prepare( $statement, $driver_options );
			
			// Treiber unterstützt keine PreparedStatements
			if( $stm===false ) {
				throw new PDOException( 'The database driver is not supporting prepared statements' ); 
			}
			
			$stm->setFetchMode( $this->getDefaultFetchMode() );
			
			return( $stm );
		}
		
		/**
		 * Fügt ein SQL-Statement direkt aus und gibt das Ergebnis zurück.
		 * 
		 * @throws PDOException Der Datenbanktreiber unterstützt keine
		 *  PreparedStatements
		 * @return PDOStatement
		 * @param string Das vorzubereitende SQL-Statement
		 * @param array Dynamische Parameter
		 * @param array Statische Parameter, die im SQL-Statement ersetzt
		 *  werden sollen.
		 * @param array Treiberspezifische Optionen
		 */
		public function query( $statement, array $params=array(), array $statics=array(), array $driver_options=array() ) {
			$stm = $this->prepare( $statement, $statics, $driver_options );
			
			if( $stm->execute( $params ) ) {
				return( $stm );
			}
			else {
				return( null );
			}
		}
		
		/**
		 * Maskiert den übergebenen Parameter so, dass dieser direkt in einem
		 * SQL-Statement verwendet werden kann.
		 * 
		 * @return string Gibt false zurück, wenn der Datenbanktreiber keine
		 *  solche Maskierung unterstützt.
		 * @param string Der zu maskierende Parameter
		 * @param int Typ des Parameters (Einer der PDO::PARAM_*-Werte)
		 */
		public function quote( $param, $type=PDO::PARAM_STR ) {
			return( $this->getPdoInstance()->quote( $param, $type ) );
		}
		
		/**
		 * Ersetzt statische Parameter in dem übergebenen SQL-Statement mit den
		 * übergebenen Werten.
		 * 
		 * Diese Klasse erweitert das erstellen von PreparedStatements um
		 * statische Parameter. Diese werden vor dem Erstellen des
		 * PDOStatement-Objektes durch entsprechende Werte ersetzt.
		 * Ein statischer Parameter muss im SQL-Statement in der Form $name$
		 * angegeben werden. Mit dem Parameter $statics können solchen
		 * Parametern Werte zugewiesen werden.
		 * 
		 * {@internal Bei der prepare-Methode ist ein Beispiel zu finden}}
		 * 
		 * @return string
		 * @param string
		 * @param array
		 */
		public function replaceStaticParameter( $statement, array $statics ) {
			$statics = array_merge( $this->_defaultStatics, $statics );
			
			// statische Parameter vorbereiten
			foreach( $statics as $key=>$value ) {
				$statement = str_replace( self::STATIC_PARAM_DELIMITER.$key.self::STATIC_PARAM_DELIMITER, Ensure::string( $value ), $statement );
			}
			
			return( $statement );
		}
		
	}
	
?>