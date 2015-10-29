<?php
/**
 * TaskFixture
 *
 */
class TaskFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'task_type_id' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 5),
		'actionable_type_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 5),
		'task_color_id' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 5),
		'team_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 5),
		'start_time' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'end_time' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'short_description' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 140, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'due_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'actionable_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'public' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'deleted' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
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
			'parent_id' => 1,
			'task_type_id' => 1,
			'actionable_type_id' => 1,
			'task_color_id' => 1,
			'team_id' => 1,
			'start_time' => '2013-10-08 16:47:33',
			'end_time' => '2013-10-08 16:47:33',
			'short_description' => 'Lorem ipsum dolor sit amet',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'due_date' => '2013-10-08',
			'actionable_date' => '2013-10-08',
			'active' => 1,
			'public' => 1,
			'deleted' => 1
		),
	);

}
