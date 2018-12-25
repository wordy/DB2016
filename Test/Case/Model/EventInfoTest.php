<?php
App::uses('EventInfo', 'Model');

/**
 * EventInfo Test Case
 */
class EventInfoTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.event_info'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->EventInfo = ClassRegistry::init('EventInfo');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->EventInfo);

		parent::tearDown();
	}

}
