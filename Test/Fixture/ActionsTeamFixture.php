<?php
/**
 * ActionsTeamFixture
 *
 */
class ActionsTeamFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'action_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
		'team_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 5),
		'action_role_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 5),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => null),
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
			'action_id' => 1,
			'team_id' => 1,
			'action_role_id' => 1,
			'active' => 1
		),
	);

}
