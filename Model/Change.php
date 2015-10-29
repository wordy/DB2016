<?php
App::uses('AppModel', 'Model');
// Used to retrieve Auth Session data, usually not available in models
App::import('component', 'CakeSession');        

/**
 * Change Model
 * @property Task $Task
 * @property ChangeType $ChangeType
 */
 
class Change extends AppModel {
    public $displayField = 'change_type_id';
    public $order = array('Change.created DESC');
    
    public $validate = array(
        'task_id' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'You must specify a Task ID',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
        'change_type_id' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'You must specify a change type',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
/*
        'text' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'You must enter a change\'s text',
                'allowEmpty' => false,
                'required' => true,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
 
        ),*/
    );
    
/**
 * belongsTo associations
 *
 * @var array
 */
    public $belongsTo = array(
        'Task' => array(
            'className' => 'Task',
            'foreignKey' => 'task_id',
        ),
        'ChangeType' => array(
            'className' => 'ChangeType',
            'foreignKey' => 'change_type_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
    );

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['change_type'] = 
        sprintf('SELECT `ChangeType`.`name` from `change_types` as `ChangeType` WHERE `ChangeType`.`id` = %s.change_type_id', $this->alias);
        
        $this->virtualFields['user_handle'] = 
        sprintf('SELECT `User`.`handle` from `users` as `User` WHERE `User`.`id` = %s.user_id', $this->alias);

        $this->virtualFields['team_code'] = 
        sprintf('SELECT `Team`.`code` from `teams` as `Team` WHERE `Team`.`id` = %s.team_id', $this->alias);

    }
    
/* *******************
 * GETTERS & SETTERS
 * *******************/
 
    public function getAllByTask($task_id){
        $changes = $this->find('all', array(
            'conditions'=>array(
                'Change.task_id'=>$task_id),
            'order'=>array(
                'Change.created DESC')));
        return $changes;        
    }

    //10-10 Untested
    public function getRecentByTeam($team){
        $tasks = $this->Task->TasksTeam->getByTeamsAndRoles($team, array(2,3));

        $today = date('Y-m-d');
        $twa = date('Y-m-d', strtotime($today.'-2 weeks'));
        //debug($twa);
        $changes = $this->find('all', array(
            'conditions'=>array(
                'Change.task_id'=>$tasks,
                'Change.created >=' => $twa,
                'Change.created <=' => $today),
            'order'=>array(
                'Change.task_id', 'Change.created DESC')));
                
        $changes = Hash::combine($changes, '{n}.Change.id', '{n}.Change', '{n}.Change.task_id');
        return $changes;
    }

    // 2015
    public function getRecentRoleChangesByTeamAndTask($team, $task){
        $now = date('Y-m-d H:i:s');
        $recent = date('Y-m-d H:i:s', strtotime($now) - 60);
        
        $rs = $this->find('all', array(
            'conditions'=>array(
                'team_id'=>$team,
                'task_id'=>$task,
                'created >' => $recent,
                'created <' => $now,
                'change_type_id IN'=> array(210, 220, 230, 240)
            )
        ));
        
        return $rs;
    }
    
    //2015 -- Used to prevent multiple role changes when cycling through task role buttons
    public function removeRecentRoleChangesByTeamAndTask($team, $task){
        $rs = $this->getRecentRoleChangesByTeamAndTask($team, $task);
        
        if($rs){
            $ids = Hash::extract($rs, '{n}.Change.id');
            $this->deleteAll(
                array('Change.id'=>$ids), 
                true
            );
        }
        return true;
    }
    
    public function getRecentChildrenByTeam($team){
        $ttasks = $this->Task->TasksTeam->getTaskIdsByTeamsAndRoles($team, $roles=array(1));
        $now = date('Y-m-d H:i:s');
        $twa = date('Y-m-d H:i:s', strtotime($now.'-2 weeks'));
            
        $rs = $this->find('all', array(
            'conditions'=>array(
                'Change.change_type_id' => 301,
                'Change.task_id' => $ttasks,
                'OR'=>array(
                    array(
                        'AND'=>array(
                            array('Change.created <'=> $now),
                            array('Change.created >'=> $twa)
                        )
                    ),
                    array(
                        'AND'=>array(
                            array('Change.modified <'=> $now),
                            array('Change.modified >'=> $twa)
                        )
                    )
                )
            ),
            'fields'=>array('Change.new_val')
        ));
        
        $rs2 = Hash::extract($rs, '{n}.Change.new_val');
        
        return $rs2;
    }
    
    
    
    /******************
     *  Record Changes 
     *    Actions to handle various changes in the Task.
     *    Used in Task's afterSave()
     *    Where possible, Auth->User.id is saved too
     *****************/

