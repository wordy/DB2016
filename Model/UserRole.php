<?php
App::uses('AppModel', 'Model');
/**
 * UserRole Model
 *
 * @property User $User
 */
class UserRole extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'role';
    public $order = 'UserRole.id ASC';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'role' => array(
			'notblank' => array(
				'rule' => array('notblank'),
			),
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_role_id',
			'dependent' => false,
		)
	);

}
