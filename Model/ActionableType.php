<?php
App::uses('AppModel', 'Model');
/**
 * ActionableType Model
 *
 */
class ActionableType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
    public $order = "ActionableType.id ASC";
    

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notblank' => array(
				'rule' => array('notblank'),
				'message' => 'Actionable type must have a name',
			),
		),
	);
	
	public $hasMany = array(
		'Task' => array(
			'className' => 'Task',
			'foreignKey' => 'actionable_type_id',
			'dependent' => false,
		)
	);
    
    public function makeList(){
        $result = Cache::read('actionable_types_list', 'short');
        
        if(!$result){
            $result = $this->find('list', array('fields'=>array('ActionableType.id','ActionableType.name')));
            Cache::write('actionable_types_list', $result, 'short');
        }
        return $result;
    }
    
}
