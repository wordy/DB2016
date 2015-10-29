<?php
App::uses('AppModel', 'Model');
/**
 * Team Model
 *
 * @property Task $Task
 * @property User $User
 */
class Team extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'code';
    public $order = array(
        'zone_id'=>'asc',
        'zone'=>'asc',
        'code'=>'asc'
    );

	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'You must specify a team name',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'code' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'You must specify a team code',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'zone' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'You must specify a team\'s zone',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
	);


/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
        'TasksTeam' => array(
            'className' => 'TasksTeam',
            'foreignKey' => 'team_id',
            'dependent' => true,
        ),
		'TeamsUser' => array(
			'className' => 'TeamsUser',
			'foreignKey' => 'team_id',
			'dependent' => true,
		),
        'Task' => array(
            'className'=>'Task',
            'foreignKey'=>'team_id',
            'dependent' => true,
            ),
        'NotificationReceived' => array(
            'className'=>'Notification',
            'foreignKey'=>'rec_team_id',
            'dependent' => true,
        ),
        'NotificationSent' => array(
            'className'=>'Notification',
            'foreignKey'=>'send_team_id',
            'dependent' => true,
        ),
	);
    
    public $belongsTo = array(
        'TaskColor' => array(
            'className' => 'TaskColor',
            'foreignKey' => 'task_color_id',
        ),
    );
    
    public function beforeSave($options=array()) {
        // Grab data pre-save... only bother if we're doing an update (i.e. $this->id exists)
        
        if($this->id){
            // Can't use $this->data (it's lost on save...), so save it here
            $presave = $this->findById($this->id);
            $this->presave = $presave;
        }            
        
        return true;
    }

    /*
     * After delete, delete associated:
     * Tasks
     * Notifications
     * TasksTeam
     * 
     */
    public function afterDelete(){
        $del_tid = $this->id;
        
        // TasksTeam
        $this->TasksTeam->deleteAllByTeam($del_tid);
        
        // Tasks
        //$this->Task->deleteAllByTeam($del_tid);
        
        // Notifications
        $this->NotificationReceived->deleteAllByTeam($del_tid);
        
        // Teams Users
        $this->TeamsUser->deleteAllByTeam($del_tid);
                
        return true;
    }
    
    public function afterSave($created, $options = array()){
        $before = $this->presave;
        
        // Process only if it was a record update    
        if($created==false){
            $after = $this->findById($this->id);

            // Detect change in team's color
            if($after['Team']['task_color_id'] != $before['Team']['task_color_id']){
                $this->Task->updateAll(
                    array(
                        'Task.task_color_id'=>$after['Team']['task_color_id']), 
                    array(
                        'Task.team_id'=>$after['Team']['id']));
            }
        }
    }
    
    public function isControlledBy($team_id, $user){
        //$task_owner = $this->field('team_id', array('id' => $task_id)); 
        $user_teams = $this->TeamsUser->getTeamsByUser($user['id']);
        
        //$this->log($user_teams);
        
        if(in_array($team_id, $user_teams)){
            return true;
        }
        else{ return false; }
    }
    
    
    public function getTeamCodeByTeamId($team_id = null){
        if ($this->exists($team_id)){
            $this->id = $team_id;
            $tcode = $this->field('code');
        }
        return ($tcode)? $tcode: null;    
    }
    
    public function getTaskColorIdByTeamId($team_id = null){
        if ($this->exists($team_id)){
            $this->id = $team_id;
            $tcid = $this->field('task_color_id');
        }
        return ($tcid)? $tcid: null;
    }

    public function listTeamCodeByCategory(){
        $list = $this->find('all', array(
            'fields'=>array('Team.id','Team.code', 'Team.zone', 'Team.zone_id'),
            'order'=>array('Team.zone_id ASC','Team.zone ASC', 'Team.code ASC')));
                        
            $tlist = array();
            foreach ($list as $team){
                $tlist[$team['Team']['zone']][$team['Team']['id']] = $team['Team']['code'];    
            }
        
        return $tlist;
    }    

    public function listControlledTeamCodeByCategory(){
        $uteams = AuthComponent::user('Teams');
        
        $list = $this->find('list', array(
                'conditions'=>array(
                    'Team.id'=>$uteams),
                'fields'=>array('Team.id','Team.code', 'Team.zone'),
                'order'=>array('Team.zone_id ASC', 'Team.zone ASC','Team.code ASC')));
        
        return $list;
    }
    
    public function listLinkableTeamCodeByCategoryAndTask($task){
        $teams = $this->TasksTeam->getLinkableTeamsByTask($task);
        $list = $this->find('all', array(
            'conditions' => array(
                'Team.id' => $teams),
            'fields'=>array('Team.id','Team.code', 'Team.zone', 'Team.zone_id'),
            'order'=>array('Team.zone_id ASC','Team.zone ASC', 'Team.code ASC')));
                        
            $tlist = array();
            foreach ($list as $team){
                $tlist[$team['Team']['zone']][$team['Team']['id']] = $team['Team']['code'];    
            }
        
        return $tlist;
    }
    
    // Produces a list of teams, sorted by zone, that are both listed
    // as assisting/pushed for a task and also controlled by current user.
    // Used to determine which teams a user can select as leading when 
    // linking to another task
    public function listAssistingAndControlledByUser($task){
        $ass = $this->listLinkableTeamCodeByCategoryAndTask($task);
        $con = $this->listControlledTeamCodeByCategory();
        $aic_teams = array();
        
        foreach($ass as $zone => $zteams){
            if(array_key_exists($zone, $con)){
                $ain_tmp = array_intersect($con[$zone], $zteams);
                if(!empty($ain_tmp)){
                    $aic_teams[$zone] = $ain_tmp;    
                }
            }
        }
        return $aic_teams;
    }
    

    
    
    
    // Deprecated
    /*
    public function teamsByZoneList(){
        $rs = $this->find('list', array(
            'fields'=>array('Team.id','Team.code', 'Team.zone'),
            'order'=>array('Team.zone ASC','Team.code ASC')));
        
        $num_zones = count($rs) - 1;
        
        $tlist = array();
        $i=0;
        foreach($rs as $zone=>$tid){
            $tlist[$zone][]=$tid;
        }
    

        return $list;
    }
    
    public function makeAllTeamIdList(){
        $list = $this->find('all', array(
            'conditions'=>array(
                'NOT'=>array(
                    'Team.code'=>array(
                        'ALL','SYS'))),
            'fields'=>array(
                'Team.id')));
                //$this->log($list);
        $list = Hash::extract($list, '{n}.Team.id');
        
        return $list;
        
    }
// Generates [id]=>team_code lists, useful for HTML <select>s
    // Allows array of excluded team codes (as strings) or team_ids as numeric
    public function listTeamCodeExclude($exclude=array()){
        if(!empty($exclude) && is_array($exclude)){
            //die(debug($exclude));
            $ex_code = array();
            $ex_id = array();
            foreach ($exclude as $key => $ex) {
                if(is_numeric($ex)){
                    $ex_id[]=$ex;
                }
                elseif(is_string($ex)) {
                    $ex_code[]=$ex;
                }
            }
            
            //$this->log($ex_code);$this->log($ex_id);
            $list = $this->find('all',array(
                'conditions'=>array(
                    'NOT'=>array(
                        'OR'=>array(
                            'Team.code'=>$ex_code,
                            'Team.id'=>$ex_id))),
                    'order'=>'Team.code ASC'));
            
            $list = Hash::combine($list, '{n}.Team.id', '{n}.Team.code', '{n}.Team.zone');  
        }
        
        // No exclusions, return full list
        else{
            $list = $this->find('list', array(
                'fields'=>array('Team.id','Team.code','Team.zone'),
                'order'=>array('Team.zone'=>'ASC', 'Team.code'=>'asc')));
        }
        
        return $list;
    }
    // Generates [#]=>id lists
    // Allows array of excluded team codes (as strings) or team_ids as numeric
    public function listTeamIdExclude($exclude=array()){
        if(!empty($exclude) && is_array($exclude)){
            
            $ex_code = array();
            $ex_id = array();
            foreach ($exclude as $key => $ex) {
                if(is_numeric($ex)){
                    $ex_id[]=$ex;
                }
                
                elseif(is_string($ex)) {
                    $ex_code[]=$ex;
                }
            }
            $list = $this->find('all',array(
                'conditions'=>array(
                    'NOT'=>array(
                        'Team.code'=>$ex_code,
                        'Team.id'=>$ex_id)),
                    'order'=>'Team.id ASC'));
                    
            
            $list = Hash::extract($list, '{n}.Team.id');  
        }
        
        // No exclusions, return full list
        else{
            $list = $this->find('list', array(
                'fields'=>array('Team.id'),
                'order'=>'Team.code ASC'));
        }
        
        return $list;
    }
    // Generates [id]=>team_code lists, useful for HTML <select>s
    public function listByTeamIds($teams){
        $list = $this->find('list',array(
                'conditions'=>array(
                    'Team.id'=>$teams),
                'order'=>array('Team.zone ASC','Team.code ASC'),
                'fields'=>array('Team.id', 'Team.code', 'Team.zone')));
        
        return $list;
    } * 
     * 
     */
     
     public function getNotificationCountByTeam($team){
         return $this->NotificationReceived->getInboxCountByTeam($team);
         //return $rs;
     }
    










}
