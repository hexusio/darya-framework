<?php
use Darya\Storage\InMemory;

class InMemoryTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Provides test data for in-memory storage.
	 * 
	 * @return array
	 */
	protected function inMemoryData() {
		return array(
			'pages' => array(
				array(
					'id' => 1,
					'name' => 'My page',
					'text' => 'Page text'
				)
			),
			'roles' => array(
				array(
					'id'   => 1,
					'name' => 'User'
				),
				array(
					'id'   => 2,
					'name' => 'Moderator'
				),
				array(
					'id'   => 3,
					'name' => 'Administrator'
				)
			),
			'users' => array(
				array(
					'id'   => 1,
					'name' => 'Chris'
				),
				array(
					'id'   => 2,
					'name' => 'Bethany'
				)
			)
		);
	}
	
	/**
	 * Retrieve the in-memory storage to test with.
	 * 
	 * @return InMemory
	 */
	protected function storage() {
		return new InMemory($this->inMemoryData());
	}
	
	public function testSimpleRead() {
		$data = $this->inMemoryData();
		
		$storage = new InMemory($data);
		
		$users = $storage->read('users');
		
		$this->assertEquals($data['users'], $users);
		
		$pages = $storage->read('pages');
		
		$this->assertEquals($data['pages'], $pages);
		
		$this->assertEquals(array(), $storage->read('non_existent'));
	}
	
	public function testSimpleListing() {
		$storage = $this->storage();
		
		$listing = $storage->listing('users', 'id');
		
		$this->assertEquals(array(array('id' => 1), array('id' => 2)), $listing);
	}
	
	public function testCount() {
		$data = $this->inMemoryData();
		
		$storage = new InMemory($data);
		
		$this->assertEquals(3, $storage->count('roles'));
		$this->assertEquals(2, $storage->count('users'));
		$this->assertEquals(1, $storage->count('pages'));
		$this->assertEquals(0, $storage->count('non_existent'));
	}
	
	public function testEqualsFilter() {
		$storage = $this->storage();
		
		$users = $storage->read('users', array('name' => 'chris'));
		
		$this->assertEquals(array(array('id' => 1, 'name' => 'Chris')), $users);
		
		$users = $storage->read('users', array('id' => 2));
		
		$this->assertEquals(array(array('id' => 2, 'name' => 'Bethany')), $users);
		
		$users = $storage->read('users', array('id' => '2'));
		
		$this->assertEquals(array(array('id' => 2, 'name' => 'Bethany')), $users);
	}
	
	public function testLikeFilter() {
		$storage = $this->storage();
		
		$roles = $storage->read('roles', array('name like' => '%admin%'));
		
		$this->assertEquals(array(array('id' => 3, 'name' => 'Administrator')), $roles);
		
		$roles = $storage->read('users', array('name like' => '%beth%'));
		
		$this->assertEquals(array(array('id' => 2, 'name' => 'Bethany')), $roles);
	}
	
	public function testOrFilter() {
		$storage = $this->storage();
		
		$roles = $storage->read('roles', array(
			'or' => array(
				'id'        => 2,
				'name like' => '%admin%',
			)
		));
		
		$this->assertEquals(array(
			array('id' => 2, 'name' => 'Moderator'),
			array('id' => 3, 'name' => 'Administrator')
		), $roles);
	}
	
}
