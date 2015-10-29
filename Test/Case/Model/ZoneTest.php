<?php
App::uses('Zone', 'Model');

/**
 * Zone Test Case
 *
 */
class ZoneTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.zone',
		'app.gm_user',
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
		'app.notification'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Zone = ClassRegistry::init('Zone');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Zone);

		parent::tearDown();
	}

}
