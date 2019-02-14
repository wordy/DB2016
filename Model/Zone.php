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

    

    public function getZonesTeams(){
        $result = Cache::read('get_zones_teams', 'short');

        if (!$result) {
            $result = $this->find('all', array('contain'=>array('Team')));
            Cache::write('get_zones_teams', $result, 'short');
        }
        return $result;
    }

    public function zoneTeamUserList(){
        //$result = Cache::read('zone_zone_team_user_list', 'short');
        //if (!$result) {
        $result = $this->find('all', array('contain'=>array('Team', 'Team.TeamsUser.User')));
        //Cache::write('zone_zone_team_user_list', $result, 'short');
        //}
        return $result;
    }

    public function listZoneCodeTeamIdTeamCode(){
        $result = Cache::read('zone_code_team_id_team_code_list', 'short');

        if (!$result) {
            $rs = $this->getZonesTeams();
            $result = Hash::combine($rs, '{n}.Team.{n}.id', '{n}.Team.{n}.code', '{n}.Team.{n}.zone');

            Cache::write('zone_code_team_id_team_code_list', $result, 'short');
        }        

        return $result;
    }    

    public function listZoneNameTeamIdTeamCode(){
        $result = Cache::read('zone_name_team_id_team_code_list', 'short');

        if (!$result) {
            $rs = $this->getZonesTeams();
            $result = Hash::combine($rs, '{n}.Team.{n}.id', '{n}.Team.{n}.code', '{n}.Team.{n}.zone_name');

            Cache::write('zone_name_team_id_team_code_list', $result, 'short');
        }        

        return $result;
    }    

    

    

    

//EOF

}

