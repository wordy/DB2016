<?php
App::uses('Assignment', 'Model');

/**
 * Assignment Test Case
 *
 */
class AssignmentTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.assignment',
		'app.task',
		'app.ttype',
		'app.tcolour',
		'app.team',
		'app.attachment',
		'app.user',
		'app.notification',
		'app.message',
		'app.send_team',
		'app.rec_team',
		'app.revision',
		'app.parent_task',
		'app.trole'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Assignment = ClassRegistry::init('Assignment');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Assignment);

		parent::tearDown();
	}

}
