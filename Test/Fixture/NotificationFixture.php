<?php
/**
 * NotificationFixture
 *
 */
class NotificationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 5, 'key' => 'primary'),
		'team_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 2),
		'message_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 5),
		'revision_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 5),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'is_read' => array('type' => 'boolean', 'null' => false, 'default' => null),
		'is_deleted' => array('type' => 'boolean', 'null' => false, 'default' => null),
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
			'team_id' => 1,
			'message_id' => 1,
			'revision_id' => 1,
			'created' => '2013-09-13 08:23:16',
			'is_read' => 1,
			'is_deleted' => 1
		),
	);

}
