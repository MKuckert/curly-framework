<?php

/**
 * CurlyX_Db_Connection_Interface
 * 
 * Basis-Interface für alle CurlyX_Db_Connection_*-Klassen.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db.Connection
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
interface CurlyX_Db_Connection_Interface {
	
	const PARAM_BOOL = 5;
	const PARAM_NULL = 0;
	const PARAM_INT = 1;
	const PARAM_STR = 2;
	const PARAM_LOB = 3;
	const PARAM_STMT = 4;
	const PARAM_INPUT_OUTPUT = 2147483648;
	const PARAM_EVT_ALLOC = 0;
	const PARAM_EVT_FREE = 1;
	const PARAM_EVT_EXEC_PRE = 2;
	const PARAM_EVT_EXEC_POST = 3;
	const PARAM_EVT_FETCH_PRE = 4;
	const PARAM_EVT_FETCH_POST = 5;
	const PARAM_EVT_NORMALIZE = 6;
	const FETCH_LAZY = 1;
	const FETCH_ASSOC = 2;
	const FETCH_NUM = 3;
	const FETCH_BOTH = 4;
	const FETCH_OBJ = 5;
	const FETCH_BOUND = 6;
	const FETCH_COLUMN = 7;
	const FETCH_CLASS = 8;
	const FETCH_INTO = 9;
	const FETCH_FUNC = 10;
	const FETCH_GROUP = 65536;
	const FETCH_UNIQUE = 196608;
	const FETCH_KEY_PAIR = 12;
	const FETCH_CLASSTYPE = 262144;
	const FETCH_SERIALIZE = 524288;
	const FETCH_PROPS_LATE = 1048576;
	const FETCH_NAMED = 11;
	const ATTR_AUTOCOMMIT = 0;
	const ATTR_PREFETCH = 1;
	const ATTR_TIMEOUT = 2;
	const ATTR_ERRMODE = 3;
	const ATTR_SERVER_VERSION = 4;
	const ATTR_CLIENT_VERSION = 5;
	const ATTR_SERVER_INFO = 6;
	const ATTR_CONNECTION_STATUS = 7;
	const ATTR_CASE = 8;
	const ATTR_CURSOR_NAME = 9;
	const ATTR_CURSOR = 10;
	const ATTR_ORACLE_NULLS = 11;
	const ATTR_PERSISTENT = 12;
	const ATTR_STATEMENT_CLASS = 13;
	const ATTR_FETCH_TABLE_NAMES = 14;
	const ATTR_FETCH_CATALOG_NAMES = 15;
	const ATTR_DRIVER_NAME = 16;
	const ATTR_STRINGIFY_FETCHES = 17;
	const ATTR_MAX_COLUMN_LEN = 18;
	const ATTR_EMULATE_PREPARES = 20;
	const ATTR_DEFAULT_FETCH_MODE = 19;
	const ERRMODE_SILENT = 0;
	const ERRMODE_WARNING = 1;
	const ERRMODE_EXCEPTION = 2;
	const CASE_NATURAL = 0;
	const CASE_LOWER = 2;
	const CASE_UPPER = 1;
	const NULL_NATURAL = 0;
	const NULL_EMPTY_STRING = 1;
	const NULL_TO_STRING = 2;
	const ERR_NONE = 00000;
	const FETCH_ORI_NEXT = 0;
	const FETCH_ORI_PRIOR = 1;
	const FETCH_ORI_FIRST = 2;
	const FETCH_ORI_LAST = 3;
	const FETCH_ORI_ABS = 4;
	const FETCH_ORI_REL = 5;
	const CURSOR_FWDONLY = 0;
	const CURSOR_SCROLL = 1;
	const PGSQL_ATTR_DISABLE_NATIVE_PREPARED_STATEMENT = 1000;
	const MYSQL_ATTR_USE_BUFFERED_QUERY = 1000;
	const MYSQL_ATTR_LOCAL_INFILE = 1001;
	const MYSQL_ATTR_INIT_COMMAND = 1002;
	const MYSQL_ATTR_READ_DEFAULT_FILE = 1003;
	const MYSQL_ATTR_READ_DEFAULT_GROUP = 1004;
	const MYSQL_ATTR_MAX_BUFFER_SIZE = 1005;
	const MYSQL_ATTR_DIRECT_QUERY = 1006;

	/**
	 * Konstruktor
	 * 
	 * @param string Datasourcename
	 * @param string Benutzername
	 * @param string Passwort
	 * @param array Treiberspezifische Optionen
	 */
	public function __construct($dsn, $username=NULL, $password=NULL, array $driver_options=array());
	
	/**
	 * Startet eine Transaktion,
	 * 
	 * @return boolean
	 */
	public function beginTransaction();
	
	/**
	 * Beendet eine Transaktion und übernimmt alle Datensatz-Änderungen.
	 * 
	 * @return boolean
	 */
	public function commit();
	
	/**
	 * Gibt den SQLSTATE der letzten Operation am Datenbank-Handle zurück.
	 * 
	 * @return string
	 */
	public function errorCode();
	
	/**
	 * Gibt erweiterte Fehlerinformationen zu der letzten Operation am
	 * Datenbank-Handle zurück.
	 * 
	 * @return array
	 */
	public function errorInfo();
	
	/**
	 * Führt das übergebene SQL-Statement aus und gibt die Anzahl der
	 * beeinflussten Zeilen zurück.
	 * 
	 * @return int
	 * @param string
	 */
	public function exec($statement);
	
	/**
	 * Gibt den Wert eines Datenbankverbindungsattributes zurück.
	 * 
	 * @return mixed
	 * @param int
	 */
	public function getAttribute($attribute);
	
	/**
	 * Gibt ein Array mit den verfügbaren Treibernamen zurück.
	 * 
	 * @return array
	 */
	public function getAvailableDrivers();
	
	/**
	 * Gibt die zuletzt erzeugte ID zurück.
	 * 
	 * @return string
	 * @param string Optionaler Name der Sequenz. Von manchen DBMS benötigt.
	 */
	public function lastInsertId($name=NULL);
	
	/**
	 * Bereitet ein SQL-Statement für die Ausführung vor und gibt ein
	 * Statement-Objekt zurück.
	 * 
	 * @return CurlyX_Db_Statement_Interface
	 * @param string SQL-Statement
	 * @param array Treiberspezifische Optionen
	 */
	public function prepare($statement, array $driver_options=array());
	
	/**
	 * Führt ein SQL-Statement aus und gibt ein Statement-Objekt für die
	 * Ergebnisabfrage zurück.
	 * 
	 * @return CurlyX_Db_Statement_Interface
	 * @param string SQL-Statement
	 */
	public function query($statement);
	
	/**
	 * Maskiert den übergebenen Parameter für die Nutzung in einer Query.
	 * 
	 * @return string
	 * @param string Der Parameter
	 * @param int Typ des Parameters
	 */
	public function quote($string, $parameter_type=self::PARAM_STR);
	
	/**
	 * Beendet die aktuelle Transaktion und verwirft alle Änderungen.
	 * 
	 * @return boolean
	 */
	public function rollBack();
	
	/**
	 * Setzt ein Datenbankverbindungsattribut
	 * 
	 * @return boolean
	 * @param int
	 * @param mixed
	 */
	public function setAttribute($attribute, $value);
	
}