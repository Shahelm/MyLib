<?php
namespace MyLib\DataBase;

/**
 * @author col.shrapnel@gmail.com
 * @link http://phpfaq.ru/safemysql
 *
 * Safe and convenient vay to handle SQL queries utilizing type-hinted placeholders.
 *
 * Key features
 * - set of helper functions to get the desired result right out of query, like in PEAR::DB
 * - conditional query building using parse() method to build queries of whatever comlexity,
 *   while keeping extra safety of placeholders
 * - type-hinted placeholders
 *
 *  Type-hinted placeholders are great because
 * - safe, as any other [properly implemented] placeholders
 * - no need for manual escaping or binding, makes the code extra DRY
 * - allows support for non-standard types such as identifier or array, which saves A LOT of pain in the back.
 *
 * Supported placeholders at the moment are:
 *
 * ?s ("string")  - strings (also DATE, FLOAT and DECIMAL)
 * ?i ("integer") - the name says it all
 * ?n ("name")    - identifiers (table and field names)
 * ?a ("array")   - complex placeholder for IN() operator  (substituted with string of 'a','b','c' format, without parentesis)
 * ?u ("update")  - complex placeholder for SET operator (substituted with string of `field`='value',`field`='value' format)
 * and
 * ?p ("parsed") - special type placeholder, for inserting already parsed statements without any processing, to avoid double parsing.
 *
 * Some examples:
 *
 * $db = new SafeMySQL(); // with default settings
 *
 * $opts = array(
 *		'user'    => 'user',
 *		'pass'    => 'pass',
 *		'db'      => 'db',
 *		'charset' => 'latin1'
 * );
 * $db = new SafeMySQL($opts); // with some of the default settings overwritten
 *
 *
 * $name = $db->getOne('SELECT name FROM table WHERE id = ?i',$_GET['id']);
 * $data = $db->getInd('id','SELECT * FROM ?n WHERE id IN ?a','table', array(1,2));
 * $data = $db->getAll("SELECT * FROM ?n WHERE mod=?s LIMIT ?i",$table,$mod,$limit);

 * $ids  = $db->getCol("SELECT id FROM tags WHERE tagname = ?s",$tag);
 * $data = $db->getAll("SELECT * FROM table WHERE category IN (?a)",$ids);
 *
 * $data = array('offers_in' => $in, 'offers_out' => $out);
 * $sql  = "INSERT INTO stats SET pid=?i,dt=CURDATE(),?u ON DUPLICATE KEY UPDATE ?u";
 * $db->query($sql,$pid,$data,$data);
 *
 * if ($var === NULL) {
 *     $sqlpart = "field is NULL";
 * } else {
 *     $sqlpart = $db->parse("field = ?s", $var);
 * }
 * $data = $db->getAll("SELECT * FROM table WHERE ?p", $bar, $sqlpart);
 *
 */

class SafeMySQL
{
    private $conn;
    private $stats;
    private $errorMode;
    private $exceptionClassName;

    private $defaults = array(
        'host'      => 'localhost',
        'user'      => 'root',
        'pass'      => '',
        'db'        => 'test',
        'port'      => NULL,
        'socket'    => NULL,
        'pconnect'  => FALSE,
        'charset'   => 'utf8',
        'errorMode'   => 'error',
        'exception' => 'Exception',
    );

    const RESULT_ASSOC = MYSQLI_ASSOC;
    const RESULT_NUM   = MYSQLI_NUM;

    public function __construct($opt = array())
    {
        $opt = array_merge($this->defaults, $opt);

        $this->errorMode  = $opt['errorMode'];
        $this->exceptionClassName = $opt['exception'];

        if ($opt['pconnect']) {
            $opt['host'] = "p:" . $opt['host'];
        }

        @$this->conn = mysqli_connect($opt['host'], $opt['user'], $opt['pass'], $opt['db'], $opt['port'], $opt['socket']);

        if (!$this->conn) {
            $this->error(mysqli_connect_errno()." ".mysqli_connect_error());
        }

        mysqli_set_charset($this->conn, $opt['charset']) or $this->error(mysqli_error($this->conn));
        unset($opt);
    }

    /**
     * Conventional function to run a query with placeholders. A mysqli_query wrapper with placeholders support
     *
     * Examples:
     * $db->query("DELETE FROM table WHERE id=?i", $id);
     *
     * @internal param string $query - an SQL query with placeholders
     * @internal param mixed $arg unlimited number of arguments to match placeholders in the query
     * @return resource|bool(false) whatever mysqli_query returns
     */
    public function query()
    {
        return $this->rawQuery($this->prepareQuery(func_get_args()));
    }

    /**
     * Conventional function to fetch single row.
     *
     * @param $result - myqli_result
     * @param int $mode - optional fetch mode, RESULT_ASSOC|RESULT_NUM, default RESULT_ASSOC
     * @return array|bool(false) whatever mysqli_fetch_array returns
     */
    public function fetch($result, $mode = self::RESULT_ASSOC)
    {
        return mysqli_fetch_array($result, $mode);
    }

    /**
     * Conventional function to get number of affected rows.
     *
     * @return int whatever mysqli_affected_rows returns
     */
    public function affectedRows()
    {
        return mysqli_affected_rows ($this->conn);
    }

    /**
     * Conventional function to get last insert id.
     *
     * @return int whatever mysqli_insert_id returns
     */
    public function insertId()
    {
        return mysqli_insert_id($this->conn);
    }

    /**
     * Conventional function to get number of rows in the resultant.
     *
     * @param $result - myqli result
     * @return int whatever mysqli_num_rows returns
     */
    public function numRows($result)
    {
        return mysqli_num_rows($result);
    }

    /**
     * Conventional function to free the resultant.
     */
    public function free($result)
    {
        mysqli_free_result($result);
    }

    /**
     * Helper function to get scalar value right out of query and optional arguments
     *
     * Examples:
     * $name = $db->getOne("SELECT name FROM table WHERE id=1");
     * $name = $db->getOne("SELECT name FROM table WHERE id=?i", $id);
     *
     * @internal param string $query - an SQL query with placeholders
     * @internal param mixed $arg unlimited number of arguments to match placeholders in the query
     * @return string|bool(false) either first column of the first row of resultant or FALSE if none found
     */
    public function getOne()
    {
        $query = $this->prepareQuery(func_get_args());

        if ($res = $this->rawQuery($query)) {
            $row = $this->fetch($res);

            if (is_array($row)) {
                return reset($row);
            }

            $this->free($res);
        }

        return false;
    }

    /**
     * Helper function to get single row right out of query and optional arguments
     *
     * Examples:
     * $data = $db->getRow("SELECT * FROM table WHERE id=1");
     * $data = $db->getOne("SELECT * FROM table WHERE id=?i", $id);
     *
     * @internal param string $query - an SQL query with placeholders
     * @internal param mixed $arg unlimited number of arguments to match placeholders in the query
     * @return array|bool(false) either associative array contains first row of resultset or FALSE if none found
     */
    public function getRow()
    {
        $query = $this->prepareQuery(func_get_args());

        if ($res = $this->rawQuery($query)) {
            $ret = $this->fetch($res);
            $this->free($res);
            return $ret;
        }

        return false;
    }

    /**
     * Helper function to get single column right out of query and optional arguments
     *
     * Examples:
     * $ids = $db->getCol("SELECT id FROM table WHERE cat=1");
     * $ids = $db->getCol("SELECT id FROM tags WHERE tagname = ?s", $tag);
     *
     * @internal param string $query - an SQL query with placeholders
     * @internal param mixed $arg unlimited number of arguments to match placeholders in the query
     * @return array|bool(false) either enumerated array of first fields of all rows of resultset or FALSE if none found
     */
    public function getCol()
    {
        $ret   = array();

        $query = $this->prepareQuery(func_get_args());

        if ($res = $this->rawQuery($query)) {
            while ($row = $this->fetch($res)) {
                $ret[] = reset($row);
            }

            $this->free($res);
        }

        return $ret;
    }

    /**
     * Helper function to get all the rows of resultant right out of query and optional arguments
     *
     * Examples:
     * $data = $db->getAll("SELECT * FROM table");
     * $data = $db->getAll("SELECT * FROM table LIMIT ?i,?i", $start, $rows);
     *
     * @internal param string $query - an SQL query with placeholders
     * @internal param mixed $arg unlimited number of arguments to match placeholders in the query
     * @return array enumerated 2d array contains the resultant. Empty if no rows found.
     */
    public function getAll()
    {
        $ret   = array();

        $query = $this->prepareQuery(func_get_args());

        if ($res = $this->rawQuery($query)) {
            while($row = $this->fetch($res)) {
                $ret[] = $row;
            }

            $this->free($res);
        }

        return $ret;
    }

