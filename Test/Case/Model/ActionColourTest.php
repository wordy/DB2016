<?php
App::uses('ActionColour', 'Model');

/**
 * ActionColour Test Case
 *
 */
class ActionColourTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.action_colour',
		'app.action',
		'app.action_type',
		'app.user',
		'app.team',
		'app.attachment',
		'app.notification',
		'app.message',
		'app.send_team',
		'app.rec_team',
		'app.message_data',
		'app.message_type',
		'app.actions_team',
		'app.action_role'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ActionColour = ClassRegistry::init('ActionColour');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ActionColour);

		parent::tearDown();
	}

}
