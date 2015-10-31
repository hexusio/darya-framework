<?php
use Darya\ORM\Record;
use Darya\Storage\InMemory;

/**
 * Tests Darya's active record ORM using in-memory storage.
 * 
 * TODO: Test updating and deleting relations.
 */
class RecordTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Data to use for the in-memory storage.
	 * 
	 * @var array
	 */
	protected static $data;
	
	/**
	 * The in-memory storage used for testing Darya's active record ORM.
	 * 
	 * @var InMemory
	 */
	protected $storage;
	
	public static function setUpBeforeClass() {
		$file = __DIR__  . '/data/cms.json';
		$data = json_decode(file_get_contents($file), true);
		
		static::$data = $data;
	}
	
	protected function setUp() {
		$this->storage = new InMemory(static::$data);
		
		Record::setSharedStorage($this->storage);
	}
	
	public function testFind() {
		$user = User::find(2);
		
		$this->assertEquals('Bethany', $user->firstname);
	}
	
	public function testAll() {
		$users = User::all();
		
		$this->assertEquals(3, count($users));
		
		$this->assertEquals('Chris', $users[0]->firstname);
		$this->assertEquals('Bethany', $users[1]->firstname);
		
		// Test filter
		$users = User::all(['firstname !=' => 'john']);
		
		$this->assertEquals(2, count($users));
		
		// Test sorting
		$users = User::all(null, 'firstname');
		
		$this->assertEquals('Bethany', $users[0]->firstname);
		$this->assertEquals('Chris', $users[1]->firstname);
		
		// Test limiting
		$users = User::all(null, null, 1);
		
		$this->assertEquals(1, count($users));
		$this->assertEquals('Chris', $users[0]->firstname);
		
		// Test offset
		$users = User::all(null, null, 1, 1);
		
		$this->assertEquals(1, count($users));
		$this->assertEquals('Bethany', $users[0]->firstname);
	}
	
	public function testHas() {
		$user = User::find(1);
		
		$this->assertTrue($user->has('roles'));
		
		$user = User::find(2);
		
		$this->assertFalse($user->has('manager'));
		
		$user = User::find(3);
		
		$this->assertTrue($user->has('manager'));
	}
	
	public function testBelongsTo() {
		$post = Post::find(3);
		
		$author = $post->author;
		
		$this->assertEquals('Bethany', $author->firstname);
		
		$user = User::find(3);
		
		$manager = $user->manager;
		
		$this->assertEquals('Bethany', $manager->firstname);
	}
	
	public function testHasMany() {
		$user = User::find(1);
		
		$posts = $user->posts;
		
		$this->assertEquals(2, count($posts));
		
		$this->assertEquals("First post", $posts[0]->title);
	}
	
	public function testBelongsToMany() {
		$user = User::find(1);
		
		$roles = $user->roles;
		
		$this->assertEquals(2, count($roles));
		
		$role = Role::find(2);
		
		$users = $role->users;
		
		$this->assertEquals(1, count($users));
		$this->assertEquals('Bethany', $users[0]->firstname);
	}
	
	public function testBelongsToManyEager() {
		$users = User::eager('roles');
		
		$this->assertEquals('Administrator', $users[0]->roles[0]->name);
		$this->assertEquals('b0ss', $users[0]->roles[1]->name);
		$this->assertEquals(2, count($users[0]->roles));
		
		$this->assertEquals('Moderator', $users[1]->roles[0]->name);
		$this->assertEquals(1, count($users[1]->roles));
		
		$this->assertEquals('User', $users[2]->roles[0]->name);
		$this->assertEquals(1, count($users[2]->roles));
	}
	
	public function testDefaultSearchAttributes() {
		$users = User::search('chris');
		
		$this->assertEquals(1, count($users));
		$this->assertEquals('Andrew', $users[0]->surname);
		
		$users = User::search('KING');
		
		$this->assertEquals(1, count($users));
		$this->assertEquals('Bethany', $users[0]->firstname);
	}
	
	public function testHasManyAssociation() {
		$user = User::find(1);
		
		$post = new Post(array(
			'id'      => 4,
			'title'   => 'Swagger',
			'content' => 'Dis one got swagger'
		));
		
		$user->posts()->associate($post);
		
		$this->assertEquals(3, $user->posts()->count());
	}
	
	public function testHasManyDissociation() {
		$user = User::find(1);
		
		$this->assertEquals(2, $user->posts()->count());
		
		$post = $user->posts[0];
		
		$this->assertEquals(1, $post->id());
		
		$dissociated = $user->posts()->dissociate($post);
		
		$this->assertEquals(1, $dissociated);
		
		$this->assertEquals(1, $user->posts()->count());
		
		$this->assertEquals(2, $user->posts[0]->id());
	}
	
	public function testHasManyFullDissociation() {
		$user = User::find(1);
		
		$this->assertEquals(2, $user->posts()->count());
		
		$dissociated = $user->posts()->dissociate();
		
		$this->assertEquals(2, $dissociated);
		
		$this->assertEquals(0, $user->posts()->count());
	}
	
}

class Post extends Record
{
	protected $relations = array(
		'author' => ['belongs_to', 'User', 'author_id']
	);
}

class Role extends Record
{
	protected $relations = array(
		'users' => ['belongs_to_many', 'User', null, null, 'user_roles']
	);
}

class User extends Record
{
	protected $relations = array(
		'manager' => ['belongs_to', 'User', 'manager_id'],
		'posts'   => ['has_many', 'Post', 'author_id'],
		'roles'   => ['belongs_to_many', 'Role', null, null, 'user_roles']
	);
	
	protected $search = array(
		'firstname', 'surname'
	);
}
