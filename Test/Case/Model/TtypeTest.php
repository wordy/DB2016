<?php
App::uses('Ttype', 'Model');

/**
 * Ttype Test Case
 *
 */
class TtypeTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.ttype',
		'app.task'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Ttype = ClassRegistry::init('Ttype');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Ttype);

		parent::tearDown();
	}

}
