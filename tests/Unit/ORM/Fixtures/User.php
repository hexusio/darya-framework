<?php
namespace Darya\Tests\Unit\ORM\Fixtures;

use Darya\ORM\Record;

use Darya\Tests\Unit\ORM\Fixtures\Post;
use Darya\Tests\Unit\ORM\Fixtures\Role;
use Darya\Tests\Unit\ORM\Fixtures\User;

class User extends Record
{
	protected $relations = array(
		'padawan' => ['has',             User::class, 'master_id'],
		'manager' => ['belongs_to',      User::class, 'manager_id'],
		'master'  => ['belongs_to',      User::class, 'master_id'],
		'posts'   => ['has_many',        Post::class, 'author_id'],
		'roles'   => ['belongs_to_many', Role::class, null, null, 'user_roles']
	);
	
	protected $search = array(
		'firstname', 'surname'
	);
}