<?php
App::uses('AppModel', 'Model');
/**
 * TeamsUser Model
 *
 * @property Team $Team
 * @property User $User
 */
class TeamsUser extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';
    public $order = array('TeamsUser.team_id ASC');
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'team_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'rule' => array('numeric'),
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
		'Team' => array(
			'className' => 'Team',
			'foreignKey' => 'team_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);
    
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['user_handle'] = sprintf('SELECT `User`.`handle` from `users` as `User` WHERE `User`.`id` = %s.user_id', $this->alias);
        $this->virtualFields['team_code'] = 
        sprintf('SELECT `Team`.`code` from `teams` as `Team` WHERE `Team`.`id` = %s.team_id', $this->alias);
    }

    public function beforeSave($options = array()) {
        // TODO: Upgrade to uniquekey.  For now, ensures only 1 assocaition between user and team.
        $conditions = array(
            'user_id'=>$this->data['TeamsUser']['user_id'],
            'team_id'=>$this->data['TeamsUser']['team_id']);
        if ($this->hasAny($conditions)){
            return false;
        }
        
        return true;
        
    }

    public function deleteAllByTeam($team_id){
        if(!$team_id){
            return false;
        }
        
        $this->deleteAll(array(
            'TeamsUser.team_id'=>$team_id
            ), 
            false, 
            true
        );
        return true;    
    }

    public function getTeamsByUser($user_id){
        $rs = $this->find('all', array(
            'conditions'=>array(
                'TeamsUser.user_id'=>$user_id),
            'order'=>array('TeamsUser.team_id ASC')));
        
        if($rs){
            $teams = Hash::extract($rs, '{n}.TeamsUser.team_id');
            return $teams;        
        }

        return array();
    }

    // 2015
    public function getUIDsByTeam($team){
        $rs = $this->find('all', array(
            'conditions'=>array(
                'TeamsUser.team_id'=>$team),
            'order'=>array('TeamsUser.team_id ASC')));
        
        if($rs){
            $teams = Hash::extract($rs, '{n}.TeamsUser.user_id');
            return $teams;        
        }
        return array();
    }

    // 2015
    public function getTeamLeadUIDsByTeam($team){
        $rs = $this->find('all', array(
            'conditions'=>array(
                'TeamsUser.team_id'=>$team,
            ),
            'contain'=> array(
                'User'=>array(
                    'conditions'=>array('User.user_role_id'=>10)
                )
            ),
            'order'=>array('TeamsUser.team_id ASC')));
        
        if($rs){
            $teams = Hash::extract($rs, '{n}.TeamsUser.user_id');
            return $teams;        
        }
        return array();
    }

    public function getControlledTeamsList($user_id){
        $this->resursive=1;
        
        $rs = $this->find('all', array(
            'conditions'=>array(
                'TeamsUser.user_id'=>$user_id),
            'order'=>array('TeamsUser.team_code ASC'),
            'fields'=>array('DISTINCT TeamsUser.team_id','TeamsUser.user_id', 'TeamsUser.team_code')));
        
        if($rs){
            $teams = Hash::combine($rs, '{n}.TeamsUser.team_id', '{n}.TeamsUser.team_code');
            return $teams;        
        }

        return array();
    }
    
    public function getControlledTeamsTidList($user_id){
        $rs = $this->find('all', array(
            'conditions'=>array(
                'TeamsUser.user_id'=>$user_id),
            'order'=>array('TeamsUser.team_id ASC'),
            'fields'=>array('DISTINCT TeamsUser.team_id','TeamsUser.user_id', 'TeamsUser.team_code')));
        
        if($rs){
            $teams = Hash::extract($rs, '{n}.TeamsUser.team_id');
            return $teams;        
        }

        return array();
         
    }
    
    
    public function getTeamCodesByUser($user){
        $rs = $this->find('all', array(
            'conditions'=>array(
                'TeamsUser.user_id'=>$user),
            'order'=>array('TeamsUser.team_id ASC')));
        
        if($rs){
            $teams = Hash::extract($rs, '{n}.TeamsUser.team_code');
            return $teams;        
        }
        
        return array();
    }
    
    public function addUserToTeam($user_id=null, $team_id=null){
        if($user_id && $team_id){
            $data = array(
                'team_id'=>$team_id,
                'user_id'=>$user_id);

            $this->create();
            if($this->save($data)){
                return true;        
            }
        }
        return false;
    }

    //1113
    public function removeUserFromTeam($user_id, $team_id){
        if($user_id && $team_id){
            $conditions = array(
                'conditions'=>array(
                    'team_id'=>$team_id, 
                    'user_id'=>$user_id
                )
            );    
            $tu_rs = $this->find('first', $conditions);
            $this->delete($tu_rs['TeamsUser']['id'], false);
        }
    }
    
    public function existsByTeamUser($team, $user){
        $rs = $this->find('first', array('TeamsUser.team_id'=>$team, 'TeamsUser.user_id'=>$user));      
        return (!empty($rs))? true: false;
    }
/**
 * Gets users subscribed to receive digest, indexed by team
 * @param {Array} $teams list of teams to fetch for. Default = ALL
 * @return {Array} $users List of users [team]=>[n]=>[user]
 * @since 2015
 * TODO: Supports only sending digest to TLs
 **/
    public function getDigestUsersByTeam($teams = array()){
        $conditions = array(
            'User.user_role_id' => 10, 
            'User.pref_digest' => true,
            'User.email <> ""',
        );
        
        if(!empty($teams)){
            $c2 = array('TeamsUser.team_id' => $teams);
            $conditions = array_merge($conditions, $c2);
        }
        
        $rs = $this->find('all', array(
            'conditions'=>$conditions,
            'contain'=>array('User'))
        );
        
        $rs = Hash::combine($rs, '{n}.TeamsUser.user_id', '{n}', '{n}.TeamsUser.team_id');
        
        $rs2 = array();
        foreach($rs as $team => $users){
            foreach($users as $uid => $user){
                $rs2[$team][] = array(
                    'id'=>$uid,
                    'email'=>$user['User']['email'],
                    'user_handle'=>$user['TeamsUser']['user_handle'],
                    'team_code'=>$user['TeamsUser']['team_code'],
                    'team_id'=>$user['TeamsUser']['team_id'],
                    'last_digest'=>$user['User']['last_digest'],
                );    
            }
        }
        return $rs2;
    }
        
    
    
    
    
    
    
    
    
    
    
}
