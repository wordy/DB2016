<?php
App::uses('Change', 'Model');

/**
 * Change Test Case
 *
 */
class ChangeTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.change',
		'app.task',
		'app.ttype',
		'app.tcolour',
		'app.team',
		'app.attachment',
		'app.assignment',
		'app.trole',
		'app.user',
		'app.message',
		'app.revision',
		'app.ctype'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Change = ClassRegistry::init('Change');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Change);

		parent::tearDown();
	}

}
