<?php
App::uses('ActionableType', 'Model');

/**
 * ActionableType Test Case
 *
 */
class ActionableTypeTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.actionable_type',
		'app.task',
		'app.task_type',
		'app.task_color',
		'app.team',
		'app.attachment',
		'app.user',
		'app.user_role',
		'app.change',
		'app.change_type',
		'app.tasks_team',
		'app.task_role',
		'app.message'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ActionableType = ClassRegistry::init('ActionableType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ActionableType);

		parent::tearDown();
	}

}
