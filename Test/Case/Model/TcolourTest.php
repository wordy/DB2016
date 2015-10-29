<?php
App::uses('Tcolour', 'Model');

/**
 * Tcolour Test Case
 *
 */
class TcolourTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.tcolour',
		'app.task'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Tcolour = ClassRegistry::init('Tcolour');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Tcolour);

		parent::tearDown();
	}

}
