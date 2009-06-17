<?php
/**
* @desc Represents a connection between PHP and a database server.
*/
class PDO {

    /**
     * @desc Represents a boolean data type. 
     */
    const PARAM_BOOL = 0;
    /**
     * @desc Represents the SQL NULL data type. 
     */
    const PARAM_NULL = 0;
    /**
     * @desc Represents the SQL INTEGER data type. 
     */
    const PARAM_INT  = 0;
    /**
     * @desc Represents the SQL CHAR, VARCHAR, or other string data type. 
     */
    const PARAM_STR  = 0;
    /**
     * @desc Represents the SQL large object data type. 
     */
    const PARAM_LOB  = 0;
    /**
     * @desc Represents a recordset type. Not currently supported by any drivers. 
     */
    const PARAM_STMT = 0;
    /**
     * @desc Specifies that the parameter is an INOUT parameter for a stored procedure. You must bitwise-OR this value with an explicit PDO::PARAM_* data type
     */
    const PARAM_INPUT_OUTPUT = 0;
    /**
     * @desc Specifies that the fetch method shall return each row as an object with variable names that correspond to the column names returned in the result set. PDO::FETCH_LAZY creates the object variable names as they are accessed.
     */
    const FETCH_LAZY = 0;
    /**
     * @desc Specifies that the fetch method shall return each row as an array indexed by column name as returned in the corresponding result set. If the result set contains multiple columns with the same name, PDO::FETCH_ASSOC returns only a single value per column name. 
     */
    const FETCH_ASSOC = 0;
    /**
     * @desc Specifies that the fetch method shall return each row as an array indexed by column name as returned in the corresponding result set. If the result set contains multiple columns with the same name, PDO::FETCH_NAMED returns an array of values per column name.
     */
    const FETCH_NAMED = 0;
    /**
     * @desc Specifies that the fetch method shall return each row as an array indexed by column number as returned in the corresponding result set, starting at column 0. 
     */
    const FETCH_NUM  = 0;
    /**
     * @desc Specifies that the fetch method shall return each row as an array indexed by both column name and number as returned in the corresponding result set, starting at column 0. 
     */
    const FETCH_BOTH = 0;
    /**
     * @desc Specifies that the fetch method shall return each row as an object with property names that correspond to the column names returned in the result set. 
     */
    const FETCH_OBJ  = 0;
    /**
     * @desc Specifies that the fetch method shall return TRUE and assign the values of the columns in the result set to the PHP variables to which they were bound with the PDOStatement::bindParam() or PDOStatement::bindColumn() methods. 
     */
    const FETCH_BOUND = 0;
    /**
     * @desc Specifies that the fetch method shall return only a single requested column from the next row in the result set. 
     */
    const FETCH_COLUMN = 0;
    /**
     * @desc Specifies that the fetch method shall return a new instance of the requested class, mapping the columns to named properties in the class. 
     */
    const FETCH_CLASS = 0;
    /**
     * @desc Specifies that the fetch method shall update an existing instance of the requested class, mapping the columns to named properties in the class.
     */
    const FETCH_INTO = 0;
    /**
     * @desc 
     */
    const FETCH_FUNC = 0;
    /**
     * @desc 
     */
    const FETCH_GROUP = 0;
    /**
     * @desc 
     */
    const FETCH_UNIQUE = 0;
    /**
     * @desc Fetch into an array where the 1st column is a key and all subsequent columns are values 
     */
    const FETCH_KEY_PAIR = 0;
    /**
     * @desc 
     */
    const FETCH_CLASSTYPE = 0;
    /**
     * @desc As PDO::FETCH_INTO but object is provided as a serialized string. Available since PHP 5.1.0. 
     */
    const FETCH_SERIALIZE = 0;
    /**
     * @desc Available since PHP 5.2.0 
     */
    const FETCH_PROPS_LATE = 0;
    /**
     * @desc If this value is FALSE, PDO attempts to disable autocommit so that the connection begins a transaction. 
     */
    const ATTR_AUTOCOMMIT = 0;
    /**
     * @desc Setting the prefetch size allows you to balance speed against memory usage for your application. Not all database/driver combinations support setting of the prefetch size. A larger prefetch size results in increased performance at the cost of higher memory usage. 
     */
    const ATTR_PREFETCH = 0;
    /**
     * @desc  Sets the timeout value in seconds for communications with the database. 
     */
    const ATTR_TIMEOUT = 0;
    /**
     * @desc See the Errors and error handling section for more information about this attribute. 
     */
    const ATTR_ERRMODE = 0;
    /**
     * @desc  This is a read only attribute; it will return information about the version of the database server to which PDO is connected
     */
    const ATTR_SERVER_VERSION = 0;
    /**
     * @desc This is a read only attribute; it will return information about the version of the client libraries that the PDO driver is using. 
     */
    const ATTR_CLIENT_VERSION = 0;
    /**
     * @desc This is a read only attribute; it will return some meta information about the database server to which PDO is connected. 
     */
    const ATTR_SERVER_INFO = 0;
    /**
     * @desc 
     */
    const ATTR_CONNECTION_STATUS = 0;
    /**
     * @desc Force column names to a specific case specified by the PDO::CASE_* constants. 
     */
    const ATTR_CASE  = 0;
    /**
     * @desc Get or set the name to use for a cursor. Most useful when using scrollable cursors and positioned updates. 
     */
    const ATTR_CURSOR_NAME = 0;
    /**
     * @desc Selects the cursor type. PDO currently supports either PDO::CURSOR_FWDONLY and PDO::CURSOR_SCROLL. Stick with PDO::CURSOR_FWDONLY unless you know that you need a scrollable cursor.
     */
    const ATTR_CURSOR = 0;
    /**
     * @desc Returns the name of the driver.
     */
    const ATTR_DRIVER_NAME = 0;
    /**
     * @desc Convert empty strings to SQL NULL values on data fetches. 
     */
    const ATTR_ORACLE_NULLS = 0;
    /**
     * @desc Request a persistent connection, rather than creating a new connection. See Connections and Connection management for more information on this attribute. 
     */
    const ATTR_PERSISTENT = 0;
    /**
     * @desc 
     */
    const ATTR_STATEMENT_CLASS = 0;
    /**
     * @desc Prepend the containing catalog name to each column name returned in the result set. The catalog name and column name are separated by a decimal (.) character. Support of this attribute is at the driver level; it may not be supported by your driver. 
     */
    const ATTR_FETCH_CATALOG_NAMES = 0;
    /**
     * @desc Prepend the containing table name to each column name returned in the result set. The table name and column name are separated by a decimal (.) character. Support of this attribute is at the driver level; it may not be supported by your driver. 
     */
    const ATTR_FETCH_TABLE_NAMES = 0;
    /**
     * @desc 
     */
    const ATTR_STRINGIFY_FETCHES = 0;
    /**
     * @desc 
     */
    const ATTR_MAX_COLUMN_LEN = 0;
    /**
     * @desc Available since PHP 5.2.0 
     */
    const ATTR_DEFAULT_FETCH_MODE = 0;
    /**
     * @desc  Available since PHP 5.1.3. 
     */
    const ATTR_EMULATE_PREPARES = 0;
    /**
     * @desc  Do not raise an error or exception if an error occurs. The developer is expected to explicitly check for errors. This is the default mode. See Errors and error handling for more information about this attribute. 
     */
    const ERRMODE_SILENT = 0;
    /**
     * @desc Issue a PHP E_WARNING message if an error occurs. See Errors and error handling for more information about this attribute. 
     */
    const ERRMODE_WARNING = 0;
    /**
     * @desc Throw a PDOException if an error occurs. See Errors and error handling for more information about this attribute
     */
    const ERRMODE_EXCEPTION = 0;
    /**
     * @desc Leave column names as returned by the database driver. 
     */
    const CASE_NATURAL = 0;
    /**
     * @desc Force column names to lower case. 
     */
    const CASE_LOWER = 0;
    /**
     * @desc Force column names to upper case. 
     */
    const CASE_UPPER = 0;
    /**
     * @desc 
     */
    const NULL_NATURAL = 0;
    /**
     * @desc 
     */
    const NULL_EMPTY_STRING = 0;
    /**
     * @desc 
     */
    const NULL_TO_STRING = 0;
    /**
     * @desc Fetch the next row in the result set. Valid only for scrollable cursors. 
     */
    const FETCH_ORI_NEXT = 0;
    /**
     * @desc Fetch the previous row in the result set. Valid only for scrollable cursors. 
     */
    const FETCH_ORI_PRIOR = 0;
    /**
     * @desc Fetch the first row in the result set. Valid only for scrollable cursors. 
     */
    const FETCH_ORI_FIRST = 0;
    /**
     * @desc Fetch the last row in the result set. Valid only for scrollable cursors. 
     */
    const FETCH_ORI_LAST = 0;
    /**
     * @desc Fetch the requested row by row number from the result set. Valid only for scrollable cursors. 
     */
    const FETCH_ORI_ABS = 0;
    /**
     * @desc  Fetch the requested row by relative position from the current position of the cursor in the result set. Valid only for scrollable cursors.
     */
    const FETCH_ORI_REL = 0;
    /**
     * @desc Create a PDOStatement object with a forward-only cursor. This is the default cursor choice, as it is the fastest and most common data access pattern in PHP. 
     */
    const CURSOR_FWDONLY = 0;
    /**
     * @desc Create a PDOStatement object with a scrollable cursor. Pass the PDO::FETCH_ORI_* constants to control the rows fetched from the result set. 
     */
    const CURSOR_SCROLL = 0;
    /**
     * @desc Corresponds to SQLSTATE '00000', meaning that the SQL statement was successfully issued with no errors or warnings. This constant is for your convenience when checking PDO::errorCode() or PDOStatement::errorCode() to determine if an error occurred. You will usually know if this is the case by examining the return code from the method that raised the error condition anyway. 
     */
    const ERR_NONE   = 0;
    /**
     * @desc Allocation event 
     */
    const PARAM_EVT_ALLOC = 0;
    /**
     * @desc Deallocation event 
     */
    const PARAM_EVT_FREE = 0;
    /**
     * @desc Event triggered prior to execution of a prepared statement. 
     */
    const PARAM_EVT_EXEC_PRE = 0;
    /**
     * @desc Event triggered subsequent to execution of a prepared statement. 
     */
    const PARAM_EVT_EXEC_POST = 0;
    /**
     * @desc Event triggered prior to fetching a result from a resultset. 
     */
    const PARAM_EVT_FETCH_PRE = 0;
    /**
     * @desc Event triggered subsequent to fetching a result from a resultset. 
     */
    const PARAM_EVT_FETCH_POST = 0;
    /**
     * @desc 
     */
    const PARAM_EVT_NORMALIZE = 0;
    /**
    * @desc begins a transaction
    */
    public function beginTransaction() { }

