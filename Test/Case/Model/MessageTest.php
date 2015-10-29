<?php
App::uses('Message', 'Model');

/**
 * Message Test Case
 *
 */
class MessageTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.message',
		'app.send_team',
		'app.rec_team',
		'app.notification',
		'app.team',
		'app.assignment',
		'app.attachment',
		'app.task',
		'app.ttype',
		'app.tcolour',
		'app.parent_task',
		'app.revision',
		'app.user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Message = ClassRegistry::init('Message');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Message);

		parent::tearDown();
	}

}
