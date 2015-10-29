<?php
App::uses('AppModel', 'Model');
/**
 * TaskColor Model
 *
 * @property Task $Task
 */
class TaskColor extends AppModel {

 /**
 * Display field
 *
 * @var string
 */
    public $displayField = 'name';
    
    public $validate = array(
        'name' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'You must specify a color name',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
        'code' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'You must specify a color code',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
    );

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Task' => array(
			'className' => 'Task',
			'foreignKey' => 'task_color_id',
			'dependent' => false,
		),
		'Team' => array(
            'className' => 'Team',
            'foreignKey' => 'task_color_id',
            'dependent' => false,
        ), 
	);


    public function getIdByCode($color_code){
        $rs = $this->findByCode($color_code);
        $task_color_id = $rs['TaskColor']['id'];
        
        return $task_color_id;
    }

    public function makeCodeAndNameList(){
        $rs = $this->find('list', array(
            'fields'=>array(
                'TaskColor.code', 
                'TaskColor.name'
            )));

        return $rs;
    }
    
    // 2015 - Used to find team colors not in use
    public function getAvailColorsList(){
        $rs = $this->Team->find('list', array(
            'fields'=>array('Team.task_color_id'),
        ));
        
        $tc = $this->find('list', array(
            'conditions'=> array(
                'id !='=> $rs,
                ),
            'fields'=> array('TaskColor.code', 'TaskColor.name')
        ));
        
        return $tc;
            
    }
    



}
