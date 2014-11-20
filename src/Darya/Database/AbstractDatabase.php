<?php
namespace Darya\Database;

use Darya\Database\DatabaseInterface;
use Darya\Common\Tools;

/**
 * Darya's abstract database connection.
 * 
 * @author Chris Andrew <chris@hexus.io>
 */
abstract class AbstractDatabase implements DatabaseInterface {
	
	/**
	 * @var mixed Connection object
	 */
	protected $connection;
	
	/**
	 * @var string The last query error
	 */
	protected $error; 
	
	/**
	 * @var string The last query executed
	 */
	protected $lastQuery;
	
	/**
	 * @var array Detailed result array corresponding to the last query
	 */
	protected $lastResult;
	
	/**
	 * @var array Set of operators used by the database query language
	 */
	protected $operators = array();
	
	/**
	 * Instantiate a new Database object and initialise its connection.
	 * 
	 * @param string $host Hostname to connect to
	 * @param string $user Username to authenticate with
	 * @param string $pass Password to authenticate with
	 * @param string $name Database to select
	 * @param int    $port [optional] Port to connect to
	 */
	abstract public function __construct($host, $user, $pass, $name, $port = null);
	
	/**
	 * Query the database and return any resulting data.
	 * 
	 * @param string $query
	 * @return mixed
	 */
	public function query($query) {
		$this->lastQuery = $query;
	}
	
	/**
	 * Determine whether a given string ends with a query comparison operator.
	 * 
	 * @param string $haystack
	 * @return bool
	 */
	public function endsWithOperator($haystack){
		return Tools::endsWith($haystack, $this->operators);
	}
	
	public function error() {
		return $this->error;
	}
	
	/**
	 * Get the last query made by this connection
	 * 
	 * @return string Database query
	 */
	public function lastQuery(){
		return $this->lastQuery;
	}
	
	/**
	 * Get the detailed result array corresponding to the last query made by this connection
	 * 
	 * @return array Result array
	 */
	public function lastResult(){
		return $this->lastResult;
	}
	
}
?>