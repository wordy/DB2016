<?php
App::uses('Linkage', 'Model');

/**
 * Linkage Test Case
 *
 */
class LinkageTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.linkage',
		'app.parent_action',
		'app.child_action',
		'app.parent_action_team',
		'app.child_action_team'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Linkage = ClassRegistry::init('Linkage');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Linkage);

		parent::tearDown();
	}

}
