<?php
App::uses('Comment', 'Model');

/**
 * Comment Test Case
 *
 */
class CommentTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.comment',
		'app.task',
		'app.task_type',
		'app.actionable_type',
		'app.task_color',
		'app.team',
		'app.tasks_team',
		'app.task_role',
		'app.teams_user',
		'app.user',
		'app.user_role',
		'app.change',
		'app.change_type',
		'app.print_pref',
		'app.notification'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Comment = ClassRegistry::init('Comment');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Comment);

		parent::tearDown();
	}

}
