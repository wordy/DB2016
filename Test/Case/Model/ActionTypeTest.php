<?php
App::uses('ActionType', 'Model');

/**
 * ActionType Test Case
 *
 */
class ActionTypeTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.action_type',
		'app.task',
		'app.task_type',
		'app.task_color',
		'app.team',
		'app.attachment',
		'app.assignment',
		'app.task_role',
		'app.user',
		'app.change',
		'app.change_type',
		'app.message'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ActionType = ClassRegistry::init('ActionType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ActionType);

		parent::tearDown();
	}

}