    /**
     * Helper function to get all the rows of resultset into indexed array right out of query and optional arguments
     *
     * Examples:
     * $data = $db->getInd("id", "SELECT * FROM table");
     * $data = $db->getInd("id", "SELECT * FROM table LIMIT ?i,?i", $start, $rows);
     *
     * @internal param string $index - name of the field which value is used to index resulting array
     * @internal param string $query - an SQL query with placeholders
     * @internal param mixed $arg unlimited number of arguments to match placeholders in the query
     * @return array - associative 2d array contains the resultset. Empty if no rows found.
     */
    public function getInd()
    {
        $args  = func_get_args();
        $index = array_shift($args);
        $query = $this->prepareQuery($args);

        $ret = array();

        if ($res = $this->rawQuery($query)) {

            while ($row = $this->fetch($res)) {
                $ret[$row[$index]] = $row;
            }

            $this->free($res);
        }

        return $ret;
    }

    /**
     * Helper function to get a dictionary-style array right out of query and optional arguments
     *
     * Examples:
     * $data = $db->getIndCol("name", "SELECT name, id FROM cities");
     *
     * @internal param string $index - name of the field which value is used to index resulting array
     * @internal param string $query - an SQL query with placeholders
     * @internal param mixed $arg unlimited number of arguments to match placeholders in the query
     * @return array - associative array contains key=value pairs out of resultset. Empty if no rows found.
     */
    public function getIndCol()
    {
        $args  = func_get_args();
        $index = array_shift($args);
        $query = $this->prepareQuery($args);

        $ret = array();

        if ($res = $this->rawQuery($query)) {

            while ($row = $this->fetch($res)) {
                $key = $row[$index];
                unset($row[$index]);
                $ret[$key] = reset($row);
            }

            $this->free($res);
        }

        return $ret;
    }

    /**
     * Function to parse placeholders either in the full query or in query part
     * useful for debug
     * and conditional query building
     *
     * Examples:
     * $query = $db->parse("SELECT * FROM table WHERE foo=?s AND bar=?s", $foo, $bar);
     * echo $query;
     *
     * if ($foo) {
     *     $qpart = $db->parse(" AND foo=?s", $foo);
     * }
     * $data = $db->getAll("SELECT * FROM table WHERE bar=?s ?p", $bar, $qpart);
     *
     * @internal param string $query - whatever expression contains placeholders
     * @internal param mixed $arg unlimited number of arguments to match placeholders in the expression
     * @return string - initial expression with placeholders substituted with data.
     */
    public function parse()
    {
        return $this->prepareQuery(func_get_args());
    }

    /**
     * function to implement whitelisting feature
     * sometimes we can't allow a non-validated user-supplied data to the query even through placeholder
     * especially if it comes down to SQL OPERATORS
     *
     * Example:
     *
     * $order = $db->whiteList($_GET['order'], array('name','price'));
     * $dir   = $db->whiteList($_GET['dir'],   array('ASC','DESC'));
     * if (!$order || !dir) {
     *     throw new http404(); //non-expected values should cause 404 or similar response
     * }
     * $sql  = "SELECT * FROM table ORDER BY ?p ?p LIMIT ?i,?i"
     * $data = $db->getArr($sql, $order, $dir, $start, $per_page);
     *
     * @param $input
     * @param  array $allowed - an array with allowed variants
     * @param bool|string $default - optional variable to set if no match found. Default to bool(false).
     * @internal param string $input - field name to test
     * @return string|bool(false) - either sanitized value or FALSE
     */
    public function whiteList($input, $allowed, $default = false)
    {
        $found = array_search($input,$allowed);
        
        return ($found === false) ? $default : $allowed[$found];
    }

    /**
     * function to filter out arrays, for the whitelisting purposes
     * useful to pass entire superglobal to the INSERT or UPDATE query
     * OUGHT to be used for this purpose, as there could be fuelds whic user inallowed to alter.
     *
     * Example:
     * $allowed = array('title','url','body','rating','term','type');
     * $data    = $db->filterArray($_POST,$allowed);
     * $sql     = "INSERT INTO ?n SET ?u";
     * $db->query($sql,$table,$data);
     *
     * @param  array $input   - source array
     * @param  array $allowed - an array with allowed field names
     * @return array filtered out source array
     */
    public function filterArray($input,$allowed)
    {
        foreach (array_keys($input) as $key) {
            if (!in_array($key,$allowed)) {
                unset($input[$key]);
            }
        }
        
        return $input;
    }

    private function rawQuery($query)
    {
        $start = microtime(true);
        $res   = mysqli_query($this->conn, $query) or $this->error(mysqli_error($this->conn).". Full query: [$query]");
        $timer = microtime(true) - $start;

        $this->stats[] = array(
            'query' => $query,
            'start' => $start,
            'timer' => $timer,
        );
        
        return $res;
    }

