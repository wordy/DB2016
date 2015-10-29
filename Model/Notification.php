<?php
App::uses('AppModel', 'Model');
/**
 * Notification Model
 *
 * @property Task $Task
 * @property RecTeam $RecTeam
 * @property SendTeam $SendTeam
 */
class Notification extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'type_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'task_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'rec_team_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'send_team_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'body' => array(
			'notblank' => array(
				'rule' => array('notblank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'is_read' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
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
		'Parent' => array(
			'className' => 'Task',
			'foreignKey' => 'parent_task_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
        'Child' => array(
            'className' => 'Task',
            'foreignKey' => 'child_task_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),

		'ReceiveTeam' => array(
			'className' => 'Team',
			'foreignKey' => 'rec_team_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'SendTeam' => array(
			'className' => 'Team',
			'foreignKey' => 'send_team_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
    
    public function getNotificationsByTeam($team){
        $rs = $this->find('all', array(
            'conditions'=>array(
                'OR'=>array(
                    'rec_team_id'=>$team,
                    'type_id <= 100'
                )),
            'order'=>array(
                'Notification.type_id ASC',
                'Notification.created DESC'
            ),
            'contain'=>array(
                'Parent',
                'Child'
            ),
        ));

        return $rs;        
    }

    
    public function getInboxByTeam($team){
        $rs = $this->find('all', array(
            'conditions'=>array(
                'OR'=>array(
                    'type_id =<'=>100,
                    'AND'=>array(
                        'rec_team_id'=>$team,
                        'is_approved'=>0,            
                    
                    ))),
            ));
        return $rs;        
    }
    
    // Includes system message in inbox count
    public function getInboxCountByTeam($team){
        $rs = $this->find('count', array(
            'conditions'=>array(
                'OR'=>array(
                    'type_id <='=> 100,
                    'AND'=>array(
                        'rec_team_id'=>$team,
                        'is_approved'=>0)
                    )
                ),
            )
        );
        return ($rs)? $rs: 0;        
    }

    public function getArchiveCountByTeam($team){
        $rs = $this->find('count', array(
            'conditions'=>array(
                'AND'=>array(
                    'rec_team_id'=>$team,
                    'is_approved'=>1,            
                    
                )
            )
        ));
        return $rs;        
    }

    public function newOpenRequest($from_team, $to_team, $parent_tid){
        if(!$from_team || !$to_team || !$parent_tid){
            return false;
        }
        
        $data = array(
            'type_id'=>300,
            'rec_team_id'=>$to_team,
            'send_team_id'=>$from_team,
            'parent_task_id'=>$parent_tid,
        );

        $this->create();
        if($this->save($data)){
            return true;
        }
        
        return false;
    }
    
    public function newCloseRequest($from_team, $to_team, $parent_tid){
        if(!$from_team || !$to_team || !$parent_tid){
            return false;
        }
        
        $data = array(
            'type_id'=>400,
            'rec_team_id'=>$to_team,
            'send_team_id'=>$from_team,
            'parent_task_id'=>$parent_tid,
        );

        $this->create();
        if($this->save($data)){
            return true;
        }
        
        return false;
    }
    
    
    
    public function removeOpenRequest($team, $parent_tid){
        if(!$team || !$parent_tid){
            return false;
        }
        
        $this->deleteAll(
            array(
                'Notification.parent_task_id'=>$parent_tid,
                'Notification.type_id'=>300,
                'Notification.rec_team_id'=>$team,
                ), 
            false, 
            false);
        return true;
    }


    public function notify($send_team, $rec_team)
    {
        
    }

    public function addAssistance($ptask_id, $ctask_id, $type_id=200){
        
        // Who owns parent task (recipient) & child (sender)
            $p_tid = $this->Parent->getLeadByTask($ptask_id);
            $c_tid = $this->Child->getLeadByTask($ctask_id);
        
        
        $data = array(
            'type_id'=>$type_id,
            'rec_team_id'=>$p_tid,
            'send_team_id'=>$c_tid,
            'parent_task_id'=>$ptask_id,
            'child_task_id'=>$ctask_id,
        );
        
        $this->create();
        if($this->save($data)){
            return true;
        }
        
        return false;
        
    }
    
    public function removeAssistance($parent_tid, $child_tid){
        if(!$parent_tid || !$child_tid){
            return false;
        }
        
        $this->deleteAll(
            array(
                $this->alias.'.parent_task_id'=>$parent_tid,
                $this->alias.'.child_task_id'=>$child_tid,
                $this->alias.'.type_id'=>200,
                ), 
            false, 
            false);
        return true;
    }
    
    public function markRead($nid){
        $this->id = $nid;
        if (!$this->exists()) {
            throw new NotFoundException(__('Invalid notification'));
        }
        
        if($this->saveField('is_read', 1)){
            return true;
        }
        
        return false;
    }
    
    public function markUnread($nid){
        $this->id = $nid;
        if (!$this->exists()) {
            throw new NotFoundException(__('Invalid notification'));
        }
        
        if($this->saveField('is_read', 0)){
            return true;
        }
        
        return false;
    }
    
    public function deleteAllByTask($task_id){
        if(!$task_id){
            return false;
        }
        
        $this->deleteAll(array(
            'OR'=>array(
                'Notification.parent_task_id'=>$task_id,
                'Notification.child_task_id'=>$task_id), 
                false, true));
        return true;    
    }
    
    public function deleteAllByTeam($team_id){
        if(!$team_id){
            return false;
        }
        
        $this->deleteAll(array(
            'OR'=>array(
                $this->alias.'.rec_team_id'=>$team_id,
                $this->alias.'.send_team_id'=>$team_id), 
                false, true));
        return true;    
    }
        
        
        
        
        
        //$this->deleteAll()
        
        
        
    












}
