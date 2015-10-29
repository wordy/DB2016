<?php
App::uses('Trole', 'Model');

/**
 * Trole Test Case
 *
 */
class TroleTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.trole',
		'app.assignment'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Trole = ClassRegistry::init('Trole');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Trole);

		parent::tearDown();
	}

}
