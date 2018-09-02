<?php
namespace Darya\ORM;

use InvalidArgumentException;

/**
 * Darya's abstract entity map.
 *
 * Describes an entity's mapping to a storage interface.
 *
 * Used by a mapper to
 *
 * @author Chris Andrew <chris@hexus.io>
 */
class EntityMap
{
	/**
	 * The entity class to map to.
	 *
	 * Must implement the Darya\ORM\Mappable interface.
	 *
	 * @var string
	 */
	protected $class;

	/**
	 * The name of the resource the entity maps to in storage.
	 *
	 * @var string
	 */
	protected $resource;

	/**
	 * The entity's primary key attribute.
	 *
	 * @var string
	 */
	protected $key = 'id';

	/**
	 * The mapping of entity attributes to storage fields.
	 *
	 * @var array
	 */
	protected $mapping;

	/**
	 * Create a new entity map.
	 *
	 * @param string $class    The entity class to map to, implementing the Darya\ORM\Mappable interface.
	 * @param string $resource The name of the resource the entity maps to in storage.
	 * @param array  $mapping  [optional] The mapping of entity attributes to storage fields.
	 */
	public function __construct(string $class, string $resource, array $mapping = [])
	{
		if (!is_subclass_of($class, Mappable::class)) {
			throw new InvalidArgumentException("EntityMap class '$class' must implement " . Mappable::class);
		}

		$this->class = $class;
		$this->resource = $resource;
		$this->mapping = $mapping;
	}

	/**
	 * Get the mapped entity class.
	 */
	public function getClass(): string
	{
		return $this->class;
	}

	/**
	 * Get the resource the entity is mapped to.
	 *
	 * @return string
	 */
	public function getResource(): string
	{
		return $this->resource;
	}

	/**
	 * Get the primary key attribute name of the entity.
	 *
	 * @return string
	 */
	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * Get the storage field name of the entity's primary key.
	 *
	 * @return string
	 */
	public function getStorageKey(): string
	{
		// Check to see if the key attribute is mapped to a storage field
		$mapping = $this->getMapping();

		if (isset($mapping[$this->getKey()])) {
			return $this->mapping[$this->getKey()];
		}

		return $this->getKey();
	}

	/**
	 * Get the mapping of entity attributes to storage fields.
	 *
	 * Returns an array with entity attributes as keys and corresponding
	 * storage fields as values.
	 */
	public function getMapping()
	{
		return $this->mapping;
	}
}