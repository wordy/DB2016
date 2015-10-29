<?php
/**
 * ActionFixture
 *
 */
class ActionFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'action_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 5),
		'description' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 500, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'stime' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'etime' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'num_teams' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 2),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 5),
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
			'action_type_id' => 1,
			'description' => 'Lorem ipsum dolor sit amet',
			'stime' => '2013-09-04 20:37:57',
			'etime' => '2013-09-04 20:37:57',
			'num_teams' => 1,
			'user_id' => 1,
			'active' => 1
		),
	);

}
