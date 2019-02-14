<?php
App::uses('AppModel', 'Model');

class Task extends AppModel {
    
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'short_description';
	
    public $stdTaskFields = array('id', 'start_time', 'end_time', 'short_description', 'task_type', 'team_code', 'task_color_code', 'time_control', 'time_offset', 'due_date');
    
    public $stdFields = array(
        'Task.id', 'Task.start_time', 'Task.end_time', 'Task.task_type','Task.short_description', 
        'Task.team_code', 'Task.task_color_code', 'Task.time_control', 'Task.time_offset', 'Task.time_offset_type');
    
    public $stdContain = array(
        'Assignment'=>array(
            'fields'=>array(
                'Assignment.id', 'Assignment.role_id'),
        ),
        'Assist'=>array(
            'fields'=>array(
                'Assist.id', 'Assist.start_time', 'Assist.end_time', 'Assist.short_description', 'Assist.task_type',
                'Assist.team_code', 'Assist.task_color_code', 'Assist.time_control', 'Assist.time_offset', 'Assist.time_offset_type')
        ),
        'Comment',
        //'Assist.Assist',
        'Parent'=>array(
            'fields'=>array(
                'Parent.id', 'Parent.parent_id', 'Parent.start_time', 'Parent.end_time', 'Parent.short_description',
                'Parent.task_type', 'Parent.team_code', 'Parent.task_color_code', 'Parent.time_control', 'Parent.time_offset', 'Parent.time_offset_type')
        ),
        'TasksTeam'=>array(
            'fields'=>array(
                'TasksTeam.team_id', 'TasksTeam.team_code', 'TasksTeam.task_role_id')
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
        ),
    );
 
    // Need to use the constructor because we use model aliases for Task (i.e. Parent/Contribution)
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['task_type'] = sprintf('SELECT `TaskType`.`name` from `task_types` as `TaskType` WHERE `TaskType`.`id` = %s.task_type_id', $this->alias);
        $this->virtualFields['task_color_code'] = sprintf('SELECT `TaskColor`.`code` from `task_colors` as `TaskColor` WHERE `TaskColor`.`id` = %s.task_color_id', $this->alias);
        $this->virtualFields['actionable_type'] = sprintf('SELECT `ActionableType`.`name` from `actionable_types` as `ActionableType` WHERE `ActionableType`.`id` = %s.actionable_type_id', $this->alias);
        $this->virtualFields['team_code'] = sprintf('SELECT `Team`.`code` from `teams` as `Team` WHERE `Team`.`id` = %s.team_id', $this->alias);
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
        'Assignment'=>array(
            'className' => 'Assignment',
            'foreignKey' => 'task_id',
            'order'=>array(
                'Assignment.id'=>'ASC'
            ),
            'dependent' => true,
        ),	   
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
        /*'NewChange' => array(
            'className' => 'Change',
            'foreignKey' => 'task_id',
            'dependent' => true,
        ),*/
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
	);

 /**
 * Checks if a task is controlled by the given user
 * If task's lead (Task.team_id) is in User's team list, they're allowed
 * Used in TaskController->isAuthorized()
 * 
 * @param string $team_id Team to check control of
 * @param string $user User info from $Auth->User
 * @return boolen
 **/
    public function isControlledBy($task_id, $user){
        $task_owner = $this->field('team_id', array('id' => $task_id)); 
        $user_teams = $user['Teams'];
        
        if(in_array($task_owner, $user_teams)){
            return true;
        }
        return false;
    }
    
//** 2015 **: Used to prevent saving changes for deleted tasks. Task.afterDelete() cascades to TasksTeam, this checks if task is deleted before allowing changes to be saved
    public function isDeleted($task_id){
        $rs = $this->findById($task_id);

        if($rs[$this->alias]['is_deleted'] == 1){
            return true;
        }
        return false;
    }
    
/************************************************
 * Validation Functions. Used in Model->$validate
 ************************************************/  
    // Ensures End >= Start
    public function validateEndAfterStart($check){
        $end = $check['end_time'];
        $start = $this->data[$this->alias]['start_time'];
        
        if((strtotime($end) < (strtotime($start)))){
            return false;
        }
        return true;
    }

    // **2016** Checks if $task is in $parent's chain of parent_ids. Used to prevent loops when tasks are syncing to each other
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
        if(!$parent || !$task){
            return false; 
        }
        if($task == $parent){
            return true; 
        }
        
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
        //$this->log("BeforeSave in Task");
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
            //$this->log('Swapped start and end time in Task::beforeSave');
        }
        
        // Format Offset for saving, only if time controlled
        $t_ctrl = (isset($this->data[$this->alias]['time_control']))? $this->data[$this->alias]['time_control']:0;
            
        if($t_ctrl == 1){
            $o_min = isset($this->data['Offset']['minutes'])? (int)$this->data['Offset']['minutes']: 0;
            $o_sec = isset($this->data['Offset']['seconds'])? (int)$this->data['Offset']['seconds']: 0;
            //$o_type = isset($this->data[$this->alias]['time_control_type'])? $this->data[$this->alias]['time_control_type']:0;
            
            $new_to = 60*$o_min + $o_sec;
            
            $this->data[$this->alias]['time_offset'] = $new_to;
            //$this->data[$this->alias]['time_offset_type'] = $o_type;
        }
        else{
            // Offset requires task to be time controlled
            if(!empty($this->data['Offset']['seconds']) || !empty($this->data['Offset']['minutes'])){
                $this->data[$this->alias]['time_offset'] = 0;
                $this->data[$this->alias]['time_offset_type'] = 0;
            }
        }

