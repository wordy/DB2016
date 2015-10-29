<?php
App::uses('Status', 'Model');

/**
 * Status Test Case
 *
 */
class StatusTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.status',
		'app.action',
		'app.action_type',
		'app.user',
		'app.team',
		'app.attachment',
		'app.notification',
		'app.message',
		'app.message_datum',
		'app.message_type',
		'app.actions_team',
		'app.action_role',
		'app.action_colour',
		'app.linked_action',
		'app.status_code'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Status = ClassRegistry::init('Status');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Status);

		parent::tearDown();
	}

}
