<?php
App::uses('TasksController', 'Controller');

/**
 * TasksController Test Case
 *
 */
class TasksControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.task',
		'app.ttype',
		'app.tcolour',
		'app.team',
		'app.assignment',
		'app.trole',
		'app.attachment',
		'app.notification',
		'app.message',
		'app.revision',
		'app.user'
	);

}