    /**
     * @desc Creates a PDO instance representing a connection to a database
     */
    public function __construct( $dsn, $username=null, $password=null, $driver_options=null ) { }
    /**
     * @desc commits a transaction
     * @return boolean
     */
    public function commit() { }
    /**
     * @desc retrieves an error code, if any, from the database
     * @return string
     */
    public function errorCode() { }
    /**
     * @desc retrieves an array of error information, if any, from the database
     * @return array
     */
    public function errorInfo() { }
    /**
     * @desc issues an SQL statement and returns the number of affected rows
     * @return int
     * @param string
     */
    public function exec( $statement ) { }
    /**
     * @desc retrieves a database connection attribute
     * @return mixed
     * @param integer
     */
    public function getAttribute( $attribute ) { }
    /**
     * @desc Return an array of available PDO drivers
     * @return array 
     */
    public function getAvailableDrivers() { }
    /**
     * @desc retrieves the value of the last row that was inserted into a table
     * @return string
     * @param string Name of the sequence object from which the ID should be returned.
     */
    public function lastInsertId( $name=null ) { }
    /**
     * @desc prepares an SQL statement for execution
     * @return PDOStatement
     * @param string
     * @param array
     */
    public function prepare( $statement, $driver_options=null ) { }
    /**
     * @desc issues an SQL statement and returns a result set
     * class PDO {
     * 	PDOStatement query ( string $statement )
     * }
     * class PDO {
     * 	PDOStatement query ( string $statement, int $PDO::FETCH_COLUMN, int $colno )
     * }
     * class PDO {
     * 	PDOStatement query ( string $statement, int $PDO::FETCH_CLASS, string $classname, array $ctorargs )
     * }
     * class PDO {
     * 	PDOStatement query ( string $statement, int $PDO::FETCH_INTO, object $object )
     * }
     * 
     * @return PDOStatement
     */
    public function query( $statement ) { }
    /**
     * @desc returns a quoted version of a string for use in SQL statements
     * 
     * @return string
     * @param string
     * @param int
     */
    public function quote( $string, $parameter_type=null ) { }
    /**
     * @desc roll back a transaction
     * @param boolean
     */
    public function rollBack() { }
    /**
     * @desc sets a database connection attribute
     * @return boolean
     * @param int
     * @param mixed
     */
    public function setAttribute( $attribute, $value ) { }
}

