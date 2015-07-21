<?php
namespace Darya\Storage;

/**
 * Darya's readable data store interface.
 * 
 * @author Chris Andrew <chris@hexus.io>
 */
interface Readable {
	
	/**
	 * Retrieve resource data using the given criteria.
	 * 
	 * @param string       $resource
	 * @param array        $filter   [optional]
	 * @param array|string $order    [optional]
	 * @param int          $limit    [optional]
	 * @param int          $offset   [optional]
	 * @return array
	 */
	public function read($resource, array $filter = array(), $order = null, $limit = null, $offset = 0);
	
	/**
	 * Retrieve all values of the given resource field.
	 * 
	 * @param string $resource
	 * @param string $field
	 * @param array  $filter   [optional]
	 * @param array  $order    [optional]
	 * @param int    $limit    [optional]
	 * @param int    $offset   [optional]
	 * @return array
	 */
	public function listing($resource, $field, array $filter = array(), $order = array(), $limit = null, $offset = 0);
	
	/**
	 * Count the given resource using the given filter.
	 * 
	 * @param string $resource
	 * @param array  $filter   [optional]
	 * @return int
	 */
	public function count($resource, array $filter = array());
	
}