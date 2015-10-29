<?php
App::uses('TasksTeam', 'Model');

/**
 * TasksTeam Test Case
 *
 */
class TasksTeamTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.tasks_team',
		'app.task',
		'app.task_type',
		'app.actionable_type',
		'app.task_color',
		'app.team',
		'app.attachment',
		'app.user',
		'app.user_role',
		'app.change',
		'app.change_type',
		'app.message',
		'app.task_role'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->TasksTeam = ClassRegistry::init('TasksTeam');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TasksTeam);

		parent::tearDown();
	}

}
