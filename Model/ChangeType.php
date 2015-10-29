<?php
App::uses('AppModel', 'Model');
/**
 * ChangeType Model
 *
 * @property Change $Change
 */
class ChangeType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

    public $order = array('ChangeType.id ASC');


/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
    	'Change' => array(
			'className' => 'Change',
			'foreignKey' => 'change_type_id',
			'dependent' => false,
		)
	);

}
