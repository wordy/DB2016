<?php
App::uses('AppModel', 'Model');
/**
 * Zone Model
 *
 * @property GmUser $GmUser
 * @property Team $Team
 */
class Zone extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'code';
    
    public $order = array('org_level'=>'ASC', 'id'=>'ASC');
    

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'code' => array(
			'notblank' => array(
				'rule' => array('notblank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'org_level' => array(
			'numeric' => array(
				'rule' => array('numeric'),
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
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'gm_user_id',
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
		'Team' => array(
			'className' => 'Team',
			'foreignKey' => 'zone_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
            'order'=>array('Team.code ASC'),
            
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
    
    public function zoneTeamList(){
        $rs = $this->find('all', array(
            'contain'=>array('Team')
        ));
    
        return $rs;
    }
    
    public function zoneTeamUserList(){
        $rs = $this->find('all', array(
            'contain'=>array(
                'Team',
                'Team.TeamsUser.User'
            ))
        );
    
        return $rs;
    }
    
    
    
    
    
    
    
    
//EOF
}
