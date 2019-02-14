<?php
App::uses('AppModel', 'Model');
/**
 * Assignment Model
 *
 * @property Role $Role
 * @property Task $Task
 */
class Assignment extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'role_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'task_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'role_id',
			'dependent'=>true,
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Task' => array(
			'className' => 'Task',
			'foreignKey' => 'task_id',
			'dependent'=>true,
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


    // Need to use the constructor because we use model aliases for Task (i.e. Parent/Contribution)
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['role_handle'] = sprintf('SELECT `Role`.`handle` from `roles` as `Role` WHERE `Role`.`id` = %s.role_id', $this->alias);
        //$this->virtualFields['priority_date'] = sprintf('LEAST((GREATEST(0, DATE(%s.due_date))), DATE(%s.end_time))', $this->alias, $this->alias);
    }

/*
    public function isControlledBy($id, $user){
        $owner = $this->field('team_id', array('id' => $id)); 
        $user_teams = $user['Teams'];
        
        if(in_array($owner, $user_teams)){
            return true;
        }
        return false;
    }
*/




    public function beforeSave($options=array()) {
        //$this->log($this->data);
        $role_id = ($this->data['Assignment']['role_id'])?: null;
        //$task_role_id = ($this->data['TasksTeam']['task_role_id']) ?: null;
        $task_id = ($this->data['Assignment']['task_id'])?: null;
        
        // Duplicates
        if ($this->existsByTaskAndRole($task_id, $role_id)){
            return false;
        }
        
        return true;
    }

    public function setByTaskAndRole($task, $role){
        $data = array(
            'task_id'=>$task,
            'role_id'=>$role
        );
        
        $this->create();
        return ($this->save($data))? true : false;
    }



    public function assignRoleToTask($task, $role){
        $data = array(
            'task_id'=>$task,
            'role_id'=>$role
        );
        
        $this->create();
        return ($this->save($data))? true : false;
    }

    public function getByTask($task_id){
        $rs = $this->find('list',
            array(
                'conditions'=>array(
                    'Assignment.task_id'=>$task_id,
                ),
                'fields'=>array('Assignment.id','Assignment.role_id')
            )
        );
            
        //if(!empty($rs)){
        //    $id = Hash::extract($rs, '{n}.Assignment.id');
        //    return $id;    
        //}
        return $rs;
    }

    public function getByRoles($roles){
        $rs = $this->find('list',
            array(
                'conditions'=>array(
                    'Assignment.role_id'=>$roles,
                ),
                'fields'=>array('Assignment.id','Assignment.role_id')
            )
        );
            
        return $rs;
    }

    public function getTaskIdsByRoles($roles){
        $rs = $this->find('list',
            array(
                'conditions'=>array(
                    'Assignment.role_id'=>$roles,
                ),
                'fields'=>array('Assignment.id','Assignment.task_id')
            )
        );
            
        return $rs;
    }


    public function existsByTask($task_id){
        $rs = $this->find('first',array(
            'conditions'=>array(
                'Assignment.task_id'=>$task_id,
            )));
        return (!empty($rs))? true:false;
    }


    public function existsByTaskAndRole($task_id, $role_id){
        $rs = $this->find('all',array(
            'conditions'=>array(
                'Assignment.task_id'=>$task_id,
                'Assignment.role_id'=>$role_id,
            )));
        return (!empty($rs))? true:false;
    }
    
    public function getRolesByTask($task){
        $rs = $this->find('list', array(
            'conditions'=>array(
                'Assignment.task_id'=>$task),
            'fields'=>array('id', 'role_id')
        ));
        
        return $rs;
    }


    public function deleteAllByTask($task){
        $this->deleteAll(array('Assignment.task_id'=>$task), false, true);
        return true;       
    }
    
    public function deleteAllByRole($role){
        $this->deleteAll(array('Assignment.role_id'=>$role), false, true);
        return true;       
    }
    
    public function deleteByTaskAndRole($task, $role){
        $this->deleteAll(
            array(
                'Assignment.task_id'=>$task, 
                'Assignment.role_id'=>$role), 
                false, true);
        return true;       
    }










}
