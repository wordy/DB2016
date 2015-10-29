<?php
App::uses('AppModel', 'Model');
/**
 * TaskRole Model
 *
 * @property Assignment $Assignment
 */
class TaskRole extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'description';

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'TasksTeam' => array(
			'className' => 'TasksTeam',
			'foreignKey' => 'task_role_id',
			'dependent' => false,
		)
	);

}
