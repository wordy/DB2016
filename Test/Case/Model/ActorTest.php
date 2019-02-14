<?php
App::uses('Actor', 'Model');

/**
 * Actor Test Case
 */
class ActorTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.actor',
		'app.team',
		'app.task_color',
		'app.task',
		'app.task_type',
		'app.actionable_type',
		'app.change',
		'app.change_type',
		'app.user',
		'app.user_role',
		'app.comment',
		'app.print_pref',
		'app.teams_user',
		'app.tasks_team',
		'app.task_role',
		'app.zone',
		'app.assignment'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Actor = ClassRegistry::init('Actor');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Actor);

		parent::tearDown();
	}

}
