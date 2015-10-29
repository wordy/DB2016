<?php
/**
 * TasksTeamFixture
 *
 */
class TasksTeamFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'task_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'team_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 5),
		'task_role_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 5),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'public' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'task_id' => 1,
			'team_id' => 1,
			'task_role_id' => 1,
			'active' => 1,
			'public' => 1,
			'deleted' => 1
		),
	);

}
