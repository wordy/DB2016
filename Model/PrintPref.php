<?php
App::uses('AppModel', 'Model');
/**
 * PrintPref Model
 *
 * @property User $User
 * @property Task $Task
 */
class PrintPref extends AppModel {
    
    public $useTable = 'print_prefs';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'User ID must be numeric',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'task_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Task ID must be numeric',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'hide_detail' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Boolean required',
             
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'hide_task' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Boolean required',
         
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Task' => array(
			'className' => 'Task',
			'foreignKey' => 'task_id',
		)
	);
    
    public function addByUserTask($user_id, $task_id, $hide_detail, $hide_task){
        if(!$user_id || !$task_id){
            return false;
        }
        
        $data = array(
            'user_id'=>$user_id,
            'task_id'=>$task_id,
            'hide_detail'=>$hide_detail,
            'hide_task'=>$hide_task,
            );
            
        $this->create();
           
        if(!$this->save($data)){
            return false;
        }
        
        return true;
    }

    public function delAllByUserTask($user_id, $task_id){
        if(!$user_id || !$task_id){
            return false;
        }
        $this->deleteAll(array(
            'PrintPref.user_id' => $user_id,
            'PrintPref.task_id' => $task_id), 
            false);

        return true;
    }
    
    public function delAllByUser($user_id){
        if(!$user_id){
            return false;
        }
        $this->deleteAll(array(
            'PrintPref.user_id' => $user_id,
            ), 
            false);

        return true;
    }
    
    public function existsByUserTask($user_id, $task_id){
        $rs = $this->find('first', array(
            'conditions'=>array(
                'PrintPref.user_id'=>$user_id,
                'PrintPref.task_id'=>$task_id,
            )));
        
        if(!$rs){ return false;}
        
        return true;
    }
    
    public function existsByUserTaskHideDetail($user_id, $task_id, $hide_detail=1){
        $rs = $this->find('first', array(
            'conditions'=>array(
                'PrintPref.user_id'=>$user_id,
                'PrintPref.task_id'=>$task_id,
                'PrintPref.hide_detail'=>$hide_detail,
        )));
        
        if(!$rs){ return false;}
        
        return true;
    }

    public function existsByUserTaskHideTask($user_id, $task_id, $hide_task=1){
        $rs = $this->find('first', array(
            'conditions'=>array(
                'PrintPref.user_id'=>$user_id,
                'PrintPref.task_id'=>$task_id,
                'PrintPref.hide_task'=>$hide_task,
                )));
        
        if(!$rs){ return false;}
        
        return true;
    }
    
/*
    public function beforeSave($options=array()) {
        $user_id = $this->data['PrintPref']['user_id'];
        $task_id = $this->data['PrintPref']['task_id'];
        $hide_detail = $this->data['PrintPref']['hide_detail'];
        $hide_task = $this->data['PrintPref']['hide_task'];
        
        // Can always hide details (1,0), (1,1)
        if($hide_detail == 1){
            return true;
        }
        // Allow (0,1) Disallow: (0,0)
        if($hide_detail == 0){
            if($hide_task == 1 || $this->existsByUserTaskHideTask($user_id, $task_id)){
                return true;
            }
            
            // If both are 0, the user is effectively removing their prefs for this task
            if($hide_task == 0 || $this->existsByUserTaskHideTask($user_id, $task_id)){
                
                //$this->log('0,0');
                $this->delAllByUserTask($user_id, $task_id);
                return false;
            }
        }
    
        return true;
    }        
    */
    public function getByUser($user_id){
        if(!$user_id){
            return false;
        }
        $rs = $this->find('all',array(
            'conditions'=>array(
                'PrintPref.user_id'=>$user_id),
            'fields'=>array('PrintPref.task_id', 'PrintPref.hide_detail', 'PrintPref.hide_task')
            ));
            
       //$rs = Hash::combine($rs1, null,'{n}.PrintPref.task_id', '{n}.PrintPref.type');     
        //$rs1 = Hash::extract($rs, '{n}.PrintPref[hide_task=true]');
        
        //$rs1 = Hash::combine($rs, '{n}.PrintPref.task_id', '{n}.PrintPref.hide_task');
        //$rs1 = Hash::combine($rs, '{n}.PrintPref[hide_task=true]', '{n}.PrintPref.task_id');
        
        //$rs2 = Hash::combine($rs, '{n}.PrintPref.task_id', '{n}.PrintPref.hide_task');    
        return $rs;
    
    }
    
    public function getUserPrefsByType($user_id){
        $upref = $this->getByUser($user_id);
        $uprint = Hash::combine($upref, '{n}.PrintPref.task_id','{n}.PrintPref.hide_detail', '{n}.PrintPref.hide_task' );
        
        $userPrint = array(
            'hide_task' => (isset($uprint[1]))? array_keys($uprint[1]): array(),
            'hide_detail' => (isset($uprint[0]))? array_keys($uprint[0]): array()
        );
        
        return $userPrint;
        
        
    }
    
    
    public function getByUser2($user_id){
        if(!$user_id){
            return false;
        }
        $rs = $this->find('all',array(
            'conditions'=>array(
                'PrintPref.user_id'=>$user_id),
            'fields'=>array('PrintPref.task_id', 'PrintPref.hide_detail', 'PrintPref.hide_task')
            ));
            
       //$rs = Hash::combine($rs1, null,'{n}.PrintPref.task_id', '{n}.PrintPref.type');     
        //$rs1 = Hash::extract($rs, '{n}.PrintPref');
        
        //$rs1 = Hash::combine($rs, '{n}.PrintPref.task_id', '{n}.PrintPref.hide_task');
        //$rs1 = Hash::combine($rs, '{n}.PrintPref[hide_task=true]', '{n}.PrintPref.task_id');
        
        //$rs2 = Hash::combine($rs, '{n}.PrintPref.task_id', '{n}.PrintPref.hide_task');
        
        //$rs1 = Hash::combine($rs, '{n}.PrintPref.task_id','{n}.PrintPref');    
        
        //$rs2 = Hash::extract($rs, '{n}');
        
        //$s1 = Hash::combine($printPrefs, '{n}.PrintPref.task_id','{n}.PrintPref.hide_detail', '{n}.PrintPref.hide_task' );
        
        return $rs;
    
    }    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