/* 
        $data_pid = (isset($this->data[$this->alias]['parent_id']))? $this->data[$this->alias]['parent_id'] : null; 
        $data_tc = (isset($this->data[$this->alias]['time_control']))? $this->data[$this->alias]['time_control'] : 0;
        $data_to = (isset($this->data[$this->alias]['time_offset']))? $this->data[$this->alias]['time_offset'] : 0;
        $data_to_type = (isset($this->data[$this->alias]['time_offset_type']))? $this->data[$this->alias]['time_offset_type'] : 0;

        
        $iDurr = 0;

        if((isset($this->data[$this->alias]['end_time'])) && (isset($this->data[$this->alias]['start_time']))){
            $iDurr = (strtotime($this->data[$this->alias]['end_time']) - strtotime($this->data[$this->alias]['start_time']));            
        }


        // Ensure linked start times for time controlled tasks
        if(isset($data_pid) && ($data_tc == 1)){
            $pstart = $this->getStartTimeByTask($data_pid);
            $pend = $this->getEndTimeByTask($data_pid);
            //$tstart = $this->data[$this->alias]['start_time'];
            
            // 2018: 4 cases when there is an offset to account for:
         
            if($new_to > 0){
                if($o_type == -1){          //Before linked task starts
                    $test_tstart = date('Y-m-d H:i:s', strtotime($pstart)-$data_to);
                    $test_tend = date('Y-m-d H:i:s', strtotime($pstart)-$data_to+$iDurr);
                }
                elseif ($o_type == -2){     //Before linked task ends
                    $test_tstart = date('Y-m-d H:i:s', strtotime($pend)-$data_to);
                    $test_tend = date('Y-m-d H:i:s', strtotime($pend)-$data_to+$iDurr);
                }
                elseif ($o_type == 1){      //After linked task starts
                    $test_tstart = date('Y-m-d H:i:s', strtotime($pstart)+$data_to);
                    $test_tend = date('Y-m-d H:i:s', strtotime($pstart)+$data_to+$iDurr);
                }
                elseif ($o_type == 2){      //After linked task ends
                    $test_tstart = date('Y-m-d H:i:s', strtotime($pend)+$data_to);
                    $test_tend = date('Y-m-d H:i:s', strtotime($pend)+$data_to+$iDurr);
                }
            }
            else{   //Time controlled, but synced (offset == 0)
                $test_tstart = date('Y-m-d H:i:s', strtotime($pend)+abs($data_to));
                $test_tend = date('Y-m-d H:i:s', strtotime($pend)+abs($data_to)+$iDurr);
            }
 
            
        }
*/
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
            
            $assigns_before = $this->Assignment->getRolesByTask($this->id);
            
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
        $this->old_assignments = (!empty($assigns_before))? $assigns_before: array();
        
        return true;
    }
    
    public function afterSave($created, $options = array()){
        //$this->log($this->data);
        
        $id_saved = $this->id;
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
        }//End TeamRoles
        
        /** Assignments **/
        //Unsetting an existing assignment
        if(empty($this->data['Assignments'])){
            $this->Assignment->deleteAllByTask($this->id);
        }
        
        //Compare new to old assignments and process changes
        if(!empty($this->data['Task']['Assignments'])){
            $new_ass = (is_array($this->data['Task']['Assignments']))? $this->data['Task']['Assignments'] : array();
            $old_ass = $this->Assignment->getByTask($this->id);
            $tba_ass = array_diff($new_ass, $old_ass);
            $tbd_ass = array_diff($old_ass, $new_ass);
            
            foreach ($tba_ass as $addass){
                $this->Assignment->setByTaskAndRole($this->id, $addass);
            }

            foreach ($tbd_ass as $delass){
                $this->Assignment->deleteByTaskAndRole($this->id, $delass);
            }
        }
        
        // Process changes in Task only if it was a record update    
        if($created == false){
            $after = $this->findById($this->id);

            // Lead Team
            if($after['Task']['team_id'] != $before['Task']['team_id']){
                // Record change of lead in TasksTeam & Change
                $this->TasksTeam->changeLeadTeam($this->id, $after['Task']['team_id']);
                $this->Change->changeLeadTeam($this->id, $before['Task']['team_id'], $after['Task']['team_id']);
                
                //If lead team changes, delete all old assignments (specific to lead team)
                $this->Assignment->deleteAllByTask($after['Task']['id']);
            }
            // Start or End Time - record Change and change start/end times of all child tasks that are synchronized to this 
            if ($after['Task']['start_time'] != $before['Task']['start_time'] || $after['Task']['end_time'] != $before['Task']['end_time']){
                $this->Change->changeStartTime($this->id, $before['Task']['start_time'], $after['Task']['start_time']);
                $this->changeChildStartEndTime($this->id);
            }
            // Description - compare text and only record the change if >5% difference
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
            if(!empty($after['Task']['team_id'])){
                $this->TasksTeam->addTeam($this->id, $after['Task']['team_id'], 1);
            }

            //Record Due Date
            if(!empty($after['Task']['due_date'])){
                $this->Change->changeDueDate($this->id, null, $after['Task']['due_date']);
            }

            //Record Actionable Status
            if(!empty($after['Task']['actionable_type_id'])){
               $this->Change->changeActionableStatus($this->id, null, $after['Task']['actionable_type_id']);
            }
            
            //Record Link to Parent in Parent & Child
            if(!empty($after['Task']['parent_id'])){
                $this->Change->newChild($after['Task']['parent_id'], $this->id);
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
            array($this->alias.'.team_id'=>$team),
            true,
            true
        );
        
        return true;       
    }
    
    // 2016: Unsets parent_id, time_offset, time_control
    // Used in Task::afterDelete
    public function unsetChildrenByParentId($parent_task){
        if(!$parent_task){
            return false;
        }
            
        $rs = $this->find('all', array(
            'conditions'=>array('parent_id'=>$parent_task),
            'fields'=>array('id')
        ));
        
        if(!empty($rs)){
            $tasks = Hash::extract($rs, '{n}.Task.id');    
        
            $this->updateAll(
                array(
                    'parent_id'=>null,
                    'time_offset'=>null,
                    'time_control'=>0
                ),
                array(
                    $this->alias.'.id'=>$tasks
                )
            );
        }
        return true;
    }

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
                array($this->alias.'.id'=>$task_ids)
            )){
                foreach($task_ids as $k=>$task_id){
                    $this->Change->parentDisconnected($task_id, $parent_task);
                }
            }
        }
        return true;    
    }
    
