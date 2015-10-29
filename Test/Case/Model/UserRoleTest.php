<?php
App::uses('UserRole', 'Model');

/**
 * UserRole Test Case
 *
 */
class UserRoleTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.user_role',
		'app.user',
		'app.team',
		'app.attachment',
		'app.task',
		'app.task_type',
		'app.actionable_type',
		'app.task_color',
		'app.tasks_team',
		'app.task_role',
		'app.change',
		'app.change_type',
		'app.message'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UserRole = ClassRegistry::init('UserRole');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserRole);

		parent::tearDown();
	}

}
