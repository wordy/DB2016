<?php

//require_once 'Google/autoload.php'; // or where

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::uses('CakeText', 'Utility');



/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

/**
 * Components
 *
 * @var array
 */
    public $components = array('Paginator');
    public $paginate = array(
        'limit' => 50,
        'order' => array(
            'User.user_role_id' => 'desc'
            
        )
    );
 
    public function beforeFilter() {
        
        parent::beforeFilter();
        
        $this->Auth->allow(array(
            'login',
            'logout',
            'changePassword',
            'forgotPassword',
            'chooseNewPass'));
    }
    
    public function isAuthorized($user){
        // Default allows (logged in users)
        if($this->action === 'changePassword'){
            return true;
        }

        // CCs    
        if(($user['user_role_id']) >= 200){
            if(in_array($this->action, array(
                'add',
            ))){
            return true;
            }
        }
        
        // Default allows
        if (in_array($this->action, array(
            'profile',
            'orgChart',
            'getUserTeamsList',
            'changeTeam',            
            ))) {
            return true;
        }

        // User can edit their own user prefs
        if ($this->action == 'prefs'){
            $uid = isset($this->request->params['pass'][0])? $this->request->params['pass'][0]: null;
                
            if ($uid == $user['id']) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }
    
    /**
 * index method
 *
 * @return void
 */
    public function index() {
        $this->Paginator->settings = array(
            'User'=>array(
                'limit'=>50,
                'order'=>array(
                    'user_role_id'=>'desc',
                    'handle'=>'asc'),
                'contain'=>array(
                    'TeamsUser',
                    'UserRole')));
        $this->User->recursive = 1;
        $this->set('users', $this->paginate());
    }


/*
 public function test(){
     require_once 'Google/autoload.php'; // or wherever autoload.php is located
//  $client = new Google_Client();
     $client_id = '190045122219-a4cve4oo8jhad4ot0uos3mrmfqm53nkr.apps.googleusercontent.com';
$client_secret = 'LxyPhtxeYhgGsVFbC5EKk1yu';
//$redirect_uri = '<YOUR_REDIRECT_URI>';
//  $client->setApplicationName("Client_Library_Examples");
$client = new Google_Client();
$client->setClientId($client_id);
  $client->setDeveloperKey("AIzaSyCF8sRfrKPNRbaQONw5-htKWiQJbNhsRAM");  

$client->setClientSecret($client_secret);
//$client->setRedirectUri($redirect_uri);
$client->setScopes('email');
     
  */   
     


  //$service = new Google_Service_Books($client);
  
  //$this->log($service);
  //$optParams = array('filter' => 'free-ebooks');
  //$results = $service->volumes->listVolumes('Henry David Thoreau', $optParams);

    //$data = array();
  //foreach ($results as $item) {
    //$data[] = $item['volumeInfo']['title'];
     
 //}
  
  //$gdrive = new Google_Service_Drive($client);
  
  //$this->log($gdrive);
  /*
  $result = array();
  $pageToken = NULL;

  do {
    try {
      $parameters = array();
      if ($pageToken) {
        $parameters['pageToken'] = $pageToken;
      }
      $files = $gdrive->files->listFiles($parameters);

      $result = array_merge($result, $files->getItems());
      $pageToken = $files->getNextPageToken();
    } catch (Exception $e) {
      print "An error occurred: " . $e->getMessage();
      $pageToken = NULL;
    }
  } while ($pageToken);
  //return $result;
  */
  
  //$results = $gdrive->list();
  
  
  //$this->log($gdrive->about);
  
        //$result = $gdrive->files->listFiles();
  
    //$result = $gdrive->getItems();
  
//  $this->set('data', $result);
//  $this->render('/Elements/Utility/debug');
// }

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function view($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        
        $owa = date('Y-m-d', strtotime(date('Y-m-d').'-2 weeks'));
        $contain = array(
            'TeamsUser',
            'Comment',
            'UserRole',
            'Change.Task',
            'Change'=>array(
                'conditions'=>array('Change.created >'=>$owa),
                )
        );
        
        $options = array(
            'contain'=>$contain,
            'conditions' => array(
                'User.' . $this->User->primaryKey => $id
            )
        );
        $this->set('user', $this->User->find('first', $options));
    }

/**
 * add method
 *
 * @return void
 */
    public function add() {
        if ($this->request->is('post')) {
            $send_welcome = $this->request->data['User']['send_welcome'];
            $this->request->data['User']['password'] = 111;
                
            // Flag for new user
            $this->request->data['User']['status'] = 100;
            
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('New user created.'), 'flash/success');
                
                if($send_welcome){
                    $new_uid = $this->User->getLastInsertId();
                    // Get Token
                    $token = $this->User->setNewPasswordResetToken($new_uid);
                    
                    if($token){
                        // Send Welcome Email
                        $welcome = $this->User->sendWelcomeEmail($new_uid, $token);
                    }
                        
                    if($token && $welcome){
                        $this->Session->setFlash(__('New user created and welcome email sent.'), 'flash/success');
                    }
                    else{
                        $this->Session->setFlash(__('User created, but there was a problem sending the welcome email.'), 'flash/error');
                    }    
                    
                }

                //$this->redirect(array('action' => 'index'));
                $this->redirect($this->referer());
            } 
            else{ // Couldn't save user
                $msg = Hash::extract($this->User->validationErrors, '{s}.{n}');
                $this->Session->setFlash($msg[0], 'flash/error');
            }
        } // Not post
        $teams = $this->User->TeamsUser->Team->listTeamCodeByCategory();
        $userRoles = $this->User->UserRole->find('list', array('conditions'=>array(
            'UserRole.id >='=>5)));
        $this->set(compact('teams', 'userRoles'));
    }

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function edit($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            //Unset password field so it doesn't try to hash the hash!
            //if(isset($this->data['User']['password'])){
            //    unset($this->data['User']['password']);
            //}
            $user_teams = $this->request->data['User']['ControlledTeams'];
            
            $this->User->create();
            //$this->log($this->request->data);
                if ($this->User->save($this->request->data)) {
                    $this->Session->setFlash(__('The user has been saved'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                 }//saved
                else { //didn't save
                    $this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'flash/error');
                 }
        }//Not post/put
        else {
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
        }
        $teams = $this->User->TeamsUser->Team->listTeamCodeByCategory();
        $userRoles = $this->User->UserRole->find('list', array('conditions'=>array(
            'UserRole.id >='=>5)));
        $teamsUser = $this->User->TeamsUser->getTeamsByUser($id);
        $userTeamCodes = $this->User->TeamsUser->getControlledTeamsTidList($id);
        $this->set(compact('teams', 'userRoles', 'teamsUser', 'userTeamCodes'));
        
    }

/**
 * prefs method
 * Allows users to alter preferences related to their compiler account
 * @throws NotFoundException
 * @param int $id user to look up
 * @return void
 */
    public function prefs($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Your preferences have been updated.'), 'flash/success');
                $this->redirect($this->referer());
             }//saved
            else { //didn't save
                //$this->log($this->User->validationErrors);
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'flash/error');
             }
        }//Not post/put
        else {
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
        }
        $this->render('user_prefs');
    }

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param {int} $id
 * @return void
 */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function login() {
        if ($this->request->is('post')) {
            //$this->log($this->request->data);
            if ($this->Auth->login()) {
                $this->User->id = $this->Auth->user('id');
                $user = $this->User->findById($this->Auth->user('id'));
                $now = date('Y-m-d');
                $sdate = $edate = null;
                $edate_config = Configure::read('CompileEnd');
                $sdate_config = Configure::read('CompileStart');
                $sdate = date('Y-m-d', strtotime($sdate_config));
                $edate = date('Y-m-d', strtotime($edate_config)+86399);
                
                // Save new last login date
                $this->User->saveField('last_login', $now);
                
                // Cancel password reset request if a user logs in successfully. 
                // Sets account "active" and updates session
                if($user['User']['pass_reset_token'] && $user['User']['status']==401){
                    $this->User->saveField('status', 200);
                    $this->User->saveField('pass_reset_token', null);
                    $this->Session->write('Auth', $this->User->findById(AuthComponent::user('id')));
                    $this->Session->setFlash(__('<b>Heads Up!</b> You\'ve successfully logged in so your existing password reset request has been cancelled.'), 'flash/info');        
                }

                // Set User's Controlled Teams:
                $userTeams = $this->User->TeamsUser->getTeamsByUser($this->User->id);
                $userTeamCodes = $this->User->TeamsUser->getTeamCodesByUser($this->User->id);
                $userTeamsList = $this->User->TeamsUser->getControlledTeamsList($this->User->id);
                $this->Session->write('Auth.User.TeamsList', $userTeamsList);
                $this->Session->write('Auth.User.Teams', $userTeams);
                $this->Session->write('Auth.User.TeamCodes', $userTeamCodes);
                $this->Session->write('Auth.User.TeamsByZone', $this->User->TeamsUser->Team->listControlledTeamCodeByCategory());
                // Set up array for Timeshift
                $this->Session->write('Auth.User.Timeshift', array());

                $mainTeamCode = $mainTeamId = false;
                if(count($userTeamsList) == 1){
                    $mainTeamCode = reset($userTeamsList);
                    reset($userTeamsList);
                    $mainTeamId = key($userTeamsList);
                    $this->Session->write('Auth.User.Settings.main_team_code', $mainTeamCode);
                    $this->Session->write('Auth.User.Settings.main_team_id', $mainTeamId);
                }
                // Set User's Default Compile Params
                $comp = array();
                $comp['Teams'] = $userTeams;
                $comp['start_date'] = $sdate;
                $comp['end_date'] = $edate;
                $comp['sort'] = 0;
                $comp['view_type'] = 1;
                $comp['view_details'] = 1;
                $comp['view_links'] = 1;
                $comp['view_threaded'] = 0;
                $comp['page'] = 1;
                $this->Session->write('Auth.User.Compile', $comp);
                
                // Default Timeshift Mode is "off"
                $this->Session->write('Auth.User.Timeshift.Mode', 0);
                $this->Session->write('Auth.User.Timeshift.Unit', "Min");
                
                
                return $this->redirect($this->Auth->redirectUrl());
            }
            else{
                $this->Session->setFlash(__('Invalid username or password, please try again'), 'flash/error');        
            }
        }
    } 

    //2017
    public function setTimeshiftPref($val){
        if($this->Session->write('Auth.User.Timeshift.Mode', $val)){
            if($this->request->is('ajax')){
                $this->response->statusCode(200);
                $this->response->body(json_encode(array('success'=>true))); 
            } 
        }
    }

    public function setTimeshiftUnit($val){
        if($this->Session->write('Auth.User.Timeshift.Unit', $val)){
            if($this->request->is('ajax')){
                $this->response->statusCode(200);
                $this->response->body(json_encode(array('success'=>true)));
            } 
        }
    }
 
    public function logout() {
        $this->Session->setFlash("You've successfully logged out.");
        $this->Session->destroy();
        return $this->redirect($this->Auth->logout());
    }
    
    // Function used by CC+ to reset a user's password
    /*
    public function adminResetPassword($id = null) {
    
        if ($this->request->is('post') || $this->request->is('put')) {
            if($this->request->data('User.password')){
                $passwordHasher = Security::hash($this->request->data['User']['password'], 'sha1', true);
                $this->request->data['User']['password'] = $passwordHasher;
            }
            
            $this->User->create();
                if ($this->User->save($this->request->data)) {
                    $this->User->saveField('status', 401);
                    $this->Session->setFlash(__('Password reset'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                 }//saved
                else { //didn't save
                    $this->Session->setFlash(__('Password could not be reset. Please try again.'), 'flash/error');
                 }
        }//Not post/put
        else {
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
        }
        $user_role = $this->Session->read('Auth.User.user_role_id');
        // Here we only allow users to change the password of someone who is at or BELOW their user level
        // I.e. CC can't change an admin password
        $users = $this->User->find('list', array('conditions'=>array(
            'User.user_role_id <='=>$user_role )));
        $this->set(compact('users'));
        $this->render('admin_password_reset');
        
    }
*/
    public function changePassword() {
        $uid = $this->Session->read('Auth.User.id');

        if($this->request->is('post') || $this->request->is('put')) {
            $this->request->data('User.id', $uid);
            $old_pass = $this->request->data('User.current_pass');
            $new_pass1 = $this->request->data('User.password1');            
            $new_pass2 = $this->request->data('User.password2');
            
            // Poor test of strong passwords. Validation is done client-side with 
            // https://github.com/ablanco/jquery.pwstrength.bootstrap
            if(strlen($new_pass1) < 5){
                $this->Session->setFlash(__('Please choose a more secure password.'), 'flash/error');
                return $this->redirect( $this->referer() );
            }
            
            $user = $this->User->find('first', array('recursive'=>1, 'conditions'=>array('User.id'=>$uid)));
            $curr_pwhash = $user['User']['password'];
            $trial_pwhash = Security::hash($old_pass, 'sha1', true);
            
            // Check if newly entered passes match, proceed if so
            if($new_pass1 == $new_pass2){                               // Pass and confirmation match
                $new_pwhash = Security::hash($new_pass1, 'sha1', true);
                if($curr_pwhash == $trial_pwhash){                      // Old pass equals stored val
                    $this->User->id = $uid;
                    if ($this->User->saveField('password', $new_pwhash)) {
                        $this->Session->setFlash(__('Your password has been changed. You will need to use your new password the next time you log into the Compiler.'), 'flash/success');
                        return $this->redirect( $this->referer() );
                    }
                }
                else { //Old/new hashes didn't match
                    $this->Session->setFlash(__('Old password was entered incorrectly. Please try again.'), 'flash/error');
                    return $this->redirect( $this->referer() );
                }
            }
            // Passwords didn't match            
            else {
                $this->Session->setFlash(__('New password and confirmation didn\'t match. Please try again.'), 'flash/error');
                return $this->redirect( $this->referer() );
            }
        }
        
        //$this->autoRender = false;
        $this->render('password_change');
    }
   
    public function profile($uid){
        if (!$this->User->exists($uid)) {
            throw new NotFoundException(__('Invalid user'));
        }
        
        $rs = $this->User->find('first', array(
            'conditions'=>array(
                'User.id'=>$uid,
            ),
            'fields'=>array(
                'User.id','User.handle','User.email','User.username','User.user_role_id','User.user_role', 'User.status',
            ),
            'contain'=>array(
                'TeamsUser'=>array(
                    'fields'=>array('TeamsUser.id','TeamsUser.team_code')    
                ),
                'Change'=>array(
                    'fields'=>array('Change.id','Change.user_id','Change.created')
                ),
                'Comment'=>array(
                    'fields'=>array('Comment.id','Comment.user_id','Comment.created')
                )
            )
        ));
        
        $this->set('user', $rs);
        $this->render('profile');
    }
    
    public function orgChart(){
        $rs = $this->User->TeamsUser->Team->Zone->zoneTeamUserList();
        $this->set('zoneTeamUserList', $rs);
    }

    function forgotPassword($token=null){
        // User submitted email to begin reset
        if ($this->request->is('post')) {
            // Get Email
            $email = $this->request->data('User.email');
            
            if(!$email){
                $this->Session->setFlash('Please specify an email address', 'flash/error');
                return;
            }
            // Find user
            $usr = $this->User->find('first', array('conditions'=>array('email'=>$email)));    
            if(isset($usr['User']['id'])){
                // Create reset token. Store hashed version while sending non-hashed to usr
                $new_token = CakeText::uuid();
                $new_token_hash = Security::hash($new_token, 'sha1', true);
                
                // Save token
                $this->User->id = $usr['User']['id'];
                $this->User->saveField('pass_reset_token', $new_token_hash);
                $this->User->saveField('status', 401);
            
                $Email = new CakeEmail('gmail');
                $Email->from(array('DBOpsCompiler@gmail.com' => 'DBOps Compiler'));
                $Email->to($usr['User']['email']);
                $Email->replyTo('DBOpsCompiler@gmail.com');
                $Email->subject('Compiler Password Reset Request');
                $Email->template('user_reset_pw')
                //->emailFormat('html');
                    ->emailFormat('both');
                $Email->viewVars(array(
                    'user' => $usr,
                    'pw_reset_token'=>$new_token
                    )
                );
                $Email->delivery = 'smtp';
                
                $Email->send();
                
                $this->Session->setFlash('Password reset email sent to <b>'.$email.'</b>. Please check your mail for details.', 'flash/success');
            }
            else{
                // Didn't find valid user
                $this->Session->setFlash('User Not Found. Please re-enter email address', 'flash/error');
            }
        } 
        else{ // Not posted
            if(isset($token)){ // Submitted token, i.e. from reset email
                //$new_token = String::uuid();
                $test_token_hash = Security::hash($token, 'sha1', true);

                $usr_rs = $this->User->find('first', array(
                    'conditions'=>array(
                        'User.pass_reset_token'=>$test_token_hash
                )));
                
                if(isset($usr_rs['User']['id']) && !empty($usr_rs['User']['id'])){
                    $this->Session->setFlash('Reset Token Verified. <p>You may now choose a new password.</p>','flash/success');
                    //return $this->chooseNewPass($test_token_hash);
                    
                    return $this->redirect(array('controller'=>'users', 'action'=>'chooseNewPass', $test_token_hash));
                    
                }
                else{
                    $this->Session->setFlash('Invalid Token. <p>Please double check the URL. This can also happen if you reset your password more than once accidently (check for a newer reset email).<p> If this persists, simply request another password reset and wait for the newest email.</p>','flash/error');
                    //return;
                }
                
            }
            else{ // No token.  Wanting to start new password reset
            }
        }
        $this->render('password_forgot');
    }

    public function chooseNewPass($reset_token = null){
        if($this->Auth->user('id')){
            $this->Session->destroy();
        }
        
        if ($this->request->is('post')) {
            $p1 = $this->request->data('User.password');
            $p2 = $this->request->data('User.password2');
            $reset_token = $this->request->data('User.reset_token');
            $this->set('reset_token', $reset_token);
            $trial_hash = Security::hash($reset_token, 'sha1', true);
            
            // Find corresponding user
            $rs = $this->User->find('first', array(
                'conditions'=>array(
                    'pass_reset_token' => $trial_hash
                )
            ));
            
            if(!$rs){
                $this->Session->setFlash('<b>Invalid Token (No User Matched).</b> The token you supplied is incorrect. Please double check the reset URL you were emailed or request a new token.', 'flash/error');
                return $this->redirect(array('controller'=>'users', 'action'=>'forgotPassword'));
            }
    
            $uid = $rs['User']['id'];
            
            if(empty($p1) || empty($p2)){
                //$this->log('was empty');    
                $this->Session->setFlash('<b>Password Empty.</b> Password and confirmation must be set. Please try again.', 'flash/error');
                return $this->redirect(array(
                    'controller'=>'users', 
                    'action'=>'chooseNewPass',$reset_token,
                    '?'=>array(
                        'source'=>'action'
                    )));
            }
            
            // Check if newly entered passes match, proceed if so
            if(!empty($p1) && !empty($p2) && ($p1 == $p2) && !empty($uid)){
                $new_pwhash = Security::hash($p1, 'sha1', true);
                $this->User->id = $uid;
                    
                if ($this->User->saveField('password', $new_pwhash)) {
                    $this->User->saveField('pass_reset_token', null);
                    $this->User->saveField('status', 200);
                    $this->Session->setFlash(__('Your new password has been set. You may use it to log in now.'), 'flash/success');
                    return $this->redirect(array('controller'=>'users', 'action'=>'login'));
                }
            }
            else { //Old/new hashes didn't match
                $this->set('reset_token', $reset_token);
            
                $this->Session->setFlash(__('<b>Password Mismatch.</b> Please try entering your new password again.'), 'flash/error');
                return $this->redirect(array(
                    'action'=>'chooseNewPass',$reset_token, 
                    '?'=>array('source'=>'action')));
            }
        }   // End Post     
        else{
            $source = $this->request->query('source');
            $this->set('source', $source);
            
            //$this->log($this->request);
            $this->set('reset_token', $reset_token); // Else, pass the hash to the view, so it can be passed again when form is submitted
            
            if(!$reset_token){
                $this->Session->setFlash('<b>Invalid Token (No Token).</b> The token you supplied is incorrect. Please double check the reset URL you were emailed or request a new token.', 'flash/error');
                return $this->redirect(array('controller'=>'users', 'action'=>'forgotPassword'));
            }

            $trial_hash = Security::hash($reset_token, 'sha1', true);
            
            // Find corresponding user
            $rs = $this->User->find('first', array(
                'conditions'=>array(
                    'pass_reset_token' => $trial_hash
                )
            ));
            
            if(!$rs){
                $this->Session->setFlash('<b>Invalid Token (Token Not Found).</b> The token you supplied is incorrect. Please double check the reset URL you were emailed or request a new token.', 'flash/error');
                return $this->redirect(array('controller'=>'users', 'action'=>'forgotPassword'));
            }
            elseif ($rs && !$source){
                $this->Session->setFlash('<b>Token Accepted.</b> You may proceed to set your new password.', 'flash/success');
            }
    
            $this->set('reset_token', $reset_token);
        } // End not post
        
        $this->render('password_choose_new');
    }
    
    public function resendWelcomeEmail($user){
        $token = $this->User->setNewPasswordResetToken($user);
        
        if(!$token){
            $this->Session->setFlash('Error creating token. Please try again.', 'flash/error');
            return $this->redirect(array('controller'=>'users', 'action'=>'index'));
        }

        $status = $this->User->sendWelcomeEmail($user, $token);
        if($status){
            $this->User->updateLastWelcomeByUser($user);
            $this->Session->setFlash('Welcome Email Sent to <b>'.$status.'</b>', 'flash/success');
            return $this->redirect(array('controller'=>'users', 'action'=>'index'));
        }
        else{
            $this->Session->setFlash('Email could not be sent. Verify User status and password reset token.', 'flash/error');
            return $this->redirect(array('controller'=>'users', 'action'=>'index'));
        }
    }

    public function changeTeam($team){
        if($team == 0){
            $userTeams = $this->User->TeamsUser->getTeamsByUser(AuthComponent::user('id'));
            $userTeamCodes = $this->User->TeamsUser->getTeamCodesByUser(AuthComponent::user('id'));
            $userTeamsList = $this->User->TeamsUser->getControlledTeamsList(AuthComponent::user('id'));
            $this->Session->write('Auth.User.TeamsList', $userTeamsList);
            $this->Session->write('Auth.User.Teams', $userTeams);
            $this->Session->write('Auth.User.TeamCodes', $userTeamCodes);
            $this->Session->write('Auth.User.TeamsByZone', $this->User->TeamsUser->Team->listControlledTeamCodeByCategory());
            $this->Session->write('Auth.User.Settings.main_team_code', null);
            $this->Session->write('Auth.User.Settings.main_team_id', null);
            $this->Session->write('Auth.User.Compile.Teams', $userTeams);
            //$this->Session->setFlash('Team reset to default', 'flash/success');
        }
        elseif($team != 0){
            $tcode = $this->User->TeamsUser->Team->getTeamCodeByTeamId($team);
            $this->Session->write('Auth.User.Settings.main_team_code', $tcode);
            $this->Session->write('Auth.User.Settings.main_team_id', $team);
            $this->Session->write('Auth.User.Compile.Teams', array($team));
            //$this->Session->setFlash('Team view set to<b> '.$tcode.'</b>', 'flash/success');
        }
        
        return $this->redirect(array('controller'=>'tasks', 'action'=>'compile'));
    }
/*
    public function test()
    {
        $rs = $this->zoneTeamCodeList();
        $this->set('data', $rs);
        $this->render('/Elements/debug');
    }
*/

///EOF
}
///EOF