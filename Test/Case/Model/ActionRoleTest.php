<?php
App::uses('ActionRole', 'Model');

/**
 * ActionRole Test Case
 *
 */
class ActionRoleTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.action_role',
		'app.actions_team'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ActionRole = ClassRegistry::init('ActionRole');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ActionRole);

		parent::tearDown();
	}

}
