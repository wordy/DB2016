<?php
App::uses('AppModel', 'Model');
/**
 * Role Model
 *
 * @property Team $Team
 * @property User $User
 * @property Assignments $Assignments
 */
class Role extends AppModel {
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['team_code'] = sprintf('SELECT `Team`.`code` from `teams` as `Team` WHERE `Team`.`id` = %s.team_id', $this->alias);
    }

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'handle';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'handle' => array(
		    'isUnique'=>array(
                'rule' => 'isUnique',
                'message' => 'That handle is already registered.  Please try something else.'
            ),
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'A handle is required',
				//'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'team_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'A handle requires a team.',
				//'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Team' => array(
			'className' => 'Team',
			'foreignKey' => 'team_id',
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

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Assignment' => array(
			'className' => 'Assignment',
			'foreignKey' => 'role_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
    
    public function getList($teams = null){
        $conditions = array();
        
        if(isset($teams)){
            $conditions = array('Role.team_id'=>$teams);
        }
        
        $rs = $this->find('list', array(
            'conditions'=>$conditions,
            'fields'=>array(
                'Role.id',
                'Role.handle',
            )
        ));
        foreach($rs as $k =>$handle){
            $rs[$k] = '@'.$handle;    
        }
        return $rs;
    }

    
    public function getListByTeam($team = null){
        $conditions = array();
        
        if(isset($team)){
            $conditions = array('Role.team_id'=>$team);
        }
        
        $rs = $this->find('list', array(
            'conditions'=>$conditions,
            'fields'=>array(
                'Role.id','Role.handle', 'Role.team_code',
            )
        ));
        foreach($rs as $team =>$rols){
            foreach($rols as $k =>$rol){
                $rs[$team][$k] = '@'.$rol;    
            }
        }
        return $rs;
    }

    public function getByTeams($teams = null){
        $conditions = array();
        
        if(isset($teams)){
            $conditions = array('Role.team_id'=>$teams);
        }
        
        $rs = $this->find('list', array(
            'conditions'=>$conditions,
            'fields'=>array(
                'Role.id','Role.handle', 'Role.team_code',
            )
        ));
        foreach($rs as $team =>$rols){
            foreach($rols as $k =>$rol){
                $rs[$team][$k] = '@'.$rol;    
            }
        }
        return $rs;
    }





    public function existsByTeamAndUser($team, $user){
        $rs = $this->find('first', array(
            'conditions'=>array(
                'team_id'=>$team,
                'user_id'=>$user
            ),
        ));
        return (!empty($rs))? TRUE:FALSE;
    }

    public function setByUsernameTeamUser($username, $team, $user){
        //$this->log('hit role');
        $rs =  array(
            'Role'=>array(
                'handle'=>$username,
                'team_id'=>$team,
                'user_id'=>$user
            )
        );
        $this->create();
        if($this->save($rs)){
            return true;
        }
        
        return false;
        
    }
    
    

}
