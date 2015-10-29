<?php
App::uses('TrolesController', 'Controller');

/**
 * TrolesController Test Case
 *
 */
class TrolesControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.trole',
		'app.assignment',
		'app.task',
		'app.ttype',
		'app.tcolour',
		'app.team',
		'app.attachment',
		'app.user',
		'app.notification',
		'app.message',
		'app.send_team',
		'app.rec_team',
		'app.revision',
		'app.parent_task'
	);

}