/**
* @desc Represents a prepared statement and, after the statement is executed, an associated result set.
*/
class PDOStatement {
    /**
    * @desc binds a PHP variable to an output column in a result set
    * @return boolean
    * @param mixed
    * @param mixed&
    * @param int
    */
    public function bindColumn( $column, $param, $type=null ) { }
    /**
    * @desc binds a PHP variable to a parameter in the prepared statement
    * @return boolean
    * @param mixed
    * @param mixed&
    * @param int
    * @param int
    * @param mixed
    */
    public function bindParam( $parameter, $variable, $data_type=null, $length=null, $driver_options=null ) { }
    /**
    * @desc Binds a value to a parameter
    * @return boolean
    * @param mixed
    * @param mixed
    * @param int
    */
    public function bindValue( $parameter, $value, $data_type=null ) { }
    /**
    * @desc closes the cursor, allowing the statement to be executed again
   * @return boolean
    */
    public function closeCursor() { }
    /**
    * @desc returns the number of columns in the result set
  * @return int
    */
    public function columnCount() { }
    /**
    * @desc retrieves an error code, if any, from the statement
  * @return string
    */
    public function errorCode() { }
    /**
    * @desc retrieves an array of error information, if any, from the statement
  * @return array
    */
    public function errorInfo() { }
    /**
    * @desc executes a prepared statement
    * @return boolean
    * @param array
    */
    public function execute( $input_parameters=null ) { }
    /**
    * @desc fetches a row from a result set
    * @return mixed
    * @param int
    * @param int
    * @param int
    */
    public function fetch( $fetch_style=null, $cursor_orientation=null, $cursor_offset=null ) { }
    /**
    * @desc fetches an array containing all of the rows from a result set
      * @return array
      * @param int
      * @param int
      * @param array
    */
    public function fetchAll( $fetch_style=null, $column_index=null, $ctor_args=null ) { }
    /**
    * @desc returns the data from a single column in a result set
      * @return string
      * @param int
    */
    public function fetchColumn( $column_number=null ) { }
    /**
    * @desc Fetches the next row and returns it as an object.
      * @return mixed
      * @param string
      * @param array
    */
    public function fetchObject( $class_name=null, $ctor_args=null ) { }
    /**
    * @desc retrieves a PDOStatement attribute
    * @return mixed
    * @param int
    */
    public function getAttribute( $attribute ) { }
    /**
    * @desc retrieves metadata for a column in the result set
    * native_type	The PHP native type used to represent the column value.
    * driver:decl_type	The SQL type used to represent the column value in the database. If the column in the result set is the result of a function, this
    * value is not returned by PDOStatement->getColumnMeta().
    * flags	Any flags set for this column.
    * name	The name of this column as returned by the database.
    * table	The name of this column's table as returned by the database.
    * len	The length of this column. Normally -1 for types other than floating point decimals.
    * precision	The numeric precision of this column. Normally 0 for types other than floating point decimals.
    * pdo_type	The type of this column as represented by the PDO::PARAM_* constants.
    * @return array
    * @param int
    */
    public function getColumnMeta( $column ) { }
    /**
    * @desc retrieves the next rowset (result set)
    * @return boolean
    */
    public function nextRowset() { }
    /**
    * @desc returns the number of rows that were affected by the execution of an SQL statement
    * @return integer
    */
    public function rowCount() { }
    /**
    * @desc sets a PDOStatement attribute
    * @return boolean
    * @param int
    * @param mixed
    */
    public function setAttribute( $attribute, $value ) { }
    /**
    * @desc sets the fetch mode for a PDOStatement 
    * class PDOStatement {
    *  bool setFetchMode ( int $mode )
    * }
    * class PDOStatement {
    *  bool setFetchMode ( int $PDO::FETCH_COLUMN, int $colno )
    * }
    * class PDOStatement {
    *  bool setFetchMode ( int $PDO::FETCH_CLASS, string $classname, array $ctorargs )
    * }
    * class PDOStatement {
    *  bool setFetchMode ( int $PDO::FETCH_INTO, object $object )
    * }
    * @return boolean
    */
    public function setFetchMode() { }
}

class PDOException extends Exception {
    public $errorInfo = null; // corresponds to PDO::errorInfo()
    // or PDOStatement::errorInfo()
    protected $message; // textual error message
    // use Exception::getMessage() to access it
    protected $code; // SQLSTATE error code
// use Exception::getCode() to access it
}
?>
