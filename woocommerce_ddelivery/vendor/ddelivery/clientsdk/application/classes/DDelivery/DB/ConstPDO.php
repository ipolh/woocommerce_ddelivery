<?php
/**
 * User: dnap
 * Date: 01.10.14
 * Time: 23:56
 */

namespace DDelivery\DB;


use Exception;

if(class_exists('\PDO')){
    /**
     * Class PDO Переопределение класса PDO если модуль отсутствует в системе
     */
    class ConstPDO extends \PDO {
        public function __construct(){
            throw new Exception('You can not create this class');
        }
    }
}else {
    class ConstPDO
    {
        /**
         * Represents a boolean data type.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_BOOL = 5;

        /**
         * Represents the SQL NULL data type.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_NULL = 0;

        /**
         * Represents the SQL INTEGER data type.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_INT = 1;

        /**
         * Represents the SQL CHAR, VARCHAR, or other string data type.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_STR = 2;

        /**
         * Represents the SQL large object data type.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_LOB = 3;

        /**
         * Represents a recordset type. Not currently supported by any drivers.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_STMT = 4;

        /**
         * Specifies that the parameter is an INOUT parameter for a stored
         * procedure. You must bitwise-OR this value with an explicit
         * PDO::PARAM_* data type.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_INPUT_OUTPUT = 2147483648;

        /**
         * Allocation event
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_EVT_ALLOC = 0;

        /**
         * Deallocation event
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_EVT_FREE = 1;

        /**
         * Event triggered prior to execution of a prepared statement.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_EVT_EXEC_PRE = 2;

        /**
         * Event triggered subsequent to execution of a prepared statement.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_EVT_EXEC_POST = 3;

        /**
         * Event triggered prior to fetching a result from a resultset.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_EVT_FETCH_PRE = 4;

        /**
         * Event triggered subsequent to fetching a result from a resultset.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_EVT_FETCH_POST = 5;

        /**
         * Event triggered during bound parameter registration
         * allowing the driver to normalize the parameter name.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const PARAM_EVT_NORMALIZE = 6;

        /**
         * Specifies that the fetch method shall return each row as an object with
         * variable names that correspond to the column names returned in the result
         * set. <b>PDO::FETCH_LAZY</b> creates the object variable names as they are accessed.
         * Not valid inside <b>PDOStatement::fetchAll</b>.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_LAZY = 1;

        /**
         * Specifies that the fetch method shall return each row as an array indexed
         * by column name as returned in the corresponding result set. If the result
         * set contains multiple columns with the same name,
         * <b>PDO::FETCH_ASSOC</b> returns
         * only a single value per column name.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_ASSOC = 2;

        /**
         * Specifies that the fetch method shall return each row as an array indexed
         * by column number as returned in the corresponding result set, starting at
         * column 0.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_NUM = 3;

        /**
         * Specifies that the fetch method shall return each row as an array indexed
         * by both column name and number as returned in the corresponding result set,
         * starting at column 0.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_BOTH = 4;

        /**
         * Specifies that the fetch method shall return each row as an object with
         * property names that correspond to the column names returned in the result
         * set.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_OBJ = 5;

        /**
         * Specifies that the fetch method shall return TRUE and assign the values of
         * the columns in the result set to the PHP variables to which they were
         * bound with the <b>PDOStatement::bindParam</b> or
         * <b>PDOStatement::bindColumn</b> methods.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_BOUND = 6;

        /**
         * Specifies that the fetch method shall return only a single requested
         * column from the next row in the result set.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_COLUMN = 7;

        /**
         * Specifies that the fetch method shall return a new instance of the
         * requested class, mapping the columns to named properties in the class.
         * The magic
         * <b>__set</b>
         * method is called if the property doesn't exist in the requested class
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_CLASS = 8;

        /**
         * Specifies that the fetch method shall update an existing instance of the
         * requested class, mapping the columns to named properties in the class.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_INTO = 9;

        /**
         * Allows completely customize the way data is treated on the fly (only
         * valid inside <b>PDOStatement::fetchAll</b>).
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_FUNC = 10;

        /**
         * Group return by values. Usually combined with
         * <b>PDO::FETCH_COLUMN</b> or
         * <b>PDO::FETCH_KEY_PAIR</b>.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_GROUP = 65536;

        /**
         * Fetch only the unique values.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_UNIQUE = 196608;

        /**
         * Fetch a two-column result into an array where the first column is a key and the second column
         * is the value. Available since PHP 5.2.3.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_KEY_PAIR = 12;

        /**
         * Determine the class name from the value of first column.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_CLASSTYPE = 262144;

        /**
         * As <b>PDO::FETCH_INTO</b> but object is provided as a serialized string.
         * Available since PHP 5.1.0. Since PHP 5.3.0 the class constructor is never called if this
         * flag is set.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_SERIALIZE = 524288;

        /**
         * Call the constructor before setting properties. Available since PHP 5.2.0.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_PROPS_LATE = 1048576;

        /**
         * Specifies that the fetch method shall return each row as an array indexed
         * by column name as returned in the corresponding result set. If the result
         * set contains multiple columns with the same name,
         * <b>PDO::FETCH_NAMED</b> returns
         * an array of values per column name.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_NAMED = 11;

        /**
         * If this value is <b>FALSE</b>, PDO attempts to disable autocommit so that the
         * connection begins a transaction.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_AUTOCOMMIT = 0;

        /**
         * Setting the prefetch size allows you to balance speed against memory
         * usage for your application. Not all database/driver combinations support
         * setting of the prefetch size. A larger prefetch size results in
         * increased performance at the cost of higher memory usage.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_PREFETCH = 1;

        /**
         * Sets the timeout value in seconds for communications with the database.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_TIMEOUT = 2;

        /**
         * See the Errors and error
         * handling section for more information about this attribute.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_ERRMODE = 3;

        /**
         * This is a read only attribute; it will return information about the
         * version of the database server to which PDO is connected.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_SERVER_VERSION = 4;

        /**
         * This is a read only attribute; it will return information about the
         * version of the client libraries that the PDO driver is using.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_CLIENT_VERSION = 5;

        /**
         * This is a read only attribute; it will return some meta information about the
         * database server to which PDO is connected.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_SERVER_INFO = 6;
        const ATTR_CONNECTION_STATUS = 7;

        /**
         * Force column names to a specific case specified by the PDO::CASE_*
         * constants.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_CASE = 8;

        /**
         * Get or set the name to use for a cursor. Most useful when using
         * scrollable cursors and positioned updates.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_CURSOR_NAME = 9;

        /**
         * Selects the cursor type. PDO currently supports either
         * <b>PDO::CURSOR_FWDONLY</b> and
         * <b>PDO::CURSOR_SCROLL</b>. Stick with
         * <b>PDO::CURSOR_FWDONLY</b> unless you know that you need a
         * scrollable cursor.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_CURSOR = 10;

        /**
         * Convert empty strings to SQL NULL values on data fetches.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_ORACLE_NULLS = 11;

        /**
         * Request a persistent connection, rather than creating a new connection.
         * See Connections and Connection
         * management for more information on this attribute.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_PERSISTENT = 12;
        const ATTR_STATEMENT_CLASS = 13;

        /**
         * Prepend the containing table name to each column name returned in the
         * result set. The table name and column name are separated by a decimal (.)
         * character. Support of this attribute is at the driver level; it may not
         * be supported by your driver.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_FETCH_TABLE_NAMES = 14;

        /**
         * Prepend the containing catalog name to each column name returned in the
         * result set. The catalog name and column name are separated by a decimal
         * (.) character. Support of this attribute is at the driver level; it may
         * not be supported by your driver.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_FETCH_CATALOG_NAMES = 15;

        /**
         * Returns the name of the driver.
         * <p>
         * using <b>PDO::ATTR_DRIVER_NAME</b>
         * <code>
         * if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
         * echo "Running on mysql; doing something mysql specific here\n";
         * }
         * </code>
         * </p>
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_DRIVER_NAME = 16;
        const ATTR_STRINGIFY_FETCHES = 17;
        const ATTR_MAX_COLUMN_LEN = 18;

        /**
         * Available since PHP 5.1.3.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_EMULATE_PREPARES = 20;

        /**
         * Available since PHP 5.2.0
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ATTR_DEFAULT_FETCH_MODE = 19;

        /**
         * Do not raise an error or exception if an error occurs. The developer is
         * expected to explicitly check for errors. This is the default mode.
         * See Errors and error handling
         * for more information about this attribute.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ERRMODE_SILENT = 0;

        /**
         * Issue a PHP <b>E_WARNING</b> message if an error occurs.
         * See Errors and error handling
         * for more information about this attribute.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ERRMODE_WARNING = 1;

        /**
         * Throw a <b>PDOException</b> if an error occurs.
         * See Errors and error handling
         * for more information about this attribute.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ERRMODE_EXCEPTION = 2;

        /**
         * Leave column names as returned by the database driver.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const CASE_NATURAL = 0;

        /**
         * Force column names to lower case.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const CASE_LOWER = 2;

        /**
         * Force column names to upper case.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const CASE_UPPER = 1;
        const NULL_NATURAL = 0;
        const NULL_EMPTY_STRING = 1;
        const NULL_TO_STRING = 2;

        /**
         * Corresponds to SQLSTATE '00000', meaning that the SQL statement was
         * successfully issued with no errors or warnings. This constant is for
         * your convenience when checking <b>PDO::errorCode</b> or
         * <b>PDOStatement::errorCode</b> to determine if an error
         * occurred. You will usually know if this is the case by examining the
         * return code from the method that raised the error condition anyway.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const ERR_NONE = 00000;

        /**
         * Fetch the next row in the result set. Valid only for scrollable cursors.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_ORI_NEXT = 0;

        /**
         * Fetch the previous row in the result set. Valid only for scrollable
         * cursors.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_ORI_PRIOR = 1;

        /**
         * Fetch the first row in the result set. Valid only for scrollable cursors.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_ORI_FIRST = 2;

        /**
         * Fetch the last row in the result set. Valid only for scrollable cursors.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_ORI_LAST = 3;

        /**
         * Fetch the requested row by row number from the result set. Valid only
         * for scrollable cursors.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_ORI_ABS = 4;

        /**
         * Fetch the requested row by relative position from the current position
         * of the cursor in the result set. Valid only for scrollable cursors.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const FETCH_ORI_REL = 5;

        /**
         * Create a <b>PDOStatement</b> object with a forward-only cursor. This is the
         * default cursor choice, as it is the fastest and most common data access
         * pattern in PHP.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const CURSOR_FWDONLY = 0;

        /**
         * Create a <b>PDOStatement</b> object with a scrollable cursor. Pass the
         * PDO::FETCH_ORI_* constants to control the rows fetched from the result set.
         * @link http://php.net/manual/en/pdo.constants.php
         */
        const CURSOR_SCROLL = 1;

        /**
         * If this attribute is set to <b>TRUE</b> on a
         * <b>PDOStatement</b>, the MySQL driver will use the
         * buffered versions of the MySQL API. If you're writing portable code, you
         * should use <b>PDOStatement::fetchAll</b> instead.
         * <p>
         * Forcing queries to be buffered in mysql
         * <code>
         * if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
         * $stmt = $db->prepare('select * from foo',
         * array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
         * } else {
         * die("my application only works with mysql; I should use \$stmt->fetchAll() instead");
         * }
         * </code>
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_USE_BUFFERED_QUERY = 1000;

        /**
         * <p>
         * Enable LOAD LOCAL INFILE.
         * </p>
         * <p>
         * Note, this constant can only be used in the <i>driver_options</i>
         * array when constructing a new database handle.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_LOCAL_INFILE = 1001;

        /**
         * <p>
         * Command to execute when connecting to the MySQL server. Will
         * automatically be re-executed when reconnecting.
         * </p>
         * <p>
         * Note, this constant can only be used in the <i>driver_options</i>
         * array when constructing a new database handle.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_INIT_COMMAND = 1002;

        /**
         * <p>
         * Maximum buffer size. Defaults to 1 MiB. This constant is not supported when
         * compiled against mysqlnd.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_MAX_BUFFER_SIZE = 1005;

        /**
         * <p>
         * Read options from the named option file instead of from
         * my.cnf. This option is not available if
         * mysqlnd is used, because mysqlnd does not read the mysql
         * configuration files.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_READ_DEFAULT_FILE = 1003;

        /**
         * <p>
         * Read options from the named group from my.cnf or the
         * file specified with <b>MYSQL_READ_DEFAULT_FILE</b>. This option
         * is not available if mysqlnd is used, because mysqlnd does not read the mysql
         * configuration files.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_READ_DEFAULT_GROUP = 1004;

        /**
         * <p>
         * Enable network communication compression. This is not supported when
         * compiled against mysqlnd.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_COMPRESS = 1006;

        /**
         * <p>
         * Perform direct queries, don't use prepared statements.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_DIRECT_QUERY = 1007;

        /**
         * <p>
         * Return the number of found (matched) rows, not the
         * number of changed rows.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_FOUND_ROWS = 1008;

        /**
         * <p>
         * Permit spaces after function names. Makes all functions
         * names reserved words.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_IGNORE_SPACE = 1009;
        const MYSQL_ATTR_SSL_KEY = 1010;

        /**
         * <p>
         * The file path to the SSL certificate.
         * </p>
         * <p>
         * This exists as of PHP 5.3.7.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_SSL_CERT = 1011;

        /**
         * <p>
         * The file path to the SSL certificate authority.
         * </p>
         * <p>
         * This exists as of PHP 5.3.7.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_SSL_CA = 1012;

        /**
         * <p>
         * The file path to the directory that contains the trusted SSL
         * CA certificates, which are stored in PEM format.
         * </p>
         * <p>
         * This exists as of PHP 5.3.7.
         * </p>
         * @link http://php.net/manual/en/ref.pdo-mysql.php
         */
        const MYSQL_ATTR_SSL_CAPATH = 1013;
        const MYSQL_ATTR_SSL_CIPHER = 1014;
        const PGSQL_ATTR_DISABLE_NATIVE_PREPARED_STATEMENT = 1000;
        const PGSQL_TRANSACTION_IDLE = 0;
        const PGSQL_TRANSACTION_ACTIVE = 1;
        const PGSQL_TRANSACTION_INTRANS = 2;
        const PGSQL_TRANSACTION_INERROR = 3;
        const PGSQL_TRANSACTION_UNKNOWN = 4;

        const PGSQL_CONNECT_ASYNC = 4;
        const PGSQL_CONNECTION_AUTH_OK = 5;
        const PGSQL_CONNECTION_AWAITING_RESPONSE = 4;
        const PGSQL_CONNECTION_MADE = 3;
        const PGSQL_CONNECTION_SETENV = 6;
        const PGSQL_CONNECTION_SSL_STARTUP = 7;
        const PGSQL_CONNECTION_STARTED = 2;
        const PGSQL_DML_ESCAPE = 4096;
        const PGSQL_POLLING_ACTIVE = 4;
        const PGSQL_POLLING_FAILED = 0;
        const PGSQL_POLLING_OK = 3;
        const PGSQL_POLLING_READING = 1;
        const PGSQL_POLLING_WRITING = 2;

        public function __construct()
        {
            throw new Exception('You can not create this class');
        }
    }
}