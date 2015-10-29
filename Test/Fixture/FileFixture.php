<?php
/**
 * FileFixture
 *
 */
class FileFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'action_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'team_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 5),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10),
		'filename' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'date' => array('type' => 'datetime', 'null' => false, 'default' => null),
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
			'user_id' => 1,
			'filename' => 'Lorem ipsum dolor sit amet',
			'url' => 'Lorem ipsum dolor sit amet',
			'date' => '2013-09-06 02:10:08'
		),
	);

}
