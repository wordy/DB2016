<?php
App::uses('AppModel', 'Model');

class Task extends AppModel {
    
    //public $components = array('Js');

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'short_description';
	
    //public $actsAs = array('Tree');
    
    public $stdTaskFields = array(
        'id',
        'start_time',
        'end_time',
        'short_description',
        'task_type',
        'team_code',
        'task_color_code',
        'time_control',
        'time_offset',
    );
    
    public $stdContain = array(
        'Assist'=>array(
            'fields'=>array(
                'Assist.id',
                'Assist.start_time',
                'Assist.end_time',
                'Assist.short_description',
                'Assist.task_type',
                'Assist.team_code',
                'Assist.task_color_code',
                'Assist.time_control',
                'Assist.time_offset',
            )
        ),
        'Comment',
        //'Assist.Assist',
        'Parent'=>array(
            'fields'=>array(
                'Parent.id',
                'Parent.parent_id',
                'Parent.start_time',
                'Parent.end_time',
                'Parent.short_description',
                'Parent.task_type',
                'Parent.team_code',
                'Parent.task_color_code',
                'Parent.time_offset',
                'Parent.time_control',
            )
        ),
        'TasksTeam'=>array(
            'fields'=>array(
                'TasksTeam.team_id',
                'TasksTeam.team_code',
                'TasksTeam.task_role_id',
                )
            ),
    );
    
    
	public $validate = array(
	   'short_description' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'Task must have a short description',
                'allowEmpty' => false,
                'required' => true
            )
        ),
        'task_type_id' => array(
            'numeric' => array(
                'rule' => array('numeric')
            )
        ),
        'actionable_type_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true
            )
        ),
        'team_id' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'You must specify a task lead',
                'allowEmpty' => false,
                'required' => true
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Expected a number'
            )
        ),
        'start_time' => array(
            'datetime' => array(
                'rule' => array('datetime'),
                'message' => 'Input a valid start time (YYYY-MM-DD HH:MM:SS)',
                'allowEmpty' => false,
                'required' => true
            )
        ),
        'end_time' => array(
            'datetime' => array(
                'rule' => array('datetime'),
                'message' => 'Input a valid end time (YYYY-MM-DD HH:MM:SS)',
                'allowEmpty' => false,
                'required' => true
            ),
            'greaterthanstart'=>array(
                'rule' => array('validateEndAfterStart'),
                'message' => 'End time must be greater or equal to start time.'
            )
        ),
        'due_date' => array(
            'datetime' => array(
                'rule' => array('date'),
                'allowEmpty' => true
            )
        ),
        'parent_id' => array(

            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true,
                'message' => 'Expected a number'
            ),
            'noloops' => array(
                'rule' => array('validateParentAllowed'),
                'message' => 'Linking to that parent isn\'t allowed. It would cause a potential loop.',
                'on' => 'update',
            ),
        ),
        'time_offset' => array(

            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true,
                'message' => 'Expected a number'
            ),
            /*
            'noloops' => array(
                'rule' => array('validateParentAllowed'),
                'message' => 'Linking to that parent isn\'t allowed. It would cause a potential loop.',
                'on' => 'update',
            ),*/
        ),
/*                
        'actionable_date' => array(
            'datetime' => array(
                'rule' => array('date'),
                //'message' => 'Your custom message here',
                'allowEmpty' => true)),
 */ 
 );
 
    // Need to use the constructor because we use model aliases for Task (i.e. Parent/Contribution)
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['task_type'] = 
        sprintf('SELECT `TaskType`.`name` from `task_types` as `TaskType` WHERE `TaskType`.`id` = %s.task_type_id', $this->alias);
        
        $this->virtualFields['task_color_code'] = 
        sprintf('SELECT `TaskColor`.`code` from `task_colors` as `TaskColor` WHERE `TaskColor`.`id` = %s.task_color_id', $this->alias);
        
        $this->virtualFields['actionable_type'] = 
        sprintf('SELECT `ActionableType`.`name` from `actionable_types` as `ActionableType` WHERE `ActionableType`.`id` = %s.actionable_type_id', $this->alias);
        
        $this->virtualFields['team_code'] = 
        sprintf('SELECT `Team`.`code` from `teams` as `Team` WHERE `Team`.`id` = %s.team_id', $this->alias);

        //$this->virtualFields['priority_date'] = 
        //sprintf('LEAST((GREATEST(0, DATE(%s.due_date))), DATE(%s.end_time))', $this->alias, $this->alias);
        //sprintf('IF((DATE(%s.due_date) <> 0) AND (DATE(%s.due_date) < DATE(%s.end_time))), %s.due_date, %s.end_time)', $this->alias, $this->alias, $this->alias, $this->alias, $this->alias);
        //$this->virtualFields['priority_date'] = 'LEAST(DATE(%s.due_date), DATE(%s.end_time))';

    }

 /**
 * belongsTo associations
 */
    public $belongsTo = array(
	   'Parent' => array(
            'className' => 'Task',
            'foreignKey' => 'parent_id'
        ),
		'TaskType' => array(
			'className' => 'TaskType',
			'foreignKey' => 'task_type_id',
		),
		'ActionableType' => array(
			'className' => 'ActionableType',
			'foreignKey' => 'actionable_type_id',
		),
		'TaskColor' => array(
			'className' => 'TaskColor',
			'foreignKey' => 'task_color_id',
		),
		'Team' => array(
			'className' => 'Team',
			'foreignKey' => 'team_id',
		)
	);

/**
 * hasMany associations
 * @var array
 */
	public $hasMany = array(
        'Assist'=>array(
            'className' => 'Task',
            'foreignKey' => 'parent_id',
            'order'=>array(
                'Assist.start_time'=>'ASC'
            ),
        ),
        'Change' => array(
            'className' => 'Change',
            'foreignKey' => 'task_id',
            'dependent' => true,
        ),
		'Comment' => array(
			'className' => 'Comment',
			'foreignKey' => 'task_id',
			'dependent' => true,
			'order'=>array(
                'Comment.created'=>'DESC'
            ),
		),
		'TasksTeam' => array(
			'className' => 'TasksTeam',
			'foreignKey' => 'task_id',
			'dependent' => true,
			'order'=>array(
			     'TasksTeam.task_role_id'=>'ASC',
			     'TasksTeam.team_code'=>'ASC',
			 )
		),
        'PrintPref' => array(
            'className' => 'PrintPref',
            'foreignKey' => 'task_id',
            'dependent' => true,
        ),
        /*
        'Notification' => array(
            'className' => 'Notification',
            'foreignKey' => 'parent_task_id',
            'dependent' => true,
        ),*/
	);

    /**
    * isControlledBy method
    * @param string $team_id team_id
    * @param string $user User info from $Auth->User
    * @return void
    */
    
    // Used in TaskController->isAuthorized()
    // If task's lead (Task.team_id) is in User's team list, they're allowed
    public function isControlledBy($task_id, $user){
        $task_owner = $this->field('team_id', array('id' => $task_id)); 
        $user_teams = $user['Teams'];
        
        if(in_array($task_owner, $user_teams)){
            return true;
        }
        return false;
    }
    
    /* 2015: Used to prevent saving changes for deleted tasks
    *  Task.afterDelete() cascades to TasksTeam, this checks if task is deleted before
    *  allowing changes to be saved
    */
    public function isDeleted($task_id){
        $rs = $this->findById($task_id);

        if($rs[$this->alias]['is_deleted'] == 1){
            return true;
        }
        return false;
    }
    
    /* Validation Functions. Used in Model->$validate
     * 
     

         public function validateIdNotParent($check){
        if($check['parent_id'] == $this->data['Task']['id']){
            return false;
        }
        return true;
    }
     * */
    
    // Ensures End >=Start
    public function validateEndAfterStart($check){
        $end = $check['end_time'];
        $start = $this->data[$this->alias]['start_time'];
        
        if((strtotime($end) < (strtotime($start)))){
            return false;
        }
        return true;
    }

    // 2016 
    // Checks if $task is in $parent's chain of parent_ids. Used to prevent loops when tasks are time controlling each other
    // @TODO/NEVERDO?: This prevents some linking even if time_control is false (for safety)    
    public function validateParentAllowed($check){
        $parent = $check['parent_id'];

        if(!$parent){ return true; } // No prob if no parent

        $task = $this->data[$this->alias]['id'];
        $tc = $this->data[$this->alias]['time_control'];
        
        if(isset($task) && isset($parent) && ($task == $parent)){
             return 'Task cannot link to itself'; 
        }

        $ctid = $parent;
        $cpid = null;
        while (!empty($ctid)) {
            $cpid = $this->field('parent_id', array($this->alias.'.id'=>$ctid));
            $ctid = $cpid;
            
            if($ctid == $task){
               return 'Potential Loop: Please choose a different linked task.';
            }
        }
        return true;        
    }
    

    // 2015. Used in Task::beforeSave(). Tests if a potential $parent ever links back to $task (loop).
    public function isChildInPidChain($task, $parent){
        if(!$parent){ return false; }
        if($task == $parent){ return true; }
        
        // Current task and parent IDs
        $ctid = $parent;
        $cpid = null;

        // Loops until there is no parent_id, i.e. top of the chain
        while (!empty($ctid)) {
            $cpid = $this->field('parent_id', array($this->alias.'.id'=>$ctid));
            $ctid = $cpid;
            
            if($ctid == $task){
               return true;
            }
        }
        return false;        
    }

    
    public function beforeSave($options=array()) {
        //$this->log($this->data);
        
        // Prevents storing empty details. Possibly overkill.
        if(isset($this->data[$this->alias]['details'])){
            if($this->data[$this->alias]['details'] == '<p><br></p>'){
                $this->data[$this->alias]['details'] = null;
            }
        }
        
        //Enforces that start time be before end time.  @DBOPS Pref If not, reverse them.
        if((isset($this->data[$this->alias]['end_time'])) && (isset($this->data[$this->alias]['start_time'])) && ((strtotime($this->data[$this->alias]['end_time']) < (strtotime($this->data[$this->alias]['start_time']))))){
            list($this->data[$this->alias]['start_time'], $this->data[$this->alias]['end_time']) = array($this->data[$this->alias]['end_time'], $this->data[$this->alias]['start_time']);
            $this->log('Swapped start and end time in Task::beforeSave');
        }
        
        // Format Offset for saving, only if time controlled
        if(isset($this->data[$this->alias]['time_control'])){
            $t_ctrl = $this->data[$this->alias]['time_control'];    
        }
        else{
            $t_ctrl = 0;
        }
            
        if($t_ctrl == 1){
            $o_min = (int)$this->data['Offset']['minutes'];
            $o_sec = (int)$this->data['Offset']['seconds'];
            $o_sign = $this->data['Offset']['sign'];
            
            if(!$o_min){ $o_min = 0;}
            if(!$o_sec){ $o_sec = 0;}
            
            $new_to = 60*$o_min + $o_sec;
            
            if($o_sign == '-'){
                $new_to = (-1)*$new_to;
            }
            $this->data[$this->alias]['time_offset'] = $new_to;
        }
        else{
            // Offset requires task to be time controlled
            if(!empty($this->data['Offset']['seconds']) || !empty($this->data['Offset']['minutes'])){
                $this->data[$this->alias]['time_offset'] = 0;
            }
        }

        $data_pid = (isset($this->data[$this->alias]['parent_id']))? $this->data[$this->alias]['parent_id'] : null; 
        $data_tc = (isset($this->data[$this->alias]['time_control']))? $this->data[$this->alias]['time_control'] : 0;
        $data_to = (isset($this->data[$this->alias]['time_offset']))? $this->data[$this->alias]['time_offset'] : 0;
        $iDurr = 0;

        if((isset($this->data[$this->alias]['end_time'])) && (isset($this->data[$this->alias]['start_time']))){
            $iDurr = (strtotime($this->data[$this->alias]['end_time']) - strtotime($this->data[$this->alias]['start_time']));            
        }

        // Ensure linked start times for time controlled tasks
        if(isset($data_pid) && ($data_tc == 1)){
            $pstart = $this->getStartTimeByTask($data_pid);
            $tstart = $this->data[$this->alias]['start_time'];
            $test_tstart = date('Y-m-d H:i:s', strtotime($pstart)+$data_to);
            $test_tend = date('Y-m-d H:i:s', strtotime($pstart)+$data_to+$iDurr);
            if($test_tstart != (strtotime($tstart))){
                $this->data[$this->alias]['start_time'] = $test_tstart;
                $this->data[$this->alias]['end_time'] = $test_tend;
            }    
        }

        
        // Grab data pre-save... only bother if we're doing an update (i.e. $this->id exists)
        $old_lead_teams = array();
        $old_push_teams = array();
        $old_open_teams = array();
        $old_closed_teams = array();
        $old_involved_teams = array();
        
        // Pre-existing tasks
        if($this->id){
            // Can't use $this->data (it's lost on save...), so save it here
            $this->presave = $this->findById($this->id);

            // Grab and save old teams
            $teams_before = $this->TasksTeam->getAllByTask($this->id);
            
            if(!empty($teams_before)){
                $old_lead_teams = Hash::extract($teams_before, '{n}.TasksTeam[task_role_id=1].team_id');
                $old_push_teams = Hash::extract($teams_before, '{n}.TasksTeam[task_role_id=2].team_id');
                $old_open_teams = Hash::extract($teams_before, '{n}.TasksTeam[task_role_id=3].team_id');   
                $old_closed_teams = Hash::extract($teams_before, '{n}.TasksTeam[task_role_id=4].team_id');
                $old_involved_teams = Hash::extract($teams_before, '{n}.TasksTeam.team_id');
            }
        }
        $this->old_lead_teams = $old_lead_teams;
        $this->old_push_teams = $old_push_teams;
        $this->old_open_teams = $old_open_teams;
        $this->old_closed_teams = $old_closed_teams;
        $this->old_involved_teams = $old_involved_teams;
        
        return true;
    }
    
    public function afterSave($created, $options = array()){
        
        //$this->log('got new save in task:afterSave');
        //$this->log($this->data);
        $before = $this->presave;

    	if(!empty($this->data['Task']['team_id'])){
            $cur_lead = $this->data['Task']['team_id'];    
    	}        

        if(!empty($this->data['TeamRoles'])){
            $new_tt = $this->data['TeamRoles'];
            $no_tt = $lead_tt = $push_tt = $open_tt = $closed_tt = array();
            if(!empty($new_tt)){
                foreach($new_tt as $te_id => $tr_id){
                    if($tr_id == 0){
                        $no_tt[] = $te_id;
                    }
                    elseif ($tr_id == 1){
                        $lead_tt[] = $te_id;
                    }
                    elseif ($tr_id == 2){
                        $push_tt[] = $te_id;
                    }
                    elseif ($tr_id == 3){
                        $open_tt[] = $te_id;
                    }
                    elseif ($tr_id == 4){
                        $closed_tt[] = $te_id;
                    }
                }
            }
            
        // To be added and to be deleted
        $tba_lt = array_diff($lead_tt, $this->old_lead_teams);
        $tba_pu = array_diff($push_tt, $this->old_push_teams);
        $tba_ot = array_diff($open_tt, $this->old_open_teams);
        $tba_ct = array_diff($closed_tt, $this->old_closed_teams);
        
        // Have no role now; Had role before -- to be deleted
        $tbd = array_intersect($no_tt, $this->old_involved_teams);
          
        // Additions 
        foreach ($tba_lt as $adds){
            $this->TasksTeam->addTeam($this->id, $adds, 1);    
        }
        foreach ($tba_pu as $adds){
            $this->TasksTeam->addTeam($this->id, $adds, 2);    
        }
        foreach ($tba_ot as $adds){
            $this->TasksTeam->addTeam($this->id, $adds, 3);
        }
        foreach ($tba_ct as $adds){
            $this->TasksTeam->addTeam($this->id, $adds, 4);
        }

        // Deletions
        foreach ($tbd as $dels){
            $this->TasksTeam->deleteAllByTaskAndTeam($this->id, $dels);
        }
    }
        // Process changes in Task only if it was a record update    
        if($created == false){
            $after = $this->findById($this->id);

            // Lead Team
            if($after['Task']['team_id'] != $before['Task']['team_id']){
                //un and reset associations
                $this->TasksTeam->changeLeadTeam($this->id, $after['Task']['team_id']);
                $this->Change->changeLeadTeam($this->id, $before['Task']['team_id'], $after['Task']['team_id']);
            }
            // Start Time
            if ($after['Task']['start_time'] != $before['Task']['start_time']){
                $this->Change->changeStartTime($this->id, $before['Task']['start_time'], $after['Task']['start_time']);
                //Change all child tasks that are time linked to this one
                $this->changeChildStartEndTime($this->id);
            }
            // Description - compare text and only record the change if >~5% difference
            if($after['Task']['short_description'] != $before['Task']['short_description']){
                similar_text($before['Task']['short_description'], $after['Task']['short_description'], $percent);
                
                if($percent < 95){
                   $this->Change->changeShortDesc($this->id, $percent);
                }
            }
            // Task Due Date
            if ($after['Task']['due_date'] != $before['Task']['due_date']){
               $this->Change->changeDueDate($this->id, $before['Task']['due_date'], $after['Task']['due_date']);
            }
            // Actionable Type
            if ($after['Task']['actionable_type_id'] != $before['Task']['actionable_type_id']){
               $this->Change->changeActionableStatus($this->id, $before['Task']['actionable_type_id'], $after['Task']['actionable_type_id']);
            }
            // Changed Parent
            if($after['Task']['parent_id'] != $before['Task']['parent_id']){
                // Unlink from old parent (in parent)
                if(!empty($before['Task']['parent_id'])){
                    $this->Change->childLeft($before['Task']['parent_id'], $this->id);
                    $this->Change->unsetParent($this->id, $before['Task']['parent_id']);    
                }
                
                // Only record these if there is a new parent (i.e. parent wasn't removed)
                if(!empty($after['Task']['parent_id'])){
                    // Record in parent and child
                    $this->Change->newChild($after['Task']['parent_id'], $this->id);
                    $this->Change->setParent($this->id, $after['Task']['parent_id']);
                }
            }
        }
        
        //NEW TASKS:
        if($created==true){
            $after = $this->findById($this->id);
            
            //Set Lead
            $new_lead_team_id = $after['Task']['team_id'];
            $this->TasksTeam->addTeam($this->id, $new_lead_team_id, 1);
            
            //Record Due Date
            if(!empty($after['Task']['due_date'])){
                //$newt = date('M d', strtotime($after['Task']['due_date'])); 
                $this->Change->changeDueDate($this->id, null, $after['Task']['due_date']);
            }
            
            //Record Actionable Status
            if(!empty($after['Task']['actionable_type_id'])){
               $this->Change->changeActionableStatus($this->id, null, $after['Task']['actionable_type_id']);
            }
            
            //Record Link to Parent in Parent & Child
            if(!empty($after['Task']['parent_id'])){
                // Record changes in parent & child                
                $this->Change->newChild($after['Task']['parent_id'], $this->id);
                $this->log('set parents in Task aftersave');
                $this->Change->setParent($this->id, $after['Task']['parent_id']);
                
            }
        }
    }

    public function beforeDelete($cascade=true){
        // Temp field set so that `Change` isn't recorded for tasks that are deleted
        // i.e. prior to deleting tasks, all teams are removed (Tasks_Team).  This would trigger
        // changes to be recorded (team unlinked), even though the task was deleted.
        // This temp flag is set before delete, and checked before recording changes
        // Refer to Change model.
        $this->saveField('is_deleted', 1);
        
        $task = $this->findById($this->id);
        
        if(!empty($task['Task']['parent_id'])){
            // Record unlinking of child task in parent
            $this->Change->childLeft($task['Task']['parent_id'], $task['Task']['id']);
        }
        return true;
    }

    public function afterDelete(){
        $this->unsetChildrenByParentId($this->id);   
        return true;
    }
    
    // Used in callback when deleting teams
    public function deleteAllByTeam($team){
        if(!$team){
            return false;
        }
        
        $this->deleteAll(
            array(
                $this->alias.'.team_id'=>$team
            ),
            true,
            true);
        
        return true;       
    }
    
    
    
    
    // 2016
    // unsets parent_id, time_offset, time_control
    // Used in Task::afterDelete
    public function unsetChildrenByParentId($parent_task){
        if(!$parent_task){
            return false;
        }
            
        $rs = $this->find('all', array(
            'conditions'=>array(
                'parent_id'=>$parent_task),
            'fields'=>array('id')
        ));
        
        if(!empty($rs)){
            $tasks = Hash::extract($rs, '{n}.Task.id');    
        
            $this->updateAll(
                array(
                    'parent_id'=>null,
                    'time_offset'=>null,
                    'time_control'=>0),
                array(
                    $this->alias.'.id'=>$tasks
                )
            );
        }
        return true;
    }
    
     // 2016
     /*
    public function unsetChildTimeCtrlByParent($task_id){
        if(!$task_id){
            return false;
        }
            
        $rs = $this->find('all', array(
            'conditions'=>array(
                'parent_id'=>$task_id),
                'time_control'=>true,
            'fields'=>array('id')
        ));
        
        if(!empty($rs)){
            $tasks = Hash::extract($rs, '{n}.Task.id');    
        
            if($this->updateAll(array(
                'parent_id'=>null),
                array(
                    $this->alias.'.id'=>$tasks
                ))){
                
                return true;    
            }
        }
        
        return true;
    }
      */
    
    // 2016 - Used in TasksTeam::afterDelete. If parent task removes a team, unlink any tasks they had already linked.
    public function resetPidTcToByParentAndTeam($parent_task, $team){
            
        if(!$parent_task || !$team){
            return false;
        }
            
        $rs = $this->find('all', array(
            'conditions'=>array(
                'parent_id'=>$parent_task,
                'team_id'=>$team 
            ),
            'fields'=>array('id')
        ));
        
        if(!empty($rs)){
            $task_ids = Hash::extract($rs, '{n}.Task.id');    

            if($this->updateAll(
                array(
                    'parent_id'=>null,
                    'time_offset'=>null,
                    'time_control'=>0),
                array(
                    $this->alias.'.id'=>$task_ids
                ))){

                foreach($task_ids as $k=>$task_id){
                    $this->Change->parentDisconnected($task_id, $parent_task);
                }
            }
        }
        return true;    
    }
    
/*********************
 * 
 * GETTERS
 * 
 * 
 *********************/
 
    // Used in Task AfterSave
    public function getLeadByTask($task){
        if($this->exists($task)){
            $team = $this->field('team_id', array($this->alias.'.id'=>$task));
            return $team;
        }
    }

    public function getLeadCodeByTask($task){
        if($this->exists($task)){
            $team = $this->field('team_code', array($this->alias.'.id'=>$task));
            return $team;
        }
    }

    
    public function getStartTimeByTask($task){
        if(!$task){return false;}
        $rs = $this->findById($task);
        $stime = $rs['Task']['start_time'];
        return $stime;
    }

    public function getShortDescByTask($task){
        if(!$task){return false;}
        $rs = $this->findById($task);
        $sdesc = $rs['Task']['short_description'];
        return $sdesc;
    }    

    public function getTimeControlByTask($task){
        if(!$task){return false;}
        $rs = $this->findById($task);
        $tc = $rs['Task']['time_control'];
        return $tc;
    }    

    
    //2016
    public function allTasksList(){
        $rs = $this->find('all', array(
            'order'=>array('Task.team_code ASC','Task.start_time ASC'),
            'fields'=>array(
                'Task.id', 'Task.short_description', 'Task.team_code', 'Task.start_time', 'Task.team_id'
            )));
        
        foreach ($rs as $k => $task){
            $tt = $task['Task']['start_time'];
            unset($rs[$k]['Task']['start_time']);
            
            $rs[$k]['Task']['start_time'] = date('M j g:i A', strtotime($tt));
        }
        
        $result = Hash::combine(
            $rs,
            '{n}.Task.id',
            array('%s: (%s) %s', 
                '{n}.Task.start_time', 
                '{n}.Task.team_code', 
                '{n}.Task.short_description'
            ),
            '{n}.Task.team_code'
            );

        return $result;
    }
     

/**********
 * Setters
 *
 **********/

    public function saveTimeshift($data) {
        return CakeSession::write('Auth.User.TimeShift',$data);
    }
 
    public function readTimeshift() {
        return CakeSession::read('Auth.User.TimeShift');
    }

    public function userTimeshift(){
        $cts = CakeSession::read('Auth.User.Timeshift');

        $rs = $this->find('all',array(
            'conditions'=>array(
                'Task.id' => $cts
            ),
            'fields'=>array('Task.id', 'Task.start_time', 'Task.end_time', 'Task.short_description'),
            'order'=>'Task.start_time ASC',
         ));
         return $rs;
    }

    public function makeSafeCompileSettings($raw=array()){
        // @TODO: Change time period for "new" to variable based on how far from event $now is
        //$now = date('Y-m-d');
        //$owa = date('Y-m-d', strtotime("-1 weeks"));
        //$twa = date('Y-m-d', strtotime("-2 weeks"));
        $cst = Configure::read('CompileStart');
        $cen = Configure::read('CompileEnd');
        
        // Process from submitted settings.  Set defaults if necessary.
        $teams = (!empty($raw['Teams']))? $raw['Teams']: array();
        $sdate = (isset($raw['start_date']))? $raw['start_date']: '';
        $edate = (isset($raw['end_date']))? $raw['end_date']: ''; 
        $view_details = (isset($raw['view_details']))? (int)$raw['view_details']: 1;
        $view_threaded = (isset($raw['view_threaded']))? (int)$raw['view_threaded']: 1;
        $view_links = (isset($raw['view_links']))? (int)$raw['view_links']: 1;
        $view_type = (isset($raw['view_type']))? (int)$raw['view_type']: 1;
        $sort = (isset($raw['sort']))? (int)$raw['sort']: 0;
        
        // DEFAULT: show past 2 weeks if nothing set
        if(!$sdate || !$edate){
            $sdate = $cst;
            $edate = $cen;
        }
        
        /*
        // DEFAULT: only end date? Grab 2 weeks before that
        elseif(!$sdate && $edate){
            $sdate = date('Y-m-d', strtotime(date("Y-m-d", strtotime($edate)) . " -2 weeks"));
        }
        // DEFAULT: only start date? Grab 2 weeks after that
        elseif($sdate && !$edate){
            $edate = date('Y-m-d', strtotime(date("Y-m-d", strtotime($sdate)) . " +2 weeks"));
        }
         */
        // Assume they meant it the other way, swap them
        if ($sdate > $edate){
            list($sdate, $edate) = array($edate, $sdate);
        }
        
        $clean = array(
            'Teams'=>$teams,
            'start_date'=>$sdate,
            'end_date'=>$edate,
            'sort'=>$sort,
            'view_type'=>$view_type,        
            'view_details'=>$view_details,
            'view_links'=>$view_links,
            'view_threaded'=>$view_threaded,
            //'page'=>$page,    
        );
        return $clean;
    }

    //2015
    public function makeCompileConditions_old($settings=array()){
        $now = date('Y-m-d');
        $owa = date('Y-m-d', strtotime("-1 weeks"));
        $twa = date('Y-m-d', strtotime("-2 weeks"));
        $twfn = date('Y-m-d', strtotime("+2 weeks"));
            
        $teams = $settings['Teams'];
        $sdate = $settings['start_date'];
        $edate = $settings['end_date'];
        //$show_details = (int)$settings['show_details'];
        //$show_pushed = (int)$settings['show_pushed'];
        $view_type = (int)$settings['view_type'];
        $sort = (int)$settings['sort'];
        //$page = (int)$settings['page'];
        
        
        // Conditions Initialization
        $order = array();
        $conditions = array();
        $limit = 25;
        $contain = array();
        $roles = array();
        
        
        // Conditions
        // Dates -- 1s less than a full day to capture all tasks on edate
        if(!empty($sdate) && !empty($edate)){
            $conditions['AND'][]= array(
                'Task.end_time <= ' => date('Y-m-d H:i:s', strtotime($edate)+86399),
                'Task.start_time >= ' => date('Y-m-d H:i:s', strtotime($sdate))
            );
        }

        // Need pushed tasks for everything except rundown view
        /*
        if($view_type != 1){
            $show_pushed = 1;
        }
        */
        // Show all roles (overwritten by 'show_pushed'=0)
        $roles = array(1, 2, 3, 4);
        
        //if($show_pushed == 0){
        //    $roles = array(1);
        //}

        switch ($sort) {
            case 0:
                $order = 'Task.start_time ASC';
                break;
            case 1:
                $order = 'Task.start_time DESC';
                break;
        }
        
        // View Types
        /* 2015:
         * 0: "Threaded" --> linked tasks under tasks (default)
         * 1: "Rundown".  No threading, one task per line 
         * 2: Due only
         * 3: Assist Only (aka "to do list")
         * 4: Due & assisting soon
         * 5: Action Items
         * 6: Recently Created
         */
         
        // Threaded.  If task's parent_id is set, it should appear UNDER the parent
        /*
        if($view_type == 0){
       
            $conditions['AND'][] = array(
                'Task.parent_id ='=>null
            );
        }
        
        // Rundown
        if($view_type == 1){
        }
        */
        
        // Lead Only
        if($view_type == 1){
            //$conditions['AND'] = array(
            //    'Task.due_date !=' => null,
            //);
            //$order = 'Task.due_date ASC';
            $roles = array(1);
        }
        
        // Open Requests
        if($view_type == 2){
            $roles = array(3);
            $conditions['AND'] = array();
            //$order = 'Task.start_time ASC';
        }

        // Assisting & Due Soon
        if($view_type == 10){
            $roles = array(3);
            $conditions['AND'] = array(
                'OR'=>array(
                    //array('Task.actionable_type_id !='=>null),
                    array(
                        'AND'=>array(
                            array(
                                'Task.due_date >'=> $now),
                            array(
                                'Task.due_date <'=> $twfn
                                )
                            )
                        ),
                    array(
                        'AND'=>array(
                            array(
                                'Task.end_time > '=> $now),
                            array(
                                'Task.end_time <' => $twfn
                                )
                        )
                    )
            ));
            $order = 'Task.end_time ASC';
        }

        // Recently Created
        if($view_type == 100){
            $conditions['AND'] = array();
            $order = 'Task.created DESC';
            $roles = array(1, 2, 3, 4);
        }
        
        // Subquery looking for tasks where $teams are listed in any of the given $roles
        $conditionsSubQuery['`TasksTeam`.`team_id`'] = $teams;
        $conditionsSubQuery['`TasksTeam`.`task_role_id`'] = $roles;
        $db = $this->TasksTeam->getDataSource();
        $subQuery = $db->buildStatement(
            array(
                'fields'     => array('DISTINCT `TasksTeam`.`task_id`'),
                'table'      => $db->fullTableName($this->TasksTeam),
                'alias'      => 'TasksTeam',
                'limit'      => null,
                'offset'     => null,
                'joins'      => array(),
                'conditions' => $conditionsSubQuery,
                'order'      => null,
                'group'      => null
            ),$this);
        $subQuery = '`Task`.`id` IN (' . $subQuery . ') ';
        $subQueryExpression = $db->expression($subQuery);
        $conditions['AND'][]= $subQueryExpression; 

        // Actionable. Overwrite other AND conditions first
        if($view_type == 500){
            $conditions['AND'] = array(
                'Task.actionable_type_id !='=>null
            );
            $order = array('Task.due_date ASC', 'Task.start_date ASC');
        }

        // DEFAULT
        if(empty($order)){
            $order = array('Task.start_time' =>'ASC');
        }
        
        $contain = array(
            'Assist'=>array(
                'fields'=>array(
                    'Assist.id',
                    'Assist.start_time',
                    'Assist.end_time',
                    'Assist.short_description',
                    'Assist.task_type',
                    'Assist.team_code',
                    'Assist.task_color_code',
                    'Assist.time_control',
                    'Assist.time_offset',
                )
            ),
            'Comment',
            //'Assist.Assist',
            'Parent'=>array(
                'fields'=>array(
                    'Parent.id',
                    'Parent.parent_id',
                    'Parent.start_time',
                    'Parent.end_time',
                    'Parent.short_description',
                    'Parent.task_type',
                    'Parent.team_code',
                    'Parent.task_color_code',
                    'Parent.time_offset',
                    'Parent.time_control',
                )
            ),
            'TasksTeam'=>array(
                'fields'=>array(
                    'TasksTeam.team_id',
                    'TasksTeam.team_code',
                    'TasksTeam.task_role_id',
                    )
                ),
            'Change'=>array(
                'conditions'=>array(
                    'Change.created >'=>$owa
                ),
                'fields'=>array(
                    'Change.created'
                )
            )
        );
        
        $cs = array(
            'teams'=>$teams,
            'start_date'=>$sdate,
            'end_date'=>$edate,
            'sort'=>$sort,
            'conditions'=>$conditions,
            'order'=>$order,
            'contain'=>$contain,
            'limit'=>$limit,
            //'page' => $page,
        );
        return $cs;
    } 
    
    // Used in TasksController->timeShift()
    public function incrementTaskTime($tasks=array(), $increment = null){
        if(!$tasks || !$increment){
            return false;
        }
        foreach($tasks as $tid){
            $this->id = $tid;

            $rs = $this->read(null, $this->id);
            $cur_s = strtotime($rs['Task']['start_time']);
            $cur_e = strtotime($rs['Task']['end_time']);

            $new_s = date('Y-m-d H:i:s', ($cur_s + $increment));
            $new_e = date('Y-m-d H:i:s', ($cur_e + $increment));

            $data = array(
                'id'=>$tid,
                'start_time' => $new_s,
                'end_time' => $new_e,
            );
            
            $this->save($data);
        }
        return true;
    }
    
    //2016
    public function makeLinkableParentsList($team){
        $tids = $this->TasksTeam->getLinkableParentsByTeam($team);

        $rs = $this->find('all', array(
            'conditions'=>array(
                'Task.team_id !='=>null, 
                'Task.team_code !='=>null, 
                'Task.id'=>$tids),
            'order'=>array(
                'Task.team_code ASC',
                'Task.start_time ASC'),
            'fields'=>array(
                'Task.id', 
                'Task.short_description',
                'Task.task_type',  
                'Task.team_code', 
                'Task.start_time', 
                'Task.team_id')
        ));
            
        $result = Hash::combine(
            $rs,
            '{n}.Task.id',
            '{n}.Task',
            '{n}.Task.team_code'
            );
            
        return $result;
    }

    
    // 2016
    // Used in Task::afterSave()
    // If start time of a task changes, and it has child tasks linked that are time controlled
    // alter start/end time
    public function changeChildStartEndTime($parent_tid){
        $this->id = $parent_tid;
        $p_start = $this->field('start_time');
        $ipstart = strtotime($p_start);
        
        $rs = $this->find('all', array(
            'conditions'=>array(
                $this->alias.'.parent_id'=>$parent_tid,
                $this->alias.'.time_control'=>1),
            'fields'=>array(
                $this->alias.'.id', $this->alias.'.start_time', $this->alias.'.end_time', $this->alias.'.time_offset'
            )
        ));
        
        foreach($rs as $task){
            $ctask = $task['Task']['id'];
            $start = strtotime($task['Task']['start_time']);
            $end = strtotime($task['Task']['end_time']);
            $off = (isset($task['Task']['time_offset']))? (int)$task['Task']['time_offset']: 0;
            $curDur = $end - $start;
            $new_cs = ($ipstart + $off);
            $new_start = date('Y-m-d H:i:s', $new_cs);
            $new_end = date('Y-m-d H:i:s', ($new_cs+$curDur));
            
            $this->id = $ctask;
            $this->saveField('start_time', $new_start);
            $this->id = $ctask;
            $this->saveField('end_time', $new_end);
            
            $this->Change->movedByParent($ctask, $task['Task']['start_time'], $new_start, $parent_tid);
            
        }
        return true;
    }

    public function linkableParentsList($team, $current=null, $child_task=null){
        $rs = $this->makeLinkableParentsList($team);
        $nl = array();
        foreach($rs as $tcode => $tids){
            foreach($tids as $tid => $task){
                
                // Prevent linking to self
                if($tid != $child_task){
                    $desc = date('M j g:iA', strtotime($task['start_time'])).': ('.$task['team_code'].') ['.$task['task_type'].'] '.$task['short_description'];
        
                    $nl[$tcode][] = array(
                        'name'=>$desc,
                        'value'=>$task['id'],
                        'data-teamid' => $task['team_id'],
                        'data-stime' => $task['start_time'],
                    );
                }
            } 
        } 
        return $nl;      
    }
    
    public function offsetToMinSecParts($seconds){
        if(!$seconds){
            return false;
        }
        elseif($seconds == 0){
            $sign = '+';
            $m = 0;
            $s = 0;    
        }
        elseif($seconds>0){
            $getMins = floor($seconds/60);
            $getSecs = floor($seconds % 60);
            //
            $sign = '+';
            $m = $getMins;
            $s = $getSecs;
        }
        elseif($seconds<0){
            $getMins = floor(abs($seconds)/60);
            $getSecs = floor(abs($seconds) % 60);
            $sign = '-';
            $m = $getMins;
            $s = $getSecs;
            }
        return array('sign'=>$sign, 'min'=>$m, 'sec'=>$s);
    }    
    

/* TESTING*/


    //2016: @TODO: threaded
    /*
    public function getRootPidChain($task){
        $ctid = $task;
        $cpid = null;
        $tcc = array();
        
        // Traverse up parent_id, stops at root (parent_id=null)       
        do{
            $cpid = $this->field('parent_id', array($this->alias.'.id'=>$ctid));
            $ctid = $cpid;
            ($cpid)? $tcc[]=$cpid:null;
        }
        while($ctid);
        
        // Clarity: Reverse array so $tcc[0] is top level root [parent_id = null]
        return array_reverse($tcc);        
    }
    */

    
    
    //2015
    /*
    public function makeLinkableParentsList($team){
        $tids = $this->TasksTeam->getLinkableParentsByTeam($team);
        
        $rs = $this->find('all', array(
            'conditions'=>array('Task.team_id !='=>null, 'Task.team_code !='=>null, 'Task.id'=>$tids),
            'order'=>array('Task.team_code ASC','Task.start_time ASC'),
            'fields'=>array(
                'Task.id', 'Task.short_description', 'Task.team_code', 'Task.start_time', 'Task.team_id'
            )));
        
        foreach ($rs as $k => $task){
            $tt = $task['Task']['start_time'];
            unset($rs[$k]['Task']['start_time']);
            
            $rs[$k]['Task']['start_time'] = date('M-j Y g:i:s A', strtotime($tt));
        }
        

        $result = Hash::combine(
            $rs,
            '{n}.Task.id',
            array('%s: (%s) %s', 
                '{n}.Task.start_time', 
                '{n}.Task.team_code', 
                '{n}.Task.short_description'
            ),
            '{n}.Task.team_code'
            );
            
            //$this->log($result);
        
        

        return $result;
    }
     
     */

    public function getOpenRequestsByTeam($team){
        $owa = date('Y-m-d', strtotime('-1 week'));
        $tids = $this->TasksTeam->getByTeamsAndRoles($team, array(3));
        //$this->virtualFields['priority_date'] = 'LEAST(GREATEST(0,DATE(`Task`.`due_date`)), DATE(`Task`.`end_time`))';
        $this->virtualFields['priority_date'] = 'IF(Task.due_date IS NOT NULL AND DATE(Task.due_date) < DATE(Task.end_time), DATE(Task.due_date), DATE(Task.end_time))';
        
        $rs = $this->find('all', array(
            'conditions'=>array(
                'Task.id'=>$tids,
                'Task.start_time >'=>Configure::read('CompileStart'),
                
              
            ),
            'contain'=>array(
                'TasksTeam',
                'Change'=>array(
                    'conditions'=>array('Change.created >'=>$owa),
                    'fields'=>array(
                        'Change.created')
                ),
                //'Parent',
                //'Assist',
            ),
            'fields'=>array(
                'Task.priority_date',
                'Task.id',
                'Task.short_description',
                'Task.start_time',
                'Task.end_time',
                'Task.due_date',
                'Task.task_type',
        //        'LEAST(DATE(`Task`.`due_date`), DATE(`Task`.`end_date`)) as `Task`.`priority_date`',
            ),
            'order'=>array(
                'Task.priority_date'=> 'ASC'),
            
        ));
        
        return $rs;
        
    }

    public $virtualFields = array(
        //    'priority_date' => 'MAX(Task.due_date, Task.end_time)'
        );

    public function getOpenWaitingByTeam($team){
                $owa = date('Y-m-d', strtotime('-1 week'));
        
        $tids = $this->TasksTeam->getOpenWaitingByTeam($team);
        
        $this->virtualFields['priority_date'] = 'IF(Task.due_date IS NOT NULL AND DATE(Task.due_date) < DATE(Task.end_time), DATE(Task.due_date), DATE(Task.end_time))';
         
        
        $rs = $this->find('all', array(
            'conditions'=>array(
                'Task.id'=>$tids
            ),
            'contain'=>array(
                'TasksTeam',
                'Change'=>array(
                    'conditions'=>array('Change.created >'=>$owa),
                    'fields'=>array(
                        'Change.created')
                ),
                //'Parent',
                'Assist'=>array(
                    'fields'=>$this->stdTaskFields
                ),
                
                
            ),
            'order'=>'Task.priority_date ASC',
        ));
        
        return $rs;
        
    }

 //2015
    public function makeCompileConditions($settings=array()){
        $now = date('Y-m-d');
        $owa = date('Y-m-d', strtotime("-1 weeks"));
        $twa = date('Y-m-d', strtotime("-2 weeks"));
        $twfn = date('Y-m-d', strtotime("+2 weeks"));
        $cstart = Configure::read('CompileStart');
        $cend = Configure::read('CompileEnd');
            
        $teams = isset($settings['Teams'])? $settings['Teams']: array();
        $sdate = isset($settings['start_date'])? $settings['start_date']: $cstart;
        $edate = isset($settings['end_date'])? $settings['end_date']: $cend;
        $sort = isset($settings['sort'])? (int)$settings['sort']: 0;
        $view_type = (isset($settings['view_type']))? (int)$settings['view_type']: 1;
        $view_details = (isset($settings['view_details']))? (int)$settings['view_details']: 1;
        $view_links = (isset($settings['view_links']))? (int)$settings['view_links']: 1;
        $view_threaded = (isset($settings['view_threaded']))? (int)$settings['view_threaded']: 1;
        //$limit = (isset($settings['limit']))? (int)$settings['limit']: 1;
        
        // Conditions Initialization
        //$order = array();
        $conditions = array();
        $limit = 25;
        $contain = array();
        $roles = array(1, 2, 3, 4);        
        $order = ($sort)? 'Task.start_time DESC':'Task.start_time ASC';

        // Conditions
        // Dates -- 1s less than a full day to capture all tasks on edate
        if(!empty($sdate) && !empty($edate)){
            $conditions['AND'][]= array(
                'Task.end_time <= ' => date('Y-m-d H:i:s', strtotime($edate)+86399),
                'Task.start_time >= ' => date('Y-m-d H:i:s', strtotime($sdate))
            );
        }


        
        
        // View Types
        /*
         * 1: Rundown
         * 10: Lead Only
         * 30: Incoming Open Request
         * 31: Outgoing Open Request
         * 100: Recent
         * 500: Action Items
         */
                          
        $useSubquery = false;
        
        if($view_type == 1){
            $roles = array(1,2,3,4);
            $useSubquery = true;
        }
        
        // Lead Only
        if($view_type == 10){
            $roles = array(1);
            $useSubquery = true;
        }
        
        // Incoming Open Requests
        if($view_type == 30){
            $roles = array(3);
            $conditions['AND'] = array();
            $useSubquery = true;
        }

        // Outgoing Open Requests
        if($view_type == 31){
            $ow_tasks = $this->TasksTeam->getOpenWaitingByTeam($teams);
            $conditions['AND'] = array(
                'Task.id'=>$ow_tasks,
            );
            $useSubquery = false;
        }
        
        // Recently Created
        if($view_type == 100){
            $conditions['AND'] = array();
            $order = 'Task.modified DESC';
            $roles = array(1, 2, 3, 4);
            $useSubquery = true;
        }
        
        // Assisting & Due Soon
        if($view_type == 399){
            $roles = array(3);
            $conditions['AND'] = array(
                'OR'=>array(
                    //array('Task.actionable_type_id !='=>null),
                    array(
                        'AND'=>array(
                            array(
                                'Task.due_date >' => $now),
                            array(
                                'Task.due_date <' => $twfn
                                )
                            )
                        ),
                    array(
                        'AND'=>array(
                            array(
                                'Task.end_time >' => $now),
                            array(
                                'Task.end_time <' => $twfn
                                )
                        )
                    )
            ));
            $order = 'Task.end_time ASC';
            $useSubquery = true;
        }
        
        // Actionable. Overwrite other AND conditions first
        if($view_type == 500){
            $conditions['AND'] = array(
                'Task.actionable_type_id !='=>null
            );
            $order = 'Task.start_time ASC';
            $useSubquery = false;
        }

        if($useSubquery){
            // Subquery looking for tasks where $teams are listed in any of the given $roles
            $conditionsSubQuery['`TasksTeam`.`team_id`'] = $teams;
            $conditionsSubQuery['`TasksTeam`.`task_role_id`'] = $roles;
            $db = $this->TasksTeam->getDataSource();
            $subQuery = $db->buildStatement(
                array(
                    'fields'     => array('DISTINCT `TasksTeam`.`task_id`'),
                    'table'      => $db->fullTableName($this->TasksTeam),
                    'alias'      => 'TasksTeam',
                    'limit'      => null,
                    'offset'     => null,
                    'joins'      => array(),
                    'conditions' => $conditionsSubQuery,
                    'order'      => null,
                    'group'      => null
                ),$this);
            $subQuery = '`Task`.`id` IN (' . $subQuery . ') ';
            $subQueryExpression = $db->expression($subQuery);
            $conditions['AND'][]= $subQueryExpression;    
        }

        // DEFAULT
        if(empty($order)){
            $order = array('Task.start_time' =>'ASC');
        }
        
        $contain = array(
            'Assist'=>array(
                'fields'=>array(
                    'Assist.id',
                    'Assist.start_time',
                    'Assist.end_time',
                    'Assist.short_description',
                    'Assist.task_type',
                    'Assist.team_code',
                    'Assist.task_color_code',
                    'Assist.time_control',
                    'Assist.time_offset',
                )
            ),
            'Comment',
            //'Assist.Assist',
            'Parent'=>array(
                'fields'=>array(
                    'Parent.id',
                    'Parent.parent_id',
                    'Parent.start_time',
                    'Parent.end_time',
                    'Parent.short_description',
                    'Parent.task_type',
                    'Parent.team_code',
                    'Parent.task_color_code',
                    'Parent.time_offset',
                    'Parent.time_control',
                )
            ),
            'TasksTeam'=>array(
                'fields'=>array(
                    'TasksTeam.team_id',
                    'TasksTeam.team_code',
                    'TasksTeam.task_role_id',
                    )
                ),
            'Change'=>array(
                'conditions'=>array(
                    'Change.created >'=>$owa
                ),
                'fields'=>array(
                    'Change.created'
                )
            )
        );
        
        $cs = array(
            'teams'=>$teams,
            'start_date'=>$sdate,
            'end_date'=>$edate,
            'sort'=>$sort,
            'conditions'=>$conditions,
            'order'=>$order,
            'contain'=>$contain,
            'limit'=>$limit,
            'view_type'=>$view_type,
            'view_details'=>$view_details,
            'view_links'=>$view_links,
            'view_threaded'=>$view_threaded,
            //'page' => $page,
        );
        
        //$this->log($cs);
        return $cs;
}


    public function getUrgentByTeam($team){
        $order = array();
        $conditions = array();

        //$settings = $this->Session->read('Auth.User.Compile');
            
        // Enforced for Urgent
        $settings['view_type']= 399;
        $settings['start_time'] = null;
        $settings['end_time'] = null;
        $settings['Teams'] = array($team);

        // Process settings, set defaults if necessary        
        $cc = $this->makeCompileConditions($settings);
        
        //$this->log($cc);
        
        $teams = $cc['teams'];
        $conditions = $cc['conditions'];
        $order = $cc['order'];
        $contain = $cc['contain'];
        $limit = $cc['limit'];
       /*
        $this->Paginator->settings = array(
            'Task'=>array(
                'contain'=>$contain,
                'limit'=>10,
                'conditions'=>$conditions,
                'order'=>$order,
        ));
*/
        $nextMeeting = $this->getNextOpsMeeting();
        
        $tasks = $this->find('all', array(
            'conditions'=>$conditions,
            'order'=>$order,
            'contain' => $contain,
            'limit' => $limit,
        ));

        return array('utasks'=> $tasks, 'nextMeeting'=>$nextMeeting);
    }

    public function digestByTeam($team){
        // Next Ops Meeting
        // New Tasks Team where team_id = $team
        $new_roles = $this->TasksTeam->getRecentByTeam($team);
        // Ending/Due Soon
        
        
        
        
        
        
        
        
        
        
        $order = array();
        $conditions = array();

        //$settings = $this->Session->read('Auth.User.Compile');
            
        // Enforced for Urgent
        $settings['view_type']= 399;
        $settings['start_time'] = null;
        $settings['end_time'] = null;
        $settings['Teams'] = array($team);

        // Process settings, set defaults if necessary        
        $cc = $this->makeCompileConditions($settings);
        
        //$this->log($cc);
        
        $teams = $cc['teams'];
        $conditions = $cc['conditions'];
        $order = $cc['order'];
        $contain = $cc['contain'];
        $limit = $cc['limit'];
       /*
        $this->Paginator->settings = array(
            'Task'=>array(
                'contain'=>$contain,
                'limit'=>10,
                'conditions'=>$conditions,
                'order'=>$order,
        ));
*/
        $nextMeeting = $this->getNextOpsMeeting();
        
        $tasks = $this->find('all', array(
            'conditions'=>$conditions,
            'order'=>$order,
            'contain' => $contain,
            'limit' => $limit,
        ));

        return array('utasks'=> $tasks, 'nextMeeting'=>$nextMeeting);
    }


    // 2016
    // Required: Description contains "Ops Meeting", Type = "Meeting", has Actionable type
    public function getNextOpsMeeting(){
        $rs = $this->find('first', array(
            'conditions'=>array(
                'Task.actionable_type_id !=' => null,
                'Task.task_type_id' => 3,
                'Task.short_description LIKE' => "%Ops Meeting%",
                'Task.start_time >'=> date('Y-m-d'),
            ),
            'contain'=>array('TasksTeam')
        ));
        
        return $rs;
    }

    public function getDigestDataByTeam($team){
        $tids = $this->TasksTeam->getRecentByTeam($team);
        $recent_children = $this->Change->getRecentChildrenByTeam($team);
        
        $recent = $this->find('all', array(
            'conditions'=>array(
                'Task.id'=>$tids
            ),
            'contain'=>$this->stdContain,
        ));
        
        $new_children = $this->find('all', array(
            'conditions'=>array(
                'Task.id'=>$recent_children,
            ),
            'contain'=>$this->stdContain,
        ));
        
        return array(
            'team_code'=>$this->Team->getTeamCodeByTeamId($team),
            'next_meeting'=> $this->getNextOpsMeeting(),
            'recent_requests'=> $recent,
            'recent_links'=> $new_children
        );
    }
    

    public function sendDigestToUser($user, $data=array(), $updateLastDigest = true){
        $monday = strtotime('last monday', strtotime('tomorrow'));
        $wk_start = date('F dS', $monday);
        $email = $user['email'];
        $tcode = $user['team_code'];
        $ename = Configure::read('EventShortName');

        if(!empty($email)){
            $Email = new CakeEmail('gmail');
            $Email->from(array('DBOpsCompiler@gmail.com' => 'DBOps Compiler'));
            $Email->to($email);
            $Email->replyTo('DBOpsCompiler@gmail.com');
            $Email->subject($ename.'-Compiler '.$tcode.' Updates (Wk of '.date('M dS',$monday).')');
            $Email->template('digest')
                ->emailFormat('html');
                //->emailFormat('both');
            $Email->viewVars(array(
                'team_code' => $data['team_code'],
                'recent_links'=>$data['recent_links'],
                'recent_requests'=>$data['recent_requests'],
                'next_meeting'=>$data['next_meeting'],
                )
            );    
            if($Email->send()){
                if($updateLastDigest){
                    $this->TasksTeam->Team->TeamsUser->User->updateLastDigestByUser($user['id']);
                }
                return array('success'=>true, 'email'=>$email);
            }
            else{
                return array('success'=>false, 'email'=>$email);
            }
        }
        return false;
    }

/*

    public function sendDigestToUser_old($user, $data=array()){

        
        $usr = $this->Team->TeamsUser->User->findById($user);
        $monday = strtotime('last monday', strtotime('tomorrow'));
        $wk_start = date('F dS', $monday);
        //$uid = $usr['User']['id'];
        $email = $usr['User']['email'];

        $ename = Configure::read('EventShortName');
        
        //$this->log($email);
        if(!empty($email)){
            $Email = new CakeEmail('gmail');
            $Email->from(array('DBOpsCompiler@gmail.com' => 'DBOps Compiler'));
            $Email->to($email);
            $Email->replyTo('DBOpsCompiler@gmail.com');
            $Email->subject($ename.'-Compiler Updates (Wk of '.date('M dS',$monday).')');
            $Email->template('digest')
                ->emailFormat('html');
                //->emailFormat('both');
            $Email->viewVars(array(
                'team_code' => $data['team_code'],
                'recent_links'=>$data['recent_links'],
                'recent_requests'=>$data['recent_requests'],
                'next_meeting'=>$data['next_meeting'],
                )
            );    
            if($Email->send()){
                return $email;
            }
            else{return 'ERROR: Email was not sent.';}
        }
        
        return false;
    }
*/


    public function updateModifiedDate($task){
        $now = date('Y-m-d H:i:s');

        $this->id = $task;
        if($this->saveField('modified', $now)){
            return true;
        }
        return false;
    }













// EOF
}