    /**
     * The function prepares sql expression.
     *
     * @param $args
     * @return string
     */
    private function prepareQuery($args)
    {
        $query = '';
        $raw   = array_shift($args);
        $array = preg_split('~(\?[nsiuap])~u', $raw, null, PREG_SPLIT_DELIM_CAPTURE);
        $argumentCount  = count($args);
        $placeholderCount  = floor(count($array) / 2);
        
        if ($placeholderCount != $argumentCount) {
            $this->error("Number of args ($argumentCount) doesn't match number of placeholders ($placeholderCount) in [$raw]");
        }

        foreach ($array as $i => $part) {
            if (($i % 2) == 0) {
                $query .= $part;
                continue;
            }

            $value = array_shift($args);
            
            switch ($part)
            {
                case '?n':
                    $part = $this->escapeIdentifiers($value);
                    break;
                case '?s':
                    $part = $this->escapeString($value);
                    break;
                case '?i':
                    $part = $this->escapeInt($value);
                    break;
                case '?a':
                    $part = $this->createIN($value);
                    break;
                case '?u':
                    $part = $this->createSET($value);
                    break;
                case '?p':
                    $part = $value;
                    break;
            }
            
            $query .= $part;
        }
        
        return $query;
    }

    /**
     * may lose precision on big numbers
     * to avoid double munus collision (one from query + one from value = comment --)
     *
     * @param $value
     * @return string
     */
    private function escapeInt($value)
    {
        $return = '';

        if (is_float($value)) {
            $return = number_format($value, 0, '.', '');
        } elseif(!is_float($value) && !is_numeric($value)) {
            $this->error("Integer (?i) placeholder expects numeric value, ".gettype($value)." given");
        } elseif(is_numeric($value)) {
            $return = $value;
        }

        return " ".$return;
    }

    /**
     * Screening function.
     *
     * @param mixed $value
     * @return string
     */
    private function escapeString($value)
    {
        return "'".mysqli_real_escape_string($this->conn, $value)."'";
    }

    /**
     * Escaping field names
     *
     * @param $value
     * @return string
     */
    private function escapeIdentifiers($value)
    {
        if (!$value) {
            $this->error("Empty value for identifier (?n) placeholder");
        }

        return "`".str_replace("`", "``", $value)."`";
    }

    /**
     * The function creates an expression IN ()
     *
     * @param $data
     * @return string|void
     */
    private function createIN($data)
    {
        if (!is_array($data)) {
            $this->error("Value for IN (?a) placeholder should be array");
            return null;
        }

        if (!$data) {
            return null;
        }

        $query = $comma = '';

        foreach ($data as $value) {
            $query .= $comma . $this->escapeString($value);
            $comma  = ",";
        }

        return $query;
    }

    private function createSET($data)
    {
        if (!is_array($data)) {
            $this->error("SET (?u) placeholder expects array, " . gettype($data) . " given");
            return null;
        }

        if (!$data) {
            $this->error("Empty array for SET (?u) placeholder");
            return null;
        }

        $query = $comma = '';

        foreach ($data as $key => $value) {
            $query .= $comma . $this->escapeIdentifiers($key) . '=' . $this->escapeString($value);
            $comma  = ",";
        }

        return $query;
    }

    /**
     * The error handler.
     *
     * @param $err
     * @throws
     */
    private function error($err)
    {
        $err  = __CLASS__.": ".$err;

        if ($this->errorMode == 'error') {
            $err .= ". Error initiated in ".$this->caller().", thrown";
            trigger_error($err, E_USER_ERROR);
        } else {
            throw new $this->exceptionClassName($err);
        }
    }

    /**
     * Trace to display the error report.
     *
     * @return string
     */
    private function caller()
    {
        $trace  = debug_backtrace();
        $caller = '';

        foreach ($trace as $t) {
            if ( isset($t['class']) && $t['class'] == __CLASS__ ) {
                $caller = $t['file']." on line ".$t['line'];
            } else {
                break;
            }
        }

        return $caller;
    }

    /**
     * Function to get last executed query.
     *
     * @return string|NULL either last executed query or NULL if were none
     */
    public function lastQuery()
    {
        $last = end($this->stats);
        return $last['query'];
    }

    /**
     * Function to get all query statistics.
     *
     * @return array contains all executed queries with timings
     */
    public function getStats()
    {
        return $this->stats;
    }
}