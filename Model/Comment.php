<?php
App::uses('AppModel', 'Model');
/**
 * Comment Model
 *
 * @property Task $Task
 * @property User $User
 */
class Comment extends AppModel {
    
        public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        
        $this->virtualFields['user_handle'] = 
        sprintf('SELECT `User`.`handle` from `users` as `User` WHERE `User`.`id` = %s.user_id', $this->alias);
    }

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'text';
    public $order = 'Comment.created DESC';
    

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'task_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			    'message' => 'Task ID is Required',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'User ID is Required',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'text' => array(
			'notblank' => array(
				'rule' => array('notblank'),
				'message' => 'Comment text cannot be empty',
				//'allowEmpty' => false,
				//'required' => false,
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
		'Task' => array(
			'className' => 'Task',
			'foreignKey' => 'task_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
    
    public function afterSave($created, $options = array()){
        if($created==true){
            $task_id = $this->data['Comment']['task_id'];
            $user_id = $this->data['Comment']['user_id'];
            
            $this->Task->Change->newComment($task_id, $user_id);
            
        }
        
    }
    
    
}
