<?php
App::uses('AppController', 'Controller');
/**
 * Roles Controller
 *
 * @property Role $Role
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class RolesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash');


    public function isAuthorized($user) {
        //$this->log($user);
        // Default allows (logged in users)
        if (in_array($this->action, array(
            //'index',
            //'add',
            //'view', 
            
            //2018
            'manage', 
            ))) {
            return true;
        }
        
        // The owner of a task can edit and delete it
        if (in_array($this->action, array(
                'edit', 
                'delete',
            ))){
                $role_id = $this->request->params['pass'][0];
            
        }

        return parent::isAuthorized($user);
    }








/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Role->recursive = 0;
        $this->Paginator->settings = array(
            'order'=>array('Role.team_id'=> 'asc','Role.handle'=>'asc'),
            'limit'=>200
        );
		$this->set('roles', $this->paginate());
	}
    
    public function manage() {
        $contain = array('Team'=>array('fields'=>array('Team.id','Team.code')));
        $order = array('Team.code'=> 'asc','Role.handle'=>'asc');
        $fields = array('Role.id','Role.handle','Role.team_id');
        
        if ($this->request->is('post')) {
            $this->Role->create();
            if ($this->Role->save($this->request->data)) {
                $this->response->statusCode(200);

                $rs = $this->Role->find('all', array('contain'=>$contain, 'order'=>$order, 'fields'=>$fields));
        
                $this->set('roles', $rs);
                
                if($this->request->is('ajax')){
                    $this->autoLayout = false;
                    $this->autoRender = false;
                    return $this->render('/Elements/role/ajax_manage_roles_table');
               }
            } else {
                    $errors = $this->Role->validationErrors;
                    $emsg = Hash::extract($errors,'{s}.{n}');
                    $this->response->statusCode(400);
                    $this->set('message', __('The role could not be saved due to these errors:'));
                    $this->set('errors', $emsg);
                    return $this->render('/Elements/task/validation_error');
            }
        }

        $rs = $this->Role->find('all', array('contain'=>$contain, 'order'=>$order, 'fields'=>$fields));
        $this->set('roles', $rs);
        $this->render('manage');
    }
/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Role->exists($id)) {
			throw new NotFoundException(__('Invalid role'));
		}
		$options = array('conditions' => array('Role.' . $this->Role->primaryKey => $id));
		$this->set('role', $this->Role->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Role->create();
			if ($this->Role->save($this->request->data)) {
				$this->Session->setFlash(__('The role has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The role could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$teams = $this->Role->Team->find('list');
		$users = $this->Role->User->find('list');
		$this->set(compact('teams', 'users'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Role->exists($id)) {
			throw new NotFoundException(__('Invalid role'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Role->save($this->request->data)) {
				$this->Session->setFlash(__('The role has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
			else {
                $errors = $this->Role->validationErrors;
                $emsg = Hash::extract($errors,'{s}.{n}');
                //$this->response->statusCode(400);
                $this->set('message', __('The role could not be saved due to these errors:'));
                $this->set('errors', $emsg);
                //return $this->render('/Elements/task/validation_error');
                
				$this->Session->setFlash(__('The role could not be saved. Please, try again.'), 'flash/error_list');
			}
		} else {
			$options = array('conditions' => array('Role.' . $this->Role->primaryKey => $id));
			$this->request->data = $this->Role->find('first', $options);
		}
		$teams = $this->Role->Team->find('list');
		$users = $this->Role->User->find('list');
		$this->set(compact('teams', 'users'));
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
		$this->Role->id = $id;
		if (!$this->Role->exists()) {
			throw new NotFoundException(__('Invalid role'));
		}
		if ($this->Role->delete()) {
			$this->Session->setFlash(__('Role deleted'), 'flash/success');
			//$this->redirect(array('action' => 'index'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('Role was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}


    public function rebuildRoles(){
        $rs = $this->Role->Team->TeamsUser->getAll();
        
        $rs1 = array();
        
        foreach ($rs as $k=>$tus){
            $un = $this->Role->Team->TeamsUser->User->getUsernameByUser($tus['TeamsUser']['user_id']);
            
            if(!$this->Role->existsByTeamAndUser($tus['TeamsUser']['team_id'], $tus['TeamsUser']['user_id'])){
                $this->Role->setByUsernameTeamUser($un, $tus['TeamsUser']['team_id'], $tus['TeamsUser']['user_id']);
            }
        }
        
        $this->autoRender = false;
        
        //$this->log($rs1);
        
    }

    public function getByTeams(){
        $AUTH_teams = AuthComponent::user('Teams');
        
        $rs = $this->Role->getByTeams($AUTH_teams);
        
        $data = array($rs,$AUTH_teams);
        
        $this->set('data',$data);
        $this->render('/Elements/Utility/debug');
        
    }








}//EOF
