<?php
App::uses('Revision', 'Model');

/**
 * Revision Test Case
 *
 */
class RevisionTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.revision',
		'app.task',
		'app.ttype',
		'app.tcolour',
		'app.team',
		'app.assignment',
		'app.attachment',
		'app.notification',
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
		$this->Revision = ClassRegistry::init('Revision');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Revision);

		parent::tearDown();
	}

}
