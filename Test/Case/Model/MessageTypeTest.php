<?php
App::uses('MessageType', 'Model');

/**
 * MessageType Test Case
 *
 */
class MessageTypeTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.message_type',
		'app.message',
		'app.send_team',
		'app.rec_team',
		'app.action',
		'app.action_type',
		'app.user',
		'app.team',
		'app.attachment',
		'app.notification',
		'app.actions_team',
		'app.action_role',
		'app.message_data'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MessageType = ClassRegistry::init('MessageType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MessageType);

		parent::tearDown();
	}

}