/*********************
 * GETTERS
 *********************/
 
    // Used in Task AfterSave
    public function getLeadByTask($task){
        if(!$this->exists($task)){
            return false;
        }
        return $this->field('team_id', array($this->alias.'.id'=>$task));
    }

    public function getLeadCodeByTask($task){
        if(!$this->exists($task)){
            return false;
        }
        return $this->field('team_code', array($this->alias.'.id'=>$task));
    }

    public function getStartTimeByTask($task){
        if(!$this->exists($task)){
            return false;
        }
        return $this->field('start_time', array($this->alias.'.id'=>$task));
    }

    public function getEndTimeByTask($task){
        if(!$this->exists($task)){
            return false;
        }
        return $this->field('end_time', array($this->alias.'.id'=>$task));
    }

    public function getShortDescByTask($task){
        if(!$this->exists($task)){
            return false;
        }
        return $this->field('short_description', array($this->alias.'.id'=>$task));
    }    

    public function getTimeControlByTask($task){
        if(!$this->exists($task)){
            return false;
        }
        return $this->field('time_control', array($this->alias.'.id'=>$task));
    }    

    public function getOpenRequestsByTeam($team){
        $owa = date('Y-m-d', strtotime('-1 week'));
        $tids = $this->TasksTeam->getByTeamsAndRoles($team, array(3));
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
                    'fields'=>array('Change.created')
                ),
            ),
            'fields'=>array(
                'Task.priority_date',
                'Task.id',
                'Task.short_description',
                'Task.start_time',
                'Task.end_time',
                'Task.due_date',
                'Task.task_type',
            ),
            'order'=>array(
                'Task.priority_date'=> 'ASC'),
        ));
        
        return $rs;
    }

    public function getOpenWaitingByTeam($team){
        $owa = date('Y-m-d', strtotime('-1 week'));
        $tids = $this->TasksTeam->getOpenWaitingByTeam($team);
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
                    'fields'=>array('Change.created')
                ),
                'Assist'=>array(
                    'fields'=>$this->stdTaskFields
                ),
                
            ),
            'order'=>'Task.priority_date ASC',
        ));
        
        return $rs;
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
            'order'=>'Task.start_time ASC',
            'fields'=>$this->stdTaskFields,
            'contain'=>array(
                'TasksTeam'=>array(
                    'fields'=>array('team_id', 'task_role_id', 'team_code')
                )
            )
        ));
        
        return $rs;
    }
    
    // 2015                
    public function getDigestDataCountByTeams($teams=array()){
        $data = array();
        $next_meeting = $this->getNextOpsMeeting();

        foreach ($teams as $k => $team){
            $req_ids = $this->TasksTeam->getRecentByTeam($team);
            $link_ids = $this->Change->getRecentChildrenByTeam($team);
            
            $recent_requests = $this->find('count', array('conditions'=>array('Task.id'=>$req_ids)));
            $recent_links = $this->find('count', array('conditions'=>array('Task.id'=>$link_ids)));
            
            $data[$team]['next_meeting'] = ($next_meeting)? 1: 0;    
            $data[$team]['recent_requests'] = $recent_requests;
            $data[$team]['recent_links'] = $recent_links;
        }
        return $data;
    }

    public function getDigestDataByTeam($team){
        $tids = $this->TasksTeam->getRecentByTeam($team);
        $recent_children = $this->Change->getRecentChildrenByTeam($team);
        $next_ops_meeting = $this->getNextOpsMeeting();
        $urgent_tasks = $this->getUrgentByTeam($team);
        
        $recent_requests = $this->find('all', array(
            'conditions'=>array(
                'Task.id'=>$tids,
                'Task.start_time >=' =>Configure::read('CompileStart'),
                'Task.end_time <=' =>Configure::read('CompileEnd'),
            ),
            'fields'=> $this->stdTaskFields,
        ));
        
        $recent_links = $this->find('all', array(
            'conditions'=>array(
                'Task.id'=>$recent_children,
                'Task.start_time >=' =>Configure::read('CompileStart'),
                'Task.end_time <=' =>Configure::read('CompileEnd'),
            ),
            'fields'=> $this->stdTaskFields,
        ));
        
        $count_next_meeting = ($next_ops_meeting)? 1:0;
        $count_recent_links = count($recent_links);
        $count_recent_requests = count($recent_requests);
        $count_urgent = count($urgent_tasks);
        
        return array(
            'Team'=>array(
                'id'=>$team,
                'team_code'=>$this->Team->getTeamCodeByTeamId($team),
            ),
            'Counts'=>array(
                'next_meeting'=> $count_next_meeting,
                'recent_links'=> $count_recent_links,
                'recent_requests'=> $count_recent_requests,
                'urgent_tasks'=>$count_urgent,
            ),
            'next_meeting'=> $next_ops_meeting,
            'recent_requests'=> $recent_requests,
            'recent_links'=> $recent_links,
            'urgent_tasks'=>$urgent_tasks,
            
        );
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
        return CakeSession::write('Auth.User.TimeShift', $data);
    }
 
    public function readTimeshift() {
        return CakeSession::read('Auth.User.TimeShift');
    }

    public function userTimeshift() {
        $cts = CakeSession::read('Auth.User.Timeshift');

        $rs = $this->find('all', array(
            'conditions'=>array('Task.id' => $cts),
            'fields'=>array('Task.id', 'Task.start_time', 'Task.end_time', 'Task.short_description'),
            'order'=>'Task.start_time ASC',
         ));
         return $rs;
    }

    //Set task's modified date to right now
    //2018 - commented out in TasksTeam -- would generate many unnecessary updates there.
    public function setModifiedDateNow($task){
        if($this->exists($task)){
            $this->id = $task;
            if($this->saveField('modified', date('Y-m-d H:i:s'))){
                return true;
            }
        }
        
        return false;
    }

    // Process from submitted settings.  Set defaults if necessary.
    public function makeSafeCompileSettings($raw=array()){
        $teams = (!empty($raw['Teams']))? $raw['Teams']: array();
        $sdate = (isset($raw['start_date']))? $raw['start_date']: null;
        $edate = (isset($raw['end_date']))? $raw['end_date']: null; 
        $view_type = (isset($raw['view_type']))? (int)$raw['view_type']: 1;
        $sort = (isset($raw['sort']))? (int)$raw['sort']: 0;
        $timeline_hr = (isset($raw['timeline_hr']))? $raw['timeline_hr']:0;
        //$view_details = (isset($raw['view_details']))? (int)$raw['view_details']: 1;
        //$view_threaded = (isset($raw['view_threaded']))? (int)$raw['view_threaded']: 1;
        //$view_threaded = $view_links = 1;
        //$view_links = (isset($raw['view_links']))? (int)$raw['view_links']: 1;
        //$fields = (isset($raw['fields']))? $raw['fields']: array();
        
        // DEFAULT: current compile range
        if(!$sdate || !$edate){
            $sdate = Configure::read('CompileStart');
            $edate = Configure::read('CompileEnd');
        }
        
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
            'timeline_hr'=>$timeline_hr,

            //'fields'=>$fields,
            //'view_details'=>$view_details,
            //'view_links'=>$view_links,
            //'view_threaded'=>$view_threaded,
        );

        return $clean;
    }

    // Used in TasksController->timeShift()
    public function incrementTaskTime($tasks = array(), $increment = null){
        if(!$tasks || !$increment) {
            return false;
        }
        foreach($tasks as $tid){
            $this->id = $tid;

            $rs = $this->read(null, $this->id);
            $cur_s = strtotime($rs['Task']['start_time']);
            $cur_e = strtotime($rs['Task']['end_time']);

            //$new_s = date('Y-m-d H:i:s', ($cur_s + $increment));
            //$new_e = date('Y-m-d H:i:s', ($cur_e + $increment));

            $data = array(
                'id'=>$tid,
                'start_time' => date('Y-m-d H:i:s', ($cur_s + $increment)),
                'end_time' => date('Y-m-d H:i:s', ($cur_e + $increment)),
            );
            
            $this->save($data);
        }
        return true;
    }
    
    //2016
    public function makeLinkableParentsList($team){
        $tids = $this->TasksTeam->getLinkableParentsByTeam($team);
        $cstart = Configure::read('CompileStart');
        $cend = Configure::read('CompileEnd');

        $rs = $this->find('all', array(
            'conditions'=>array(
                'Task.id'=>$tids,
                'Task.start_time >='=>$cstart,
                'Task.end_time <='=>$cend,
                //'Task.TasksTeam.team_id'=>$team,
            ),
            'order'=>array(
                'Task.team_code ASC',
                'Task.start_time ASC'),
            'fields'=>array(
                'Task.id', 
                'Task.short_description',
                'Task.task_type',  
                'Task.team_code', 
                'Task.start_time',
                'Task.end_time', 
                'Task.team_id')
        ));
            
        $result = Hash::combine($rs, '{n}.Task.id', '{n}.Task', '{n}.Task.team_code');
        return $result;
    }

    
    // 2016: Used in Task::afterSave()
    // If start or end time of a task changes and it has child tasks linked that are time controlled, alter start/end time
    public function changeChildStartEndTime($parent_tid){
        $this->id = $parent_tid;
        $parent_start = $this->field('start_time');
        $parent_end = $this->field('end_time');
        $par_start = strtotime($parent_start);
        $par_end = strtotime($parent_end);
        
        // Find all time controlled tasks
        $rs = $this->find('all', array(
            'conditions'=>array(
                $this->alias.'.parent_id'=>$parent_tid,
                $this->alias.'.time_control'=>1),
            'fields'=>array(
                $this->alias.'.id', 
                $this->alias.'.start_time', 
                $this->alias.'.end_time', 
                $this->alias.'.time_offset',
                $this->alias.'.time_offset_type')
        ));
        
        // Loop over time controlled tasks, saving new start/end
        foreach($rs as $task){
            $ctask = $task['Task']['id'];
            $start = strtotime($task['Task']['start_time']);
            $end = strtotime($task['Task']['end_time']);
            $offset = (isset($task['Task']['time_offset']))? (int)$task['Task']['time_offset']: 0;
            $offset_type = (isset($task['Task']['time_offset_type']))? (int)$task['Task']['time_offset_type']: 0;
            $task_duration = $end - $start;

            if($offset_type == -1  || $offset_type == 0){ // Starts BEFORE START of linked task
                $new_start = date('Y-m-d H:i:s', ($par_start-$offset));
                $new_end = date('Y-m-d H:i:s', ($par_start-$offset+$task_duration));
            }
            elseif ($offset_type == -2) { // Starts BEFORE END of linked task
                $new_start = date('Y-m-d H:i:s', ($par_end-$offset));
                $new_end = date('Y-m-d H:i:s', ($par_end-$offset+$task_duration));
            }
            elseif ($offset_type == 1) { // Starts AFTER START of linked task
                $new_start = date('Y-m-d H:i:s', ($par_start+$offset));
                $new_end = date('Y-m-d H:i:s', ($par_start+$offset+$task_duration));
            }
            elseif ($offset_type == 2) { // Starts AFTER END of linked task
                $new_start = date('Y-m-d H:i:s', ($par_end+$offset));
                $new_end = date('Y-m-d H:i:s', ($par_end+$offset+$task_duration));
            }
            
            $this->id = $ctask;
            $this->saveField('start_time', $new_start);
            $this->id = $ctask;
            $this->saveField('end_time', $new_end);
            
            // Record change -- task was moved because parent task moved
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
                        'data-etime' => $task['end_time'],
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
            $m = 0;
            $s = 0;    
        }
        elseif($seconds>0){
            $getMins = floor($seconds/60);
            $getSecs = floor($seconds % 60);
            $m = $getMins;
            $s = $getSecs;
        }
        elseif($seconds<0){
            $getMins = floor(abs($seconds)/60);
            $getSecs = floor(abs($seconds) % 60);
            $m = $getMins;
            $s = $getSecs;
            }
        return array('min'=>$m, 'sec'=>$s);
    }    
    

    //2015
    public function makeCompileConditions($settings=array()){
        //debug($settings);
        
        $now = date('Y-m-d');
        $owa = date('Y-m-d', strtotime("-1 weeks"));
        $twa = date('Y-m-d', strtotime("-2 weeks"));
        $twfn = date('Y-m-d', strtotime("+2 weeks"));
        $compileStart = Configure::read('CompileStart');
        $compileEnd = Configure::read('CompileEnd');
            
        $teams = isset($settings['Teams'])? $settings['Teams']: array();
        $order = isset($settings['order'])? $settings['order']: array();
        $sdate = isset($settings['start_date'])? $settings['start_date']: $compileStart;
        $edate = isset($settings['end_date'])? $settings['end_date']: $compileEnd;

        $sort = isset($settings['sort'])? (int)$settings['sort']: 0;
        $view_type = (isset($settings['view_type']))? (int)$settings['view_type']: 1;
        $timeline_hr = isset($settings['timeline_hr'])? $settings['timeline_hr']: 6;
        $tlsdate = isset($settings['tl_start_date'])? $settings['tl_start_date']: $compileStart;
        $tledate = isset($settings['tl_end_date'])? $settings['tl_end_date']: $compileEnd;
        
        $fields = (isset($settings['fields']))? $settings['fields']: array();

        
        //debug($timeline_hr);
        //debug($view_type);
        
        // Conditions Initialization
        $conditions = array();
        $limit = 25;
        $contain = array();
        //$roles = array(1, 2, 3, 4);
        $roles = array();        
        $order = ($sort)? 'Task.start_time DESC':'Task.start_time ASC';

        // Conditions
        // Dates -- 1s less than a full day to capture all tasks on edate
        if(!empty($sdate) && !empty($edate)){
            $conditions['AND'][]= array(
                'Task.start_time <= ' => date('Y-m-d H:i:s', strtotime($edate)+86399),
                'Task.start_time >= ' => date('Y-m-d H:i:s', strtotime($sdate))
            );
        }
        
    /*******************************
     * View Types
     * 1: Rundown
     * 2: Timeline
     * 10: Lead Only
     * 30: Incoming Open Request
     * 31: Outgoing Open Request
     * 100: Recent
     * 500: Action Items
     ******************************/
        $useSubquery = false;
        
        // Rundown
        if($view_type == 1){
            //$roles = array(1,2,3,4);
            $useSubquery = true;
        }

        // Hourly Event Timeline
        if($view_type == 2){
            $limit = 500;
            $roles = array(1);

            //******************* TEMP *********************
            $date_event= Configure::read('EventDate');
            //$date_event= "2019-02-09";
            //**********************************************
            
            $useSubquery = false;
            
            $time_s = date('Y-m-d H:i:s', strtotime($date_event)+$timeline_hr*60*60);
            $time_e = date('Y-m-d H:i:s', strtotime($time_s) + (59*60)+59);
            
            //$order = $conditions = array();
            
            if($teams){
                $conditions['AND'] = array('Task.team_id'=>$teams);
            }
            $conditions['OR'] = array(
                array(  //Starts during, ends after
                    'Task.start_time >='=> $time_s,
                    'Task.start_time <='=> $time_e,
                    'Task.end_time >'=> $time_e,               
                ),
                array( //Starts before, ends during
                    'Task.start_time <'=> $time_s,
                    'Task.end_time >'=> $time_s,
                    'Task.end_time <='=> $time_e,   
                ),
                array( //Starts and ends during
                    'Task.start_time >='=> $time_s,
                    'Task.start_time <='=> $time_e,
                    'Task.end_time >='=> $time_s,
                    'Task.end_time <='=> $time_e,   
                ),
                array(  //Start before, ends after
                    'Task.start_time <'=> $time_s,
                    'Task.end_time >'=> $time_e,               
                ),
            );
            
            $contain = array(
                'Assignment'=>array(
                    'fields'=>array(
                        'role_handle',
                    )
                )
            );
            
            $order = 'Task.start_time ASC';
            //$fields = array('Task.id', 'Task.team_code', 'Task.task_type', 'Task.start_time', 'Task.end_time', 'Task.short_description');
        }
        
        // Lead Only
        if($view_type == 10){
            $roles = array(1);
            $useSubquery = true;
        }
        
        // Incoming Open Requests
        if($view_type == 30){
            $roles = array(3);
            //$conditions['AND'] = array();
            $conditions['AND'] = array(
                'Task.start_time >=' =>$compileStart,
                'Task.start_time <=' =>$compileEnd,
            );
            $useSubquery = true;
        }

        // Outgoing Open Requests
        if($view_type == 31){
            $useSubquery = false; 
            $conditions['AND'] = array(
                'Task.id' => $this->TasksTeam->getOpenWaitingByTeam($teams),
                'Task.start_time >=' => $compileStart,
                'Task.start_time <=' => $compileEnd,
            );
        }
        
        // Recently Created
        if($view_type == 100){
            $conditions['AND'] = array(
                'Task.start_time >=' =>$compileStart,
                'Task.start_time <=' =>$compileEnd,
            );
            $order = 'Task.modified DESC';
            $roles = array(1, 2, 3, 4);
            $useSubquery = true;
        }
        // Assisting & Due Soon
        if($view_type == 399){
            $roles = array(3);
            $conditions['AND'] = array(
                'OR' => array(
                    array(
                        'AND' => array(
                            array('Task.due_date >' => $now),
                            array('Task.due_date <' => $twfn))),
                    array(
                        'AND' => array(
                            array('Task.end_time >' => $now),
                            array('Task.end_time <' => $twfn)))
                ));
            $order = 'Task.end_time ASC';
            $useSubquery = true;
        }        
        // Actionable.
        if($view_type == 500){
            $conditions['AND'] = array(
                'Task.actionable_type_id !='=>null,
                'Task.start_time >=' =>$compileStart,
                'Task.start_time <=' =>$compileEnd,
            );
            $order = 'Task.start_time ASC';
            $useSubquery = false;
        }

        if($useSubquery){
            // Subquery looking for tasks where $teams are listed in any of the given $roles
            
            if(!empty($teams)){
                $conditionsSubQuery['`TasksTeam`.`team_id`'] = $teams;
            }
            
            if(!empty($roles)){
                $conditionsSubQuery['`TasksTeam`.`task_role_id`'] = $roles;    
            }
            
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
        if(empty($contain)){
            $contain = array(
                'Assignment',
                'Assignment.Role',
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
                        'Assist.time_offset_type',
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
            }
        $cs = array(
            'teams'=>$teams,
            'start_date'=>$sdate,
            'end_date'=>$edate,
            'sort'=>$sort,
            'conditions'=>$conditions,
            'order'=>$order,
            'fields'=>$fields,
            'contain'=>$contain,
            'limit'=>$limit,
            'view_type'=>$view_type,
            'timeline_hr' => $timeline_hr,
            'timeline_start_date'=>$tlsdate,
            'timeline_end_date'=>$tledate,
        );
        
        return $cs;
    }

    public function getUrgentByTeam($team){
        $order = array();
        $conditions = array();

        // Enforced for Urgent
        $settings['view_type']= 399;
        $settings['start_time'] = null;
        $settings['end_time'] = null;
        $settings['Teams'] = array($team);

        // Process settings, set defaults if necessary        
        $cc = $this->makeCompileConditions($settings);
        $teams = $cc['teams'];
        $conditions = $cc['conditions'];
        $order = $cc['order'];
        $contain = $cc['contain'];
        $limit = $cc['limit'];

        $nextMeeting = $this->getNextOpsMeeting();
        
        $tasks = $this->find('all', array(
            'conditions'=>$conditions,
            'order'=>$order,
            'contain' => $contain,
            'limit' => $limit,
        ));

        return array(
        'utasks'=> $tasks, 
        'nextMeeting'=>$nextMeeting);
    }

    public function digestByTeam($team){
        $new_roles = $this->TasksTeam->getRecentByTeam($team);
        
        $order = array();
        $conditions = array();

        // Enforced for Urgent
        $settings['view_type']= 399;
        $settings['start_time'] = null;
        $settings['end_time'] = null;
        $settings['Teams'] = array($team);

        // Process settings, set defaults if necessary        
        $cc = $this->makeCompileConditions($settings);
        
        $teams = $cc['teams'];
        $conditions = $cc['conditions'];
        $order = $cc['order'];
        $contain = $cc['contain'];
        $limit = $cc['limit'];

        $nextMeeting = $this->getNextOpsMeeting();
        $tasks = $this->find('all', array(
            'conditions'=>$conditions,
            'order'=>$order,
            'contain' => $contain,
            'limit' => $limit,
        ));

        return array('utasks'=> $tasks, 'nextMeeting'=>$nextMeeting);
    }

/**
 * Sends digest to Users on a team who are subscribed.
 * Returns status and arrays of sent and failed emails.
 * NOTE: Cannot check for bounces from here, only PHP fails
 * @param $team_id int Team to send digest to
 * @return array
 * @since 2015  
 */
    public function sendDigestToTeam($team_id){
        $dusers = $this->Team->TeamsUser->getDigestUsersByTeam($team_id);
        
        $tdata = $this->getDigestDataByTeam($team_id);

        $success = true;
        $sent = $failed = array();
        foreach ($dusers[$team_id] as $k => $user){
            $response = $this->sendDigestToUser($user, $tdata, true);
            
            if($response['success'] == true){
                $sent[] = $response['email'];
            }
            else{
                $failed[] = $response['email'];
                $success = false;
            }
        }

        return array('success'=>$success, 'sent'=>$sent, 'failed'=>$failed);
    }
/**
 * Sends digest to a single user
 * @param $user {Array} User data (email, team code)
 * @param $data {Array} Data to be sent in email
 * @param $updateLastDigest {bool} Whether to update the User.last_digest field. Default=true;
 * @since 2015  
 */
    public function sendDigestToUser($user, $data=array(), $updateLastDigest = true){
        $monday = strtotime('last monday', strtotime('tomorrow'));
        $wk_start = date('F jS', $monday);
        $email = $user['email'];
        $tcode = $user['team_code'];
        $ename = Configure::read('EventShortName');

        if(!empty($email)){
            $Email = new CakeEmail('gmail');
            $Email->from(array('DBOpsCompiler@gmail.com' => 'DBOps Compiler'));
            $Email->to($email);
            $Email->replyTo('DBOpsCompiler@gmail.com');
            $Email->subject($ename.'-Compiler '.$tcode.' Updates (Wk of '.date('M jS',$monday).')');
            $Email->template('digest')
                ->emailFormat('html');
                //->emailFormat('both');
            $Email->viewVars(array(
                'team_code' => $data['Team']['team_code'],
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




// EOF
}

