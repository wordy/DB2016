<?php
App::uses('ActionsTeam', 'Model');

/**
 * ActionsTeam Test Case
 *
 */
class ActionsTeamTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.actions_team',
		'app.action',
		'app.action_type',
		'app.user',
		'app.attachment',
		'app.message',
		'app.notification',
		'app.team',
		'app.action_role'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ActionsTeam = ClassRegistry::init('ActionsTeam');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ActionsTeam);

		parent::tearDown();
	}

}
