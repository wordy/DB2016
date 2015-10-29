<?php
App::uses('AppModel', 'Model');
App::uses('String', 'Utility');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * User Model
 *
 * @property Team $Team
 * @property Attachment $Attachment
 */
class User extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'handle';
	
	public $components = array('Auth');
    
    public $order = array('user_role_id'=>'DESC', 'handle'=>'ASC');
     
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notblank'),
                'message' => 'A username is required',
                'allowEmpty' => false,
                'required' => true
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'This username has already been taken. Please choose another.'
            ),
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notblank'),
                'message' => 'A password is required',
                'allowEmpty' => false,
                'required' => true,
                'on' => 'create', // Limit validation to 'create' or 'update' operations
                
            )
        ),
       'handle' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'A user handle is required',
                'allowEmpty' => false,
                'required' => true
            )
        ),
       'email' => array(
            'notblank' => array(
                'rule' => array('notblank'),
                'message' => 'An email address is required.',
                'allowEmpty' => false,
                'required' => true
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'This email address is already registered to another user. Please choose another.'
            ),
            'email'=>array(
                'rule'=>'email',
                'message' => 'Invalid email address.'
            )
        ),

        'user_role_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => false,
                'required' => true,
                'message' => 'A user role is required',
            )
        ),
    );

/**
 * belongsTo associations
 * @var array
 */
    public $belongsTo = array(
        'UserRole' => array(
            'className' => 'UserRole',
            'foreignKey' => 'user_role_id',
        )
    );

/**
 * hasMany associations
 * @var array
 */
    public $hasMany = array(
        'Change' => array(
            'className' => 'Change',
            'foreignKey' => 'user_id',
        ),
        'Comment'=>array(
            'className'=> 'Comment',
            'foreignKey'=> 'user_id'
        ),

        'PrintPref'=>array(
            'className'=> 'PrintPref',
            'foreignKey'=> 'user_id'
        ),
        'TeamsUser' => array(
            'className' => 'TeamsUser',
            'foreignKey'=> 'user_id',
            'dependent' => true,
            'order'=>array('TeamsUser.team_id ASC'),
        ),
    );
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['user_role'] = sprintf('SELECT `UserRole`.`role` from `user_roles` as `UserRole` WHERE `UserRole`.`id` = %s.user_role_id', $this->alias);
         //$this->virtualFields['team_code'] = sprintf('SELECT `Team`.`code` from `teams` as `Team` WHERE `Team`.`id` = %s.team_id', $this->alias);
    }

    public function beforeSave($options=array()) {
        // Hash password if adding new record
        if(!$this->id){
            $passwordHasher = new SimplePasswordHasher();
            $this->data['User']['password'] = $passwordHasher->hash($this->data['User']['password']);
        }
    
        $this->old_teams = array();
        // Grab data pre-save... only bother if we're doing an update (i.e. $this->id exists)
        if($this->id){
            // Can't use $this->data (it's lost on save...), so save it here
            $this->presave = $this->findById($this->id);

            // Grab and save old teams
            $ct_list = $this->TeamsUser->getControlledTeamsTidList($this->id);
            
            $this->old_teams = (!empty($ct_list)) ? $ct_list : array();
        }
        
        if(!empty($this->data[$this->alias]))
            
        return true;
    }
    
    public function afterSave($created, $options=array()){
        if(isset($this->data['User']['ControlledTeams'])){
            $new_teams = array(); 

            if(!empty($this->data['User']['ControlledTeams'])){
                $new_teams = $this->data['User']['ControlledTeams'];
            }
                
            // To be added and to be deleted        
            $tba_teams = array_diff($new_teams, $this->old_teams);
            $tbd_teams = array_diff($this->old_teams, $new_teams);   

            // Process each add/delete using the model function             
            foreach ($tba_teams as $adds){
                $this->TeamsUser->addUserToTeam($this->id, $adds);
            }
            foreach ($tbd_teams as $dels){
                $this->TeamsUser->removeUserFromTeam($this->id, $dels);
            }
        }
    }
    
    public function getHandleByUser($user_id){
        if($this->exists($user_id)){
            $uhan = $this->field('handle', array($this->alias.'.id'=>$user_id));
            return $uhan;
        }
    }    

/**
 * setNewPasswordResetToken method
 *
 * @throws NotFoundException
 * @param int $user
 * @return void
 */
    public function setNewPasswordResetToken($user){
        if(!$this->exists($user)){
            return false;
        }

        $usr = $this->findById($user);

        $usr_id = $usr['User']['id'];
        
        $new_token = String::uuid();
        $new_token_hash = Security::hash($new_token, 'sha1', true);
        
        $usr['User']['pass_reset_token'] = $new_token_hash;
        $usr['User']['status'] = 401;
        
        if($this->save($usr)){
            return $new_token;
        }
        
        return false;
    }

    public function sendWelcomeEmail($user, $token){
        if(!$this->exists($user)){
            return false;
        }
        
        $usr = $this->findById($user);
        
        $uid = $usr['User']['id'];
        $status = $usr['User']['status'];
        $email = $usr['User']['email'];
        $usrToken = $usr['User']['pass_reset_token'];
        
        if($status == 401 && !empty($token) &&!empty($usrToken) && !empty($email)){
            $Email = new CakeEmail('gmail');
            $Email->from(array('DBOpsCompiler@gmail.com' => 'DBOps Compiler'));
            $Email->to($email);
            $Email->replyTo('DBOpsCompiler@gmail.com');
            $Email->subject('Welcome to '.Configure::read('AppShortName').' Compiler');
            $Email->template('welcome_email')
                ->emailFormat('html');
                //->emailFormat('both');
            $Email->viewVars(array(
                'user' => $usr,
                'token'=>$token
                )
            );    
        
            if($Email->send()){
                return $email;
            }
        }
        
        return false;
    }
	
	
    public function getDigestUsersByTeam(){
        $rs = $this->find('all', array(
            'conditions'=>array('User.user_role_id'=>10, 'User.pref_digest'=>true),
            'fields'=>array('User.id', 'User.email'),
            
        ));
        
        return $rs;
    }


    public function updateLastDigestByUser($user_id){
        $today = date('Y-m-d');
        $this->id = $user_id;
        if($this->saveField('last_digest', $today)){
            return true;
        }
        return false;
    }  



}
