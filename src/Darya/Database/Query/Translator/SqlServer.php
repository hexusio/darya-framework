<?php
namespace Darya\Database\Query\Translator;

use Darya\Database;
use Darya\Database\Query\AbstractSqlTranslator;
use Darya\Storage;

/**
 * Darya's SQL Server query translator.
 * 
 * TODO: Offset!
 * 
 * @author Chris Andrew <chris@hexus.io>
 */
class SqlServer extends AbstractSqlTranslator {
	
	/**
	 * Escape the given identifier.
	 * 
	 * @param mixed $identifier
	 * @return mixed
	 */
	public function identifier($identifier) {
		return $identifier;
	}
	
	/**
	 * Prepare a LIMIT clause using the given limit and offset.
	 * 
	 * @param int $limit  [optional]
	 * @param int $offset [optional]
	 * @return string
	 */
	protected function prepareLimit($limit = null, $offset = 0) {
		if (!is_numeric($limit) || !is_numeric($offset) || empty($limit)) {
			return null;
		}
		
		$limit = (int) $limit;
		$offset = (int) $offset;
		
		return "TOP $limit";
	}
	
	/**
	 * Prepare a SELECT statement using the given columns, table, clauses and
	 * options.
	 * 
	 * @param string       $table
	 * @param array|string $columns
	 * @param string       $where    [optional]
	 * @param string       $order    [optional]
	 * @param string       $limit    [optional]
	 * @param bool         $distinct [optional]
	 * @return string
	 */
	protected function prepareSelect($table, $columns, $where = null, $order = null, $limit = null, $distinct = false) {
		$table = $this->identifier($table);
		
		$distinct = $distinct ? 'DISTINCT' : '';
		
		return static::concatenate(array('SELECT', $distinct, $limit, $columns, 'FROM', $table, $where, $order));
	}
	
	/**
	 * Prepare an UPDATE statement with the given table, data and clauses.
	 * 
	 * @param string $table
	 * @param array  $data
	 * @param string $where [optional]
	 * @param string $limit [optional]
	 * @return string
	 */
	protected function prepareUpdate($table, $data, $where = null, $limit = null) {
		$table = $this->identifier($table);
		
		foreach ($data as $key => $value) {
			$column = $this->identifier($key);
			$value = $this->escape($value);
			$data[$key] = "$column = $value";
		}
		
		$values = implode(', ', $data);
		
		return static::concatenate(array('UPDATE', $limit, $table, 'SET', $values, $where));
	}
	
	/**
	 * Prepare a DELETE statement with the given table and clauses.
	 * 
	 * @param string $table
	 * @param string $where [optional]
	 * @param string $limit [optional]
	 * @return string
	 */
	protected function prepareDelete($table, $where = null, $limit = null) {
		$table = $this->identifier($table);
		
		if ($table == '*' || !$table || !$where) {
			return null;
		}
		
		return static::concatenate(array('DELETE', $limit, 'FROM', $table, $where));
	}
	
}
