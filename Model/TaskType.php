<?php
App::uses('AppModel', 'Model');
/**
 * TaskType Model
 *
 * @property Task $Task
 */
class TaskType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
    public $order = 'TaskType.id ASC';

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Task' => array(
			'className' => 'Task',
			'foreignKey' => 'task_type_id',
			'dependent' => false

		)
	);
    
    public function makeListByCategory(){
        $result = Cache::read('task_types_list', 'short');
        
        if(!$result){
            $result = $this->find('list', array(
                'fields'=>array('TaskType.id','TaskType.name', 'TaskType.grouping'))
            );
            
            Cache::write('task_types_list', $result, 'short');
        }
        
        return $result;
    }

}
