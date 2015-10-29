<?php
/**
 * LinkageFixture
 *
 */
class LinkageFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'parent_action_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'child_action_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'parent_action_team_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 5),
		'child_action_team_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 5),
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
			'parent_action_id' => 1,
			'child_action_id' => 1,
			'parent_action_team_id' => 1,
			'child_action_team_id' => 1,
			'active' => 1
		),
	);

}