    public function changeStartTime($task_id, $old_start_time, $new_start_time){
        $this->create();
        $data = array(
            'task_id'=>$task_id,
            'change_type_id'=>101,
        );
            
        if($old_start_time){
            $data['old_val'] = $old_start_time;
        }
        if($new_start_time){
            $data['new_val'] = $new_start_time;
        }

        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }

    public function changeShortDesc($task_id){
        $this->create();
        $data = array(
            'task_id'=>$task_id,
            'change_type_id'=>102,
        );
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }

    // Record linkage to a parent in the child task
    public function setParent($task_id, $parent_task_id){
        $par_team = $this->Task->getLeadCodeByTask($parent_task_id);
        $par_sd = $this->Task->getShortDescByTask($parent_task_id);
        $par_start = $this->Task->getStartTimeByTask($parent_task_id);
        
        $data = array(
            'task_id'=>$task_id,
            'change_type_id' => 141
        );

        if($par_team){
            $data['var1'] = $par_team;
        }
        if($par_sd){
            $data['var2'] = $par_sd;
        }
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }

        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }

    // Record linkage to a parent in the child task
    public function unsetParent($task_id, $parent_task_id){
        $par_team = $this->Task->getLeadCodeByTask($parent_task_id);
        $par_sd = $this->Task->getShortDescByTask($parent_task_id);
        $par_start = $this->Task->getStartTimeByTask($parent_task_id);
        
        $data = array(
            'task_id'=>$task_id,
            'change_type_id' => 142
        );

        if($par_team){
            $data['var1'] = $par_team;
        }
        if($par_sd){
            $data['var2'] = $par_sd;
        }
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }

        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }

    
    public function changeActionableStatus($task_id, $old_type, $new_type){
        $data = array();
        
        if($old_type){
            $old_code = $this->Task->ActionableType->field('name', array('ActionableType.id'=>$old_type));    
            $data['old_val'] = $old_code;    
        }
                
        $new_code = $this->Task->ActionableType->field('name', array('ActionableType.id'=>$new_type));
        $data['new_val'] = $new_code;
        $data['task_id'] = $task_id;
        $data['change_type_id']= 150;
         
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }                
        
        $this->create();
            if($this->save($data)){
                return true;
            }
            else{
                return false; 
            }
    }

    public function changeDueDate($task_id, $old_dd, $new_dd){
        $data = array();
        $data['task_id'] = $task_id;
        $data['change_type_id'] = 160;
        
        if($old_dd){
            $data['old_val'] = $old_dd;
        }
        if($new_dd){
            $data['new_val'] = $new_dd;
        }
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }
        
        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }

    // Record start time changes in [Time linked] Children due to Parent time change
    public function movedByParent($task_id, $old_start, $new_start, $parent_task){
        $parent_team = $this->Task->getLeadCodeByTask($parent_task);
        $task_sd = $this->Task->getShortDescByTask($parent_task);

        $data = array(
            'task_id'=>$task_id,
            'change_type_id'=>170,
            );
            
        if($old_start){
            $data['old_val'] = $old_start;
        }
        if($new_start){
            $data['new_val'] = $new_start;
        }
        if($parent_team){
            $data['var1'] = $parent_team;
        }
        if($task_sd){
            $data['var2'] = $task_sd;
        }
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }

        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }
 
    public function changeLeadTeam($task_id, $old_lead, $new_lead){
        $old_team = $this->Task->Team->getTeamCodeByTeamId($old_lead);
        $new_team = $this->Task->Team->getTeamCodeByTeamId($new_lead);
        
        $data = array();
        $data['task_id'] = $task_id;
        $data['change_type_id'] = 210;
        
        if($old_team){
            $data['old_val'] = $old_team;
        }
        if($new_team){
            $data['new_val'] = $new_team;
        }
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }

        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }     
         
    // 2015
    // TT:AfterSave()    
    public function addTeamToTask($task_id, $team_id, $task_role_id, $old_role=false){
        if(!$task_id||!$team_id||!$task_role_id){return false;}
        //$old_role = $this->Task->TasksTeam->getTeamRoleByTask($team_id, $task_id);
        
        $data = array(
            'task_id' => $task_id,
            'new_val' =>  $task_role_id,
            'team_id' =>  $team_id
        );
        
        if($old_role){
            $data['old_val'] = $old_role;
        }
        
        switch($task_role_id){
            case 1:
                $data['change_type_id'] = 210;
                break;
            case 2:
                $data['change_type_id'] = 220;
                break;
            case 3:
                $data['change_type_id'] = 230;
                break;
            case 4:
                $data['change_type_id'] = 240;
                break;
        }

        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }
        
        //$this->log('addTeamToTask Change pre-save data');
        //$this->log($data);

        $this->create();
        if($this->save($data)){
            //$this->log('saved Change');
            
            return true;
        }
        else{
            return false; 
        }
    }     
     
    // TT:afterSave() 
    public function changeTeamRole($task, $team, $old_role, $new_role){
        if(!$task || !$team || !$old_role || !$new_role){
            return false;
        }
        
        $ctype = null;
        switch ($new_role) {
            case 1:
                $ctype = 210;
                break;
            case 2:
                $ctype = 220;
                break;
            case 3:
                $ctype = 230;
                break;
            case 4:
                $ctype = 240;
                break;
        }

        $data = array(
            'team_id' => $team,
            'task_id' => $task,
            'new_val' => $new_role,
            'old_val' => $old_role,
            'change_type_id'=>$ctype,
        );
        
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }

        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }          
         
     }
    
    // 2015
    // TT:afterDelete(); 
    public function removeTeamRole($task, $team){
        if(!$task || !$team){
            return false;
        }
        
        $data = array(
            'team_id' => $team,
            'task_id' => $task,
            'change_type_id'=>299,
        );
        
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }

        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }   
         
     }
     
    // Record a new incoming linkage in the parent task
    public function newChild($parent_task, $child_task){
        $team_code = $this->Task->getLeadCodeByTask($child_task);
        $task_sd = $this->Task->getShortDescByTask($child_task);

        $data = array(
            'task_id'=>$parent_task,
            'change_type_id'=>301,
        );

        if($child_task){
            $data['new_val'] = $child_task;
        }    
        if($team_code){
            $data['var1'] = $team_code;
        }
        if($task_sd){
            $data['var2'] = $task_sd;
        }
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }

        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }
    
    // Records child task unlinking in Parent
    public function childLeft($parent_task, $child_task_id){
        // Who & what left
        $lead = $this->Task->getLeadCodeByTask($child_task_id);
        $par_sd = $this->Task->getShortDescByTask($child_task_id);
        
        $data = array(
            'task_id'=>$parent_task,
            'change_type_id' => 302,
        );

        if($lead){
            $data['var1'] = $lead;
        }
        if($par_sd){
            $data['var2'] = $par_sd;
        }
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }

        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }
    
    // Records child task unlinking in Parent
    public function parentDisconnected($child_task, $parent_task){
        $lead = $this->Task->getLeadCodeByTask($parent_task);
        $par_sd = $this->Task->getShortDescByTask($parent_task);
        
        $data = array(
            'task_id'=>$child_task,
            'change_type_id' => 310,
        );

        if($lead){
            $data['var1'] = $lead;
        }
        if($par_sd){
            $data['var2'] = $par_sd;
        }
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }

        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }    

    
   public function newComment($task, $user){
        $com_user = $this->User->getHandleByUser($user);
        //$par_sd = $this->Task->getShortDescByTask($task);
        
        $data = array(
            'task_id'=>$task,
            'change_type_id' => 401,
        );

        if($com_user){
            $data['var1'] = $com_user;
        }
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }

        $this->create();
        if($this->save($data)){
            return true;
        }
        else{
            return false; 
        }
    }    
    
    
    
    

    
    
    
    
    
    
    
    /*
    
    // Records unlink in the parent_task
    public function removeParent($task_id, $parent_task){
        $pteam_id = $this->Task->getLeadByTask($parent_task);
        $team_code = $this->Task->Team->getTeamCodeByTeamId($pteam_id);

        $data = array(
            'task_id'=>$task_id,
            'change_type_id'=>240,
            'text'=> 'Unlinked from '.$team_code,
            );
            
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }
        
        $this->create();
        if($this->save($data)){
            return true;
        }
        else{ return false; }
    }
    */

    /*
    public function removeTeamFromTask($task_id, $team_id, $task_role_id){
        $team_code = $this->Task->Team->field('code', array('Team.id'=>$team_id));
        $task_role = $this->Task->TasksTeam->TaskRole->field('description', array('id'=>$task_role_id));
            
        $data = array(
            'task_id'=>$task_id,
            'text'=> $team_code. ' removed',
        );
        
        if($task_role_id==1){
            $data['change_type_id'] = 210;
        }
        elseif($task_role_id==2){
            $data['change_type_id'] = 220;
        }
        elseif($task_role_id==3){
            $data['change_type_id'] = 230;
        }
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }
        
        $this->create();
        if($this->save($data)){
            return true;
        }
        else{ return false; }
    }
    
    public function removeTeamFromTaskCompletely($task_id, $team_id){
        $data = array(
            'task_id'=>$task_id,
            'team_id'=> $team_id,
            'change_type_id'=>299,
        );
        
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }
        
        $this->create();
        if($this->save($data)){
            return true;
        }
        else{ return false; }
    }    
    */
    
    
    
    
    
    
    
    
    
///////// EOF
}
///////// EOF