<?php
App::uses('TeamsUser', 'Model');

/**
 * TeamsUser Test Case
 *
 */
class TeamsUserTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.teams_user',
		'app.team',
		'app.attachment',
		'app.task',
		'app.task_type',
		'app.actionable_type',
		'app.task_color',
		'app.change',
		'app.change_type',
		'app.user',
		'app.user_role',
		'app.comment',
		'app.tasks_team',
		'app.task_role',
		'app.message'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->TeamsUser = ClassRegistry::init('TeamsUser');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TeamsUser);

		parent::tearDown();
	}

}
