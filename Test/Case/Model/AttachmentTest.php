<?php
App::uses('Attachment', 'Model');

/**
 * Attachment Test Case
 *
 */
class AttachmentTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.attachment',
		'app.task',
		'app.ttype',
		'app.tcolour',
		'app.team',
		'app.assignment',
		'app.notification',
		'app.message',
		'app.send_team',
		'app.rec_team',
		'app.revision',
		'app.user',
		'app.parent_task'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Attachment = ClassRegistry::init('Attachment');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Attachment);

		parent::tearDown();
	}

}
