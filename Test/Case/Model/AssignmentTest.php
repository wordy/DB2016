<?php
App::uses('Assignment', 'Model');

/**
 * Assignment Test Case
 */
class AssignmentTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.assignment',
		'app.role',
		'app.task',
		'app.task_type',
		'app.actionable_type',
		'app.task_color',
		'app.team',
		'app.zone',
		'app.user',
		'app.user_role',
		'app.change',
		'app.change_type',
		'app.comment',
		'app.print_pref',
		'app.teams_user',
		'app.tasks_team',
		'app.task_role'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Assignment = ClassRegistry::init('Assignment');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Assignment);

		parent::tearDown();
	}

}
