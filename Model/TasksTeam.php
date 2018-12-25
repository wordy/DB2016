<?php
App::uses('AppModel', 'Model');

 /**
 * TasksTeam Model
 *
 * @property Task $Task
 * @property Team $Team
 * @property TaskRole $TaskRole
 */
class TasksTeam extends AppModel {
	public $displayField = 'id';
    public $order = array('TasksTeam.task_role_id ASC', 'TasksTeam.team_code ASC');

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['team_code'] = sprintf('SELECT `Team`.`code` from `teams` as `Team` WHERE `Team`.`id` = %s.team_id', $this->alias);
    }

	public $belongsTo = array(
		'Task' => array(
			'className' => 'Task',
			'foreignKey' => 'task_id',
            'dependent' => true
		),
		'Team' => array(
			'className' => 'Team',
			'foreignKey' => 'team_id',
            'dependent' => true
		),
		'TaskRole' => array(
			'className' => 'TaskRole',
			'foreignKey' => 'task_role_id',
		)
	);
    
    public $validate = array(
        'task_id' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'You must specify a task',
                'allowEmpty' => false,
                'required' => true),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Expected a number')
        ),
        'team_id' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'You must specify a team',
                'allowEmpty' => false,
                'required' => true),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Expected a number')
        ),
        'task_role_id' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'You must specify a task role ID',
                'allowEmpty' => false,
                'required' => true),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Expected a number')
        ),
        
    );
    
    
    public function beforeSave($options=array()) {
        //$this->log($this->data);
        $team_id = ($this->data['TasksTeam']['team_id'])?: null;
        $task_role_id = ($this->data['TasksTeam']['task_role_id']) ?: null;
        $task_id = ($this->data['TasksTeam']['task_id'])?: null;
        
        // Duplicates. Also prevents saving if the team is already lead on the task
        if ($this->existsByTaskTeamRole($task_id, $team_id, $task_role_id) || $this->existsByTaskTeamRole($task_id, $team_id, 1)){
            //$this->log('prevented TT save because of duplicate or already lead');
            return false;
        }

        // Prevents saving multiple changes when cycling through roles.
        // TODO: Move to AfterSave
        $this->Task->Change->removeRecentRoleChangesByTeamAndTask($team_id, $task_id);
        
        // If role is CHANGING, find old role, and record change
        if($this->existsByTaskTeam($task_id, $team_id)){
            $rs = $this->findByTaskIdAndTeamId($task_id, $team_id);
            $old_role = $rs['TasksTeam']['task_role_id'];

            if(!empty($task_role_id)){
                $rs['TasksTeam']['task_role_id'] = $task_role_id;    
                // TODO: NO idea why the modified field does not update automagically
                $rs['TasksTeam']['modified'] = date('Y-m-d H:i:s');
                $this->data = $rs;
                $this->old_role = $old_role;
            }
        }
        return true;
    }
    
    public function afterSave($created, $options = array()){
        $team_id = $this->data['TasksTeam']['team_id'];
        $task_role_id = $this->data['TasksTeam']['task_role_id'];
        $task_id = $this->data['TasksTeam']['task_id'];

        // Trigger update of Task's modified date
        //$this->Task->updateLastModifiedDate($task_id);

        if($created==true){
            // If we created a new linkage, record Change in the Task
            if(!empty($task_id) && !empty($team_id) && !empty($task_role_id)){
                $this->Task->Change->addTeamToTask($task_id, $team_id, $task_role_id);
            }

            // Ensures only one lead per team
            if($task_role_id == 1){
                $this->deleteAll(array(
                    'TasksTeam.task_id'=> $task_id,
                    'TasksTeam.team_id !='=> $team_id,
                    'TasksTeam.task_role_id'=>1),
                true);
                
                //If team had other roles, delete them (relevant when xferring task ownership)
                $this->deleteAllNonLeadByTaskTeam($task_id, $team_id);
            }
        }
        // Not created, updated.
        else{
            $old_role = null;
            if(isset($this->old_role)){
                $old_role = $this->old_role;
            }
            $this->Task->Change->changeTeamRole($task_id, $team_id, $old_role, $task_role_id);
        }
        return true;
    }
    
    public function beforeDelete($cascade=true){
        $predelete = $this->findById($this->id);
        $this->predelete = $predelete;
        return true;
    }
    
    public function afterDelete(){
        $team_id = $this->predelete['TasksTeam']['team_id'];
        $task_role_id = $this->predelete['TasksTeam']['task_role_id'];
        $task_id = $this->predelete['TasksTeam']['task_id'];
        $has_role = $this->getTeamRoleByTask($team_id, $task_id);

        // This prevents saving a change when the task has already been deleted
        // Only remove if team has NO role (i.e. role wasn't just changed)
        if(!$this->Task->isDeleted($task_id) && !$has_role){
            $this->Task->Change->removeTeamRole($task_id, $team_id);    
        }
        
        // Team was removed. Unlink any tasks they may have already linked
        $this->Task->resetPidTcToByParentAndTeam($task_id, $team_id);
            
        return true;
    }
    
    // Updated 2015.  Generic adding function. Updates if team already had a role.
    public function addTeam($task, $team, $task_role_id){
        $old_role = $this->getTeamRoleByTask($team, $task);
        
        // Old Role == New role --> prevent duplicate
        if($old_role == $task_role_id){
            return false;
        }
        // Old role != new role. Save field & record changed role
        elseif (($old_role) && ($old_role != $task_role_id)){
            $rs = $this->findByTaskIdAndTeamId($task, $team);
            $rs['TasksTeam']['task_role_id'] = $task_role_id;
            unset($rs['TasksTeam']['created']);
            unset($rs['TasksTeam']['team_code']);
            if($this->save($rs)){
                return true;
            }
        }
        // New assignment
        elseif(!$old_role){
            $data = array(
                'task_id'=>$task,
                'team_id'=>$team,
                'task_role_id'=>$task_role_id,
            );
                
            $this->create();
            if($this->save($data)){
                return true;
            }
        }        
        return false;
    }
    
    public function existsByTaskTeamRole($task_id, $team_id, $task_role_id){
        $rs = $this->find('all',array(
            'conditions'=>array(
                'TasksTeam.task_id'=>$task_id,
                'TasksTeam.team_id'=>$team_id,
                'TasksTeam.task_role_id'=>$task_role_id
            )));
        return (!empty($rs))? true:false;
    }

    //2016 only one role allowed
    public function existsByTaskTeam($task_id, $team_id){
        $rs = $this->find('all',array(
            'conditions'=>array(
                'TasksTeam.task_id'=>$task_id,
                'TasksTeam.team_id'=>$team_id
            )));
        return (!empty($rs))? true:false;
    }

    // NOTE: compatible with >2 roles
    public function existsByTaskAndTeam($task_id, $team_id){
        $rs = $this->find('all', array(
            'conditions'=>array(
                'TasksTeam.task_id'=>$task_id,
                'TasksTeam.team_id'=>$team_id)));
                
        return (!empty($rs))? true:false; 
    }

    // 2016 Used in TasksTeam::beforeSave to ensure there's always >= 1 team lead per task
    public function getCountOfLeadTeamsByTask($task_id){
        $rs = $this->find('count',array(
            'conditions'=>array(
                'TasksTeam.task_id'=>$task_id,
                'TasksTeam.task_role_id'=>1
            )));
        return $rs;
    }

    public function getAllByTask($task_id){
        $rs = $this->find('all', array(
            'conditions'=>array(
                'TasksTeam.task_id'=>$task_id
            )));
        return $rs;
    }
    
    public function getLeadTeamByTask($task) {
        $rs = $this->findByTaskId($task);
        if(empty($rs)){
            return false;
        }
        return $rs['TasksTeam']['team_code'];
    }
    
    //2016
    public function getClosedAssistingByTask($task_id){
        if(!$task_id){
            return false;
        }
        
        $rs = $this->find('all', array(
            'conditions'=>array(
                'task_id'=>$task_id,
                'task_role_id'=>4,
            )
        
        ));
        
        $teams = Hash::extract($rs, '{n}.TasksTeam.team_id');
        
        if(!empty($teams)){
            return $teams;
        }
        else{return array();}
        
        
    }    
    
    public function getLinkableTeamsByTask($task_id){
        if(!$task_id){
            return false;
        }
        
        $rs = $this->find('all', array(
            'conditions'=>array(
                'task_id'=>$task_id,
                'task_role_id'=>array(1,2,3,4),
            )
        
        ));
        
        $teams = Hash::extract($rs, '{n}.TasksTeam.team_id');
        
        if(!empty($teams)){
            return $teams;
        }
        else{return array();}
    }
    
    public function getTaskIdsByTeamsAndRoles($teams, $roles=array(1,2,3,4)){
        $rs = $this->find('all',array(
            'conditions'=>array(
                'TasksTeam.team_id'=>$teams,
                'TasksTeam.task_role_id'=>$roles),
            'fields'=>array('TasksTeam.task_id')));
            
        $rs = Hash::extract($rs, '{n}.TasksTeam.task_id');
        
        return $rs;
    }
    
    // 2015
    /**
    * getTeamRoleByTask method
    * @param int $team team ID
    * @param int $task task ID
    * @return void
    */
    function getTeamRoleByTask($team, $task){
        $rs = $this->find('first', array(
            'conditions'=>array(
                'team_id'=>$team,
                'task_id'=>$task,
            )
        )); 
        
        if($rs){
            return $rs['TasksTeam']['task_role_id'];
        }

        return false;    
    }
    
    //2015:
    public function getLinkableParentsByTeam($team){
        if(!$team){
            return false;
        }
        
        $rs = $this->find('list', array(
            'conditions'=>array(
                'TasksTeam.team_id'=>$team,
                'TasksTeam.task_role_id'=>array(1,2,3,4)    
            ),
            'fields'=>array('TasksTeam.task_id')
        ));
        
        $linkable = Hash::extract($rs, '{n}');
        
        return $linkable;
    }
    
    public function getPossibleRolesByTask($lead = null, $task = null){
        $teams = $this->Team->listTeams();
        
        $allowTRoles[0] = array();
        $allowTRoles[1] = array();
        $allowTRoles[2] = array();
        $allowTRoles[3] = array();
        $allowTRoles[4] = array();
        
        if(!$task){
            foreach($teams as $tmid=>$tcode){
                if($lead == $tmid){
                    $allowTRoles[1][$tmid] = $tcode; 
                }
                else{
                    $allowTRoles[0][$tmid] = $tcode;
                }
            }
        }
        else{ // $task is defined
            $ttrs = $this->getAllByTask($task);
            $ateams = $teams;
            
            $troles = Hash::combine($ttrs, '{n}.TasksTeam.team_id', '{n}.TasksTeam.team_code', '{n}.TasksTeam.task_role_id');
            foreach ($troles as $rid =>$rteams){
                foreach($rteams as $tmid => $tcode){
                    $allowTRoles[$rid][$tmid] = $tcode;
                    unset($ateams[$tmid]);
                }
            }
            // remaining teams don't have a role        
            foreach($ateams as $tmid =>$tcode){
                $allowTRoles[0][$tmid] = $tcode;
            }
        }
        return $allowTRoles;
    }    

    public function getByTeamsAndRoles($teams, $roles=array(1,2,3,4)){
        $rs = $this->find('all',array(
            'conditions'=>array(
                'TasksTeam.team_id'=>$teams,
                'TasksTeam.task_role_id'=>$roles),
            'fields'=>array('TasksTeam.task_id'),
            'order'=>array('TasksTeam.task_id')));
            
        $rs = Hash::extract($rs, '{n}.TasksTeam.task_id');
        $rs = array_unique($rs);
        return $rs;
    }
    
    // 2016 - Used to find tasks, owned by $team, that still have outstanding open requests
    public function getOpenWaitingByTeam($team){
        $ttasks = $this->getByTeamsAndRoles($team, 1);
        
        $rs = $this->find('all', array(
            'conditions'=>array(
                'TasksTeam.task_id'=>$ttasks,
                'TasksTeam.task_role_id' => 3,
                'TasksTeam.team_id !='=>$team,
            ),
        ));

        $rs = Hash::extract($rs, '{n}.TasksTeam.task_id');
        $rs = array_unique($rs);
        return $rs;
    }

    // 2016 Recent Role Changes by Team
    public function getRecentByTeam($team, $limit = 5){
        $now = date('Y-m-d');
        $twa = date('Y-m-d', strtotime('-2 weeks'));
        //$ttasks = $this->getByTeamsAndRoles($team, 1);
        
        $rs = $this->find('all', array(
            'conditions'=> array(
                'TasksTeam.task_role_id' => 3,
                'TasksTeam.team_id'=>$team,
                'OR'=>array(
                    array(
                        'AND'=>array(
                            array('TasksTeam.modified >'=>date('Y-m-d H:i:s', strtotime($twa))),
                            array('TasksTeam.modified <='=>date('Y-m-d H:i:s', strtotime($now)+86399))
                        )
                    ),
                    array(
                        'AND'=>array(
                            array('TasksTeam.created >'=>date('Y-m-d H:i:s', strtotime($twa))),
                            array('TasksTeam.created <='=>date('Y-m-d H:i:s', strtotime($now)+86399))
                        )
                    )
                )
            ),
            'limit'=>$limit,
        
        ));
        
        $rs = Hash::extract($rs, '{n}.TasksTeam.task_id');
        $rs = array_unique($rs);
        return $rs;
    }

    // Used in Task->afterSave()
    public function changeLeadTeam($task_id=null, $new_lead_team_id=null){
        // Can only have one lead... find and destroy (in case) first
        if(!empty($task_id) && !empty($new_lead_team_id)){
            $this->deleteLeadTeamByTask($task_id);
            $this->addTeam($task_id, $new_lead_team_id, 1);
        }
    }
    
    // DeleteAll ($conditions=array, $cascade=true, $callbacks=false)
    // Always returns true --> Either deletes the record, or none existed
    public function deleteAllByTaskAndTeam($task, $team){
        $this->deleteAll(
            array(
                'TasksTeam.task_id'=>$task,
                'TasksTeam.team_id'=>$team), 
                false, true);
        return true;    
    }
    
    public function deleteAllNonLeadByTaskTeam($task, $team){
        $this->deleteAll(
            array(
                'TasksTeam.task_id'=>$task,
                'TasksTeam.team_id'=>$team,
                'TasksTeam.task_role_id != 1'), 
            false, 
            true
        );
        return true;    
    }
    
    public function deleteAllByTeam($team){
        if(!$team){
            return false;
        }
        
        $this->deleteAll(
            array('TasksTeam.team_id'=>$team),
            false,
            true);
        
        return true;       
    }
    
    public function deleteLeadTeamByTask($task_id){
        $this->deleteAll(array(
            'TasksTeam.task_id'=>$task_id,
            'TasksTeam.task_role_id'=>1), false, true);
        return true;    
    }
    
    //Used in TasksTeam->afterSave
    public function deletePushedTeamByTask($task = null, $team = null){
        $this->deleteAll(array(
            'TasksTeam.task_id'=>$task,
            'TasksTeam.team_id'=>$team,
            'TasksTeam.task_role_id'=>2), false, true);
        return true;    
    }

    public function deleteOpenTeamByTask($task = null, $team = null){
        $this->deleteAll(array(
            'TasksTeam.task_id'=> $task,
            'TasksTeam.team_id'=> $team,
            'TasksTeam.task_role_id'=> 3
            ), 
            false, 
            true
        );
        return true;    
    }
    
    public function deleteClosedTeamByTask($task = null, $team = null){
        $this->deleteAll(array(
            'TasksTeam.task_id'=> $task,
            'TasksTeam.team_id'=> $team,
            'TasksTeam.task_role_id'=> 4
            ), 
            false, 
            true
        );
        return true;    
    }

    







//EOF
}
