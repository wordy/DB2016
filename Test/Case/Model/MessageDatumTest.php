<?php
App::uses('MessageDatum', 'Model');

/**
 * MessageDatum Test Case
 *
 */
class MessageDatumTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.message_datum'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MessageDatum = ClassRegistry::init('MessageDatum');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MessageDatum);

		parent::tearDown();
	}

}
