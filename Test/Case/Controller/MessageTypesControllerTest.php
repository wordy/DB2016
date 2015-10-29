<?php
App::uses('MessageTypesController', 'Controller');

/**
 * MessageTypesController Test Case
 *
 */
class MessageTypesControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.message_type',
		'app.message',
		'app.send_team',
		'app.rec_team',
		'app.action',
		'app.action_type',
		'app.user',
		'app.team',
		'app.attachment',
		'app.notification',
		'app.actions_team',
		'app.action_role',
		'app.message_data'
	);

}
