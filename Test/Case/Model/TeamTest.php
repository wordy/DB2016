<?php
App::uses('Team', 'Model');

/**
 * Team Test Case
 *
 */
class TeamTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
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
		'app.message',
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
		$this->Team = ClassRegistry::init('Team');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Team);

		parent::tearDown();
	}

/**
 * testMakeListNoAll method
 *
 * @return void
 */
	public function testMakeListNoAll() {
	}

/**
 * testMakeAllTeamIdList method
 *
 * @return void
 */
	public function testMakeAllTeamIdList() {
	}

/**
 * testMakeAllTeamList method
 *
 * @return void
 */
	public function testMakeAllTeamList() {
	}

}
