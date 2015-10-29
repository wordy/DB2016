<?php
App::uses('LinkedAction', 'Model');

/**
 * LinkedAction Test Case
 *
 */
class LinkedActionTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.linked_action',
		'app.action1',
		'app.action2'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->LinkedAction = ClassRegistry::init('LinkedAction');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->LinkedAction);

		parent::tearDown();
	}

}
