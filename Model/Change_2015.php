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
    
     /*
     * Actions to handle various changes in the Task.
     * Used in Task's afterSave()
     * Where possible, Auth->User.id is saved too
     */

    //NOTE: Defaults to adding team as contributor
    public function addTeamToTask($task_id, $team_id, $task_role_id = 2){
        $team_code = $this->Task->Team->getTeamCodeByTeamId($team_id);
        $task_role = $this->Task->TasksTeam->TaskRole->field('description', array('id'=>$task_role_id));

        $data = array(
            'task_id'=>$task_id,
            //'text'=> $team_code. ' was added',
            );
            
        if($task_role_id==1){
            $data['change_type_id'] = 210;
            $data['text'] = $team_code. ' added as Lead';
        }
        elseif($task_role_id==2){
            $data['change_type_id'] = 220;
            $data['text'] = 'To '.$team_code;
        }
        elseif($task_role_id==3){
            $data['change_type_id'] = 230;
            $data['text'] = 'To '.$team_code;
        }
        elseif($task_role_id==3){
            $data['change_type_id'] = 240;
            $data['text'] = 'To '.$team_code;
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
    
    // Record linkage to a parent in the child task
    public function linkToParent($task_id, $parent_task_id){
        $lead = $this->Task->getLeadByTask($parent_task_id);
        $team_code = $this->Task->Team->getTeamCodeByTeamId($lead);

        $data = array(
            'task_id'=>$task_id,
            'change_type_id'=>300,
            'text'=> 'Linked to '.$team_code.' task',
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
    
    // Record a new incoming linkage in the parent task
    public function addLinkedTask($task_id, $team_id){
        $team_code = $this->Task->Team->getTeamCodeByTeamId($team_id);

        $data = array(
            'task_id'=>$task_id,
            'change_type_id'=>240,
            'text'=> $team_code. ' linked a task',
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
    
    // Records unlink in the parent_task
    public function unlinkTask($task_id, $team_id){
        $team_code = $this->Task->Team->getTeamCodeByTeamId($team_id);

        $data = array(
            'task_id'=>$task_id,
            'change_type_id'=>240,
            'text'=> $team_code. ' unlinked a task',
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
    

    
    public function removeTeamFromTask($task_id, $team_id, $task_role_id = 2){
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
    
    public function changeStartTime($task_id, $old_start_time, $new_start_time){
        $this->create();
        $data = array(
            'task_id'=>$task_id,
            'change_type_id'=>101,
            'text'=> 'Changed from '.$old_start_time.' to '.$new_start_time,
            );
        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }
        if($this->save($data)){
            return true;
        }
        else{ return false; }
    }
    
    public function changeActionableStatus($task_id, $old_type, $new_type){
        if($old_type){
            $old_code = $this->Task->ActionableType->field('name', array('ActionableType.id'=>$old_type));    
        }
        else {
            $old_code = null;
        }
                
        $new_code = $this->Task->ActionableType->field('name', array('ActionableType.id'=>$new_type));
        
        $data = array();
        $data['task_id'] = $task_id;
        $data['change_type_id']= 150;
        
        // Didn't have status set
        if(empty($old_code)){
            $data['text'] = 'Status set to '.$new_code;
        }
        if(empty($new_code)){
            $data['text'] = 'Removed from list';
        }
        if(!empty($old_code) && !empty($new_code)){
            $data['text'] ='Status changed from '.$old_code.' to '.$new_code; 
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
    /*
    public function changeActionableDate($task_id, $old_date, $new_date){
        $data = array();
        $data['task_id'] = $task_id;
        $data['change_type_id']=401;
            
        if(empty($old_date)){
            $data['text'] = 'Set to '.$new_date;
        }
        if(empty($new_date)){
            $data['text'] = 'Date removed';
        }
        if(!empty($old_date) && !empty($new_date)){
            $data['text'] = 'Changed from '.$old_date.' to '.$new_date;
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
    */
    public function changeDueDate($task_id, $old_dd, $new_dd){
        $data = array();
        $data['task_id'] = $task_id;
        $data['change_type_id'] = 160;
            
        if(empty($old_dd)){
            $data['text'] = 'Set to '.$new_dd;
        }
        if(empty($new_dd)){
            $data['text'] = 'Removed';
        }
        if(!empty($old_dd) && !empty($new_dd)){
            $data['text'] = 'Changed from '.$old_dd.' to '.$new_dd;
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
    
    // NOTE: Creator is required on creation of task.. assume true 'cuz lazy
    public function changeLeadTeam($task_id, $old_lead, $new_lead){
        $old_team = $this->Task->Team->getTeamCodeByTeamId($old_lead);
        $new_team = $this->Task->Team->getTeamCodeByTeamId($new_lead);
        
        $data = array();
        $data['task_id'] = $task_id;
        $data['change_type_id'] = 210;

        if(CakeSession::read('Auth.User.id')){
            $data['user_id']= CakeSession::read('Auth.User.id');
        }
        if(empty($old_team)){
            $data['text'] = $new_team.' created task';
        }
        if(!empty($old_team) && !empty($new_team)){
            $data['text'] = 'Ownership transferred to '.$new_team.' from '.$old_team;
        }
        
        $this->create();
        if($this->save($data)){
            return true;
        }
        else{ return false; }
    }
    
    
    
    
    
    
    
    
///////// EOF
}
///////// EOF