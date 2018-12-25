<?php
App::uses('Role', 'Model');

/**
 * Role Test Case
 */
class RoleTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.role',
		'app.team',
		'app.task_color',
		'app.task',
		'app.task_type',
		'app.actionable_type',
		'app.assignment',
		'app.actor',
		'app.user',
		'app.user_role',
		'app.change',
		'app.change_type',
		'app.comment',
		'app.print_pref',
		'app.teams_user',
		'app.tasks_team',
		'app.task_role',
		'app.zone',
		'app.assignments'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Role = ClassRegistry::init('Role');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Role);

		parent::tearDown();
	}

}
