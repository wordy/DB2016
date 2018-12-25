<?php
App::uses('AppModel', 'Model');
/**
 * Actor Model
 *
 * @property Team $Team
 * @property User $User
 * @property Assignment $Assignment
 */
class Actor extends AppModel {
    // Need to use the constructor because we use model aliases for Task (i.e. Parent/Contribution)
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
    public $order = 'Actor.id asc';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'handle' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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
			'foreignKey' => 'actor_id',
			'dependent' => false,
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
    
    public function getListByTeam($team){
        $rs = $this->find('list', array(
            'conditions'=>array(
                'Actor.team_id'=>$team
            ),
            'fields'=>array(
                'Actor.id','Actor.handle', 'Actor.team_code',
            )
        ));
        foreach($rs as $team =>$acts){
            foreach($acts as $k =>$act){
                $rs[$team][$k] = '@'.$act;    
            }
        }
        return $rs;
    }

}
