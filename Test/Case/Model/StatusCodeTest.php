<?php
App::uses('StatusCode', 'Model');

/**
 * StatusCode Test Case
 *
 */
class StatusCodeTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.status_code',
		'app.status'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->StatusCode = ClassRegistry::init('StatusCode');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->StatusCode);

		parent::tearDown();
	}

}
