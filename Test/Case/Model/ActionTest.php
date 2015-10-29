<?php
App::uses('Action', 'Model');

/**
 * Action Test Case
 *
 */
class ActionTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.action',
		'app.action_type',
		'app.user',
		'app.attachment',
		'app.message',
		'app.notification',
		'app.actions_team'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Action = ClassRegistry::init('Action');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Action);

		parent::tearDown();
	}

}
