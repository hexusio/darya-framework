<?php
namespace Darya\Database;

use Darya\Storage;

/**
 * Darya's database connection interface.
 * 
 * @author Chris Andrew <chris@hexus.io>
 */
interface Connection {
	
	/**
	 * Initiate a connection to the database.
	 */
	public function connect();
	
	/**
	 * Determine whether there is an active connection to the database.
	 * 
	 * @return bool
	 */
	public function connected();
	
	/**
	 * Close the connection to the database.
	 */
	public function disconnect();
	
	/**
	 * Translate a storage query to a query for this connection.
	 * 
	 * @param Storage\Query $storageQuery
	 * @return Database\Query
	 */
	public function translate(Storage\Query $storageQuery);
	
	/**
	 * Query the database.
	 * 
	 * @param string $query
	 * @param array  $parameters [optional]
	 * @return \Darya\Database\Result
	 */
	public function query($query, array $parameters = array());
	
	/**
	 * Escape the given string for use in a query.
	 * 
	 * @param string $string
	 * @return string
	 */
	public function escape($string);
	
	/**
	 * Retrieve any error that occurred with the last operation.
	 * 
	 * @return \Darya\Database\Error
	 */
	public function error();
	
}
