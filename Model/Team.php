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
			'notblank' => array(
				'rule' => array('notblank'),
				'message' => 'You must specify a team name',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'code' => array(
			'notblank' => array(
				'rule' => array('notblank'),
				'message' => 'You must specify a team code',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'zone' => array(
            'notblank' => array(
                'rule' => array('notblank'),
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
        'Change' => array(
            'className'=>'Change',
            'foreignKey'=>'team_id',
            'dependent' => true,
        ),
        'Role' => array(
            'className'=>'Role',
            'foreignKey'=>'team_id',
            'dependent' => true,
        ),

	);
    
    public $belongsTo = array(
        'TaskColor' => array(
            'className' => 'TaskColor',
            'foreignKey' => 'task_color_id',
        ),
        'Zone' => array(
            'className' => 'Zone',
            'foreignKey' => 'zone_id',
        ),
        
        
    );
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        //$this->virtualFields['zone_code'] = sprintf('SELECT `Zone`.`code` from `zones` as `Zone` WHERE `Zone`.`id` = %s.zone_id', $this->alias);
        $this->virtualFields['zone_name'] = sprintf('SELECT `Zone`.`description` from `zones` as `Zone` WHERE `Zone`.`id` = %s.zone_id', $this->alias);
         //$this->virtualFields['team_code'] = sprintf('SELECT `Team`.`code` from `teams` as `Team` WHERE `Team`.`id` = %s.team_id', $this->alias);
    }
    
    public function beforeSave($options=array()) {
        // Grab data pre-save... only bother if we're doing an update (i.e. $this->id exists)
        
        if($this->id){
            // Can't use $this->data (it's lost on save...), so save it here
            $presave = $this->findById($this->id);
            $this->presave = $presave;
        }            
        
        return true;
    }

    /**
     * After delete, delete associated:
     * Tasks
     * TasksTeam
     * Changes
     * TeamsUsers
     **/
    public function afterDelete(){
        $del_tid = $this->id;
        
        // TasksTeam
        $this->TasksTeam->deleteAllByTeam($del_tid);
        
        // Teams Users
        $this->TeamsUser->deleteAllByTeam($del_tid);

        // Changes
        $this->Change->deleteAllByTeam($del_tid);
                
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
        
        return false;
    }
    
    // 2016 Used in TeamsController::home();
    public function existsByTeamCode($team_code){
        $rs = $this->find('first', array('conditions'=>array('Team.code'=>$team_code)));
        return !empty($rs)? true: false;
    }
    
    public function getTeamCodeByTeamId($team_id = null){
        if(!$team_id){return false;}
        if ($this->exists($team_id)){
            $this->id = $team_id;
            $tcode = $this->field('code');
            return $tcode;
        }
    }

    public function getTeamIdByCode($team_code){
        $rs = $this->find('first', array(
            'conditions'=>array(
                'Team.code'=>$team_code
            )
        ));
        return (!empty($rs))? $rs['Team']['id']: false; 
    }
    
    public function getTaskColorIdByTeamId($team_id = null){
        if ($this->exists($team_id)){
            $this->id = $team_id;
            $tcid = $this->field('task_color_id');
        }
        return $tcid;
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
    
    
    public function listTeamCodeByZone(){
        $list = $this->find('all', array(
            'fields'=>array('Team.id','Team.code', 'Team.zone', 'Team.zone', 'Team.zone_id'),
            'order'=>array('Team.zone_id ASC','Team.zone ASC', 'Team.code ASC')));
                        
            $tlist = array();
            foreach ($list as $team){
                $tlist[$team['Team']['zone']][$team['Team']['id']] = $team['Team']['code'];    
            }
        
        return $list;
    }
    
    /*
    public function listTeamsExclude($teams=array()){
        $rs = $this->find('all', array(
            'conditions'=>array(    
                'Team.id !='=>$teams
            ),
            'order'=>array(
                'Team.zone_id ASC', 
                'Team.zone ASC', 
                'Team.code ASC'
            ),
            'fields'=>array(
                'Team.id',
                'Team.code', 
                'Team.zone', 
                'Team.zone_id'
            ),
        ));
        
        return $rs;
    }
*/
    public function listLeadAndPotentialAssist($leadteam){
        if(!$leadteam){
            return false;
        }
        $lead = array();
        $assist = array();
        
        $rs = $this->find('all', array(
            
            'order'=>array(
                'Team.zone_id ASC', 
                'Team.zone ASC', 
                'Team.code ASC'
            ),
            'fields'=>array(
                'Team.id',
                'Team.code', 
                'Team.zone', 
                'Team.zone_id'
            ),
        ));
        
        foreach($rs as $k=>$team){
            if ($team['Team']['id'] == $leadteam){
                $lead[]=$team;
                unset($rs[$k]);        
            }
            else{
                $assist[] = $team;
            }
        }
        
        return array('lead'=>$lead, 'assist'=>$assist);
    }
    
    public function listTeams(){
        $rs = $this->find('all', array(
            
            'order'=>array(
                'Team.zone_id ASC', 
                'Team.zone ASC', 
                'Team.code ASC'
            ),
            'fields'=>array(
                'Team.id',
                'Team.code', 
                'Team.zone', 
                'Team.zone_id'
            ),
        ));
        
        $tlist=array();
        foreach($rs as $k=>$team){
            $tlist[$team['Team']['id']] = $team['Team']['code'];
        }
        
        return $tlist;
    }
    
    public function listSelectTeamCodeByCategory($teams = array()){
        $list = $this->find('all', array(
            'conditions'=>array(
                'Team.id !='=>$teams    
            ),
            'fields'=>array(
                'Team.id',
                'Team.code', 
                'Team.zone', 
                'Team.zone_id'
            ),
            'order'=>array(
                'Team.zone_id ASC', 
                'Team.zone ASC', 
                'Team.code ASC'
            )));
                        
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
                'fields'=>array('Team.id','Team.code', 'Team.zone_name'),
                'order'=>array('Team.zone_id ASC', 'Team.zone ASC','Team.code ASC')));
        
        return $list;
    }

    public function listControlledTeamCodeByCategoryCode(){
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
    
    // Produces a list of teams, sorted by zone, that are both listed as assisting/pushed for a task and also controlled by current user.
    // Used to determine which teams a user can select as leading when linking to another task
    public function listAssistingAndControlledByUser($task){
        $ass = $this->listLinkableTeamCodeByCategoryAndTask($task);
        $con = $this->listControlledTeamCodeByCategoryCode();
        
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
    
    function teamIdCodeListByZoneCode(){
        $result = Cache::read('team_id_code_list_by_zone', 'short');
        
        if (!$result) {
            $rs = $this->Zone->getZonesTeams();
            $result = Hash::combine($rs, '{n}.Team.{n}.id', '{n}.Team.{n}.code', '{n}.Team.{n}.zone');
            Cache::write('team_id_code_list_by_zone', $result, 'short');
        }
        return $result;
    }

    function teamIdCodeList(){
        $result = Cache::read('team_id_code_list', 'short');
        
        if (!$result) {
            $rs = $this->Zone->listZoneCodeTeamIdTeamCode();
            
            $result = array();
            foreach($rs as $z_code =>$team){
                foreach($team as $tid =>$tcode){
                    $result[$tid] = $tcode;
                }
            }
            
            Cache::write('team_id_code_list', $result, 'short');
        }
        return $result;
    }
 
    /*
    // Returns like 'PRS'=>array([n]=>array('id','CODE'))
    public function zoneTeamList(){
        $result = Cache::read('team_zone_team_list', 'short');
        
        if (!$result) {
            $rs = $this->Zone->zoneTeamList();
            $result = Hash::combine($rs, '{n}.Team.{n}.id', '{n}.Team.{n}.code', '{n}.Team.{n}.zone');
            Cache::write('team_zone_team_list', $result, 'short');
        }
        return $result;
    }
*/
    /*
    function zoneTeamCodeList(){
        $tlist = $this->zoneTeamList();

        $zoneTeamCodeList = array();
        foreach ($tlist as $zone => $tids){
            foreach ($tids as $tid => $tcode){
                $zoneTeamCodeList[$zone][$tid] = $tcode;    
            }
        }
        return $zoneTeamCodeList;
    }


    function zoneNameTeamCodeList(){
        $result = Cache::read('team_zone_name_team_code_list', 'short');
        
        if (!$result) {
            $tlist = $this->zoneNameTeamList();
            $result = array();
            foreach ($tlist as $zone => $tids){
                foreach ($tids as $tid => $tcode){
                    $result[$zone][$tid] = $tcode;    
                }
            }
            Cache::write('team_zone_name_team_code_list', $result, 'short');
        }
        return $result;
    }

     
    // Returns like 'Production Support'=>array([n]=>array('id','CODE'))
    public function zoneNameTeamList(){
        $result = Cache::read('team_zone_name_team_list', 'short');
        
        if (!$result) {
            $rs = $this->Zone->zoneTeamList();
            $result = Hash::combine($rs, '{n}.Team.{n}.id', '{n}.Team.{n}.code', '{n}.Team.{n}.zone_name');
            Cache::write('team_zone_name_team_list', $result, 'short');
        }
        return $result;
    }

*/

    
    // Deprecated

    






//EOF
}
// EOF