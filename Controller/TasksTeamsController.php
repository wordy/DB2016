<?php
App::uses('AppController', 'Controller');
/**
 * TasksTeams Controller
 *
 * @property TasksTeam $TasksTeam
 * @property PaginatorComponent $Paginator
 */
class TasksTeamsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

    public function isAuthorized($user) {
        // Default allows (logged in users)
        if (in_array($this->action, array(
            //'index',
            //'allowedAssistTeams',
            'openRequest',
            'closeRequest',
            'chgRole',
            'pushOnly',
            
            //2016
            'updateSig',
            'getLinkableParents',
            
            ))) {
            return true;
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
            
            'order' => 'TasksTeam.id DESC'
    );
        
		$this->TasksTeam->recursive = 0;

		$this->set('tasksTeams', $this->paginate());
	}
    
    public function byTask($task=null) {
         $this->Paginator->settings = array(
            'conditions'=>array(
                'TasksTeam.task_id'=>$task    
            ),       
            'order' => array('TasksTeam.task_role_id ASC',
            'TasksTeam.team_code ASC')
    );
        
        $this->TasksTeam->recursive = 0;

        $this->set('tasksTeams', $this->paginate());
        
        $this->render('by_task');
    }

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->TasksTeam->exists($id)) {
			throw new NotFoundException(__('Invalid tasks team'));
		}
		$options = array('recursive'=>1,'conditions' => array('TasksTeam.' . $this->TasksTeam->primaryKey => $id));
		$this->set('tasksTeam', $this->TasksTeam->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->TasksTeam->create();
			if ($this->TasksTeam->save($this->request->data)) {
				$this->Session->setFlash(__('The tasks team has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('That association couldn\'t be saved.  Maybe it already existed?  Please try again.'), 'flash/error');
			}
		}
		$tasks = $this->TasksTeam->Task->find('list');
		$teams = $this->TasksTeam->Team->find('list');
		$taskRoles = $this->TasksTeam->TaskRole->find('list');
		$this->set(compact('tasks', 'teams', 'taskRoles'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->TasksTeam->exists($id)) {
			throw new NotFoundException(__('Invalid tasks team'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->TasksTeam->save($this->request->data)) {
				$this->Session->setFlash(__('The tasks team has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The tasks team could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('TasksTeam.' . $this->TasksTeam->primaryKey => $id));
			$this->request->data = $this->TasksTeam->find('first', $options);
		}
		$tasks = $this->TasksTeam->Task->find('list');
		$teams = $this->TasksTeam->Team->find('list');
		$taskRoles = $this->TasksTeam->TaskRole->find('list');
		$this->set(compact('tasks', 'teams', 'taskRoles'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->TasksTeam->id = $id;
		if (!$this->TasksTeam->exists()) {
			throw new NotFoundException(__('Invalid tasks team'));
		}
		if ($this->TasksTeam->delete()) {
			$this->Session->setFlash(__('Tasks team deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Tasks team was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}

/*
    public function parentRequestsByTeam($team_id){
        $rs = $this->TasksTeam->find('all', array(
            'contain'=>array(
                'Task'    
            
            ),
            'conditions'=>array(
                'TasksTeam.team_id'=>$team_id,
                'TasksTeam.task_role_id'=>10,
                'TasksTeam.is_pending'=>1)
        ));
        
        $this->set('data', $rs);
        $this->render('/Elements/utility/debug');
        
    }
    
    public function getLinkableParents($team){
        $rs = $this->TasksTeam->getLinkableParentsByTeam($team);
        
        $this->set('data', $rs);
        $this->render('/Elements/utility/debug');
    }
*/
    public function closeRequest(){
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    
        if($this->request->is('ajax')){
            $task = $this->request->data('task');
            $team = $this->request->data('team');
            
            if(!$task || !$team){
                return false;
            }
            
            //$cts = $this->Session->read('Auth.User.Timeshift');
            $user = $this->Session->read('Auth.User');
            
            // Verify user owns the task to be added
            if($this->TasksTeam->Task->isControlledBy($task, $user)){
                $rs = array('TasksTeam'=>array(
                'task_id'=>(int)$task,
                'team_id'=>(int)$team,
                'task_role_id'=>4));
                
                //$this->log($rs);
                
                if($this->TasksTeam->save($rs)){
                  //  $this->log('tried to save');
                    $data = array(
                        'success'=>true,
                        'message'=>"Request Closed",
                        'tr_id'=>4,
                        );
                    return json_encode($data);
                }
                    
            }
            else{ //Does not control
                $this->response->type('json');
                $this->response->statusCode(401);
                
                $this->response->body(json_encode(array(
                    'success' => false, 
                    'message' => "Sorry, your permissions don't allow you to close that request")));
                //$this->_stop();
            }
        }
        
     //$this->render('time_shift');
    }


    public function openRequest(){
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    
        if($this->request->is('ajax')){
            $task = $this->request->data('task');
            $team = $this->request->data('team');
            
            if(!$task || !$team){
                return false;
            }
            
            //$cts = $this->Session->read('Auth.User.Timeshift');
            $user = $this->Session->read('Auth.User');
            
            // Verify user owns the task to be added
            if($this->TasksTeam->Task->isControlledBy($task, $user)){
                $rs = array('TasksTeam'=>array(
                    'task_id'=>(int)$task,
                    'team_id'=>(int)$team,
                    'task_role_id'=>3));
                
                //$this->log($rs);
                
                if($this->TasksTeam->save($rs)){
                  //  $this->log('tried to save');
                    $data = array(
                        'success'=>true,
                        'message'=>"Request Opened",
                        'tr_id'=>3,
                        );
                    return json_encode($data);
                }
                    
            }
            else{ //Does not control
                $this->response->type('json');
                $this->response->statusCode(401);
                
                $this->response->body(json_encode(array(
                    'success' => false, 
                    'message' => "Sorry, your permissions don't allow you to open that request")));
                //$this->_stop();
            }
        }
        
     //$this->render('time_shift');
    }

    public function pushOnly(){
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    
        if($this->request->is('ajax')){
            $task = $this->request->data('task');
            $team = $this->request->data('team');
            
            if(!$task || !$team){
                return false;
            }
            
            //$cts = $this->Session->read('Auth.User.Timeshift');
            $user = $this->Session->read('Auth.User');
            
            // Verify user owns the task to be added
            if($this->TasksTeam->Task->isControlledBy($task, $user)){
                $rs = array('TasksTeam'=>array(
                    'task_id'=>(int)$task,
                    'team_id'=>(int)$team,
                    'task_role_id'=>2));
                
                //$this->log($rs);
                
                if($this->TasksTeam->save($rs)){
                  //  $this->log('tried to save');
                    $data = array(
                        'success'=>true,
                        'message'=>"Pushed to team",
                        'tr_id'=>2,
                        );
                    return json_encode($data);
                }
                    
            }
            else{ //Does not control
                $this->response->type('json');
                $this->response->statusCode(401);
                
                $this->response->body(json_encode(array(
                    'success' => false, 
                    'message' => "Sorry, your permissions don't allow you to push that task")));
                //$this->_stop();
            }
        }
        
     //$this->render('time_shift');
    }
    public function allowedAssistTeams($lead=null){
        $teams = $this->TasksTeam->Team->listLeadAndPotentialAssist($lead);
        $this->set('new_teams', $teams);
        
        if (!empty($this -> request -> params['requested'])){
            return $teams; 
        }        
        $this->render('/Elements/tasks_team/new_team_list');
    }

    public function updateSig($lead = null, $task = null){
        if(!$this->request->is('post')){
            //throw new MethodNotAllowedException('You can\'t access that function from here.');
        }
        
        if($this->request->data){
            $lead = $this->request->data('team');
            $task = $this->request->data('task');
        }
        
        $allowTRoles = $this->TasksTeam->getPossibleRolesByTask($lead, $task);
            
        $this->set('teamsRoles', $allowTRoles);

        if (!empty($this -> request -> params['requested'])){
            return $allowTRoles; 
        }        

        $this->render('/Elements/tasks_team/tt_signature');
    }    
    
    public function chgRole(){
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        
        $updated = false;
    
        if($this->request->is('ajax')){
            $task = $this->request->data('task');
            $team = $this->request->data('team');
            $role = $this->request->data('role');
            
            if(!isset($task) || !isset($team) || !isset($role)){
                $this->response->statusCode(401);
                $this->response->body(json_encode(array(
                    'success' => false, 
                    'message' => "A task, team, and role must be specified")));
               //$this->response->send();
               return false;
            }
            
            $user = $this->Session->read('Auth.User');
            $old_role = $this->TasksTeam->getTeamRoleByTask($team, $task);

            // Verify user owns the task
            if($this->TasksTeam->Task->isControlledBy($task, $user)){
                
                // Had role, and new role is different
                if((!empty($old_role)) && ($old_role != $role)){
                    $rs = $this->TasksTeam->findByTaskIdAndTeamId($task, $team);
                    //$this->log('rs in ttcontroller before saving -- chgRole Action');
                    //$this->log($rs);
                    $rs['TasksTeam']['task_role_id'] = $role;
                    //unset($rs['TasksTeam']['created']);
                    //unset($rs['TasksTeam']['modified']);
                    //unset($rs['TasksTeam']['team_code']);
                    //$this->TasksTeam->id = $rs['TasksTeam']['id'];
                    //$this->log($rs);
                    if($this->TasksTeam->save($rs)){
                        //$this->log($this->TasksTeam->data);
                        $updated = true;
                        //$this->TasksTeam->Task->Change->changeTeamRole($task, $team, $old_role, $role);
                    }
                    
                    
                }
                else{
                    $rs = array(
                        'TasksTeam'=>array(
                            'task_id'=>(int)$task,
                            'team_id'=>(int)$team,
                            'task_role_id'=>(int)$role
                        )
                    );

                    if($this->TasksTeam->save($rs)){
                        $updated = true;
                    }
                }
                
                if($updated){
                    $this->response->type('json');
                    $this->response->statusCode(200);
                    $this->response->body(json_encode(array(
                        'success' => true,
                        'tr_id'=>$role, 
                        'message' => "Team's role successfully changed")));
                    //$this->response->send();
                }
                

            }
            else{ // Does not control
                $this->response->type('json');
                $this->response->statusCode(401);
                $this->response->body(json_encode(array(
                    'success' => false, 
                    'message' => "Your permissions don't allow you to change that team's role")));
            }
        }
    }

    function getTeamRoleByTask($team, $task){
        $rs = $this->TasksTeam->getTeamRoleByTask($team,$task); 
        
        //$this->findByTeamIdAndTaskId($team, $task);
        
        $this->set('data', $rs);
        $this->render('/Elements/Utility/debug');
    }
    



}
