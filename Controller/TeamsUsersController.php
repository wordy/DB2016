<?php
App::uses('AppController', 'Controller');
/**
 * TeamsUsers Controller
 *
 * @property TeamsUser $TeamsUser
 * @property PaginatorComponent $Paginator
 */
class TeamsUsersController extends AppController {

/**
 * Components
 *
 * @var array
 */
	//public $components = array('Paginator');
	public $components = array('Paginator','RequestHandler');
	    //var $components = array('RequestHandler');


/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->TeamsUser->recursive = 0;
		$this->set('teamsUsers', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->TeamsUser->exists($id)) {
			throw new NotFoundException(__('Invalid teams user'));
		}
		$options = array('conditions' => array('TeamsUser.' . $this->TeamsUser->primaryKey => $id));
		$this->set('teamsUser', $this->TeamsUser->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->TeamsUser->create();
			if ($this->TeamsUser->save($this->request->data)) {
			    $this->Session->setFlash(__('User added to team.'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
			    
				$this->Session->setFlash(__('User couldn\'t be added to team. Check if they\'re already a member.'), 'flash/error');
			}
		}
		$teams = $this->TeamsUser->Team->find('list');
		$users = $this->TeamsUser->User->find('list');
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
		if (!$this->TeamsUser->exists($id)) {
			throw new NotFoundException(__('Invalid teams user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->TeamsUser->save($this->request->data)) {
				$this->Session->setFlash(__('The teams user has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The teams user could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('TeamsUser.' . $this->TeamsUser->primaryKey => $id));
			$this->request->data = $this->TeamsUser->find('first', $options);
		}
		$teams = $this->TeamsUser->Team->find('list');
		$users = $this->TeamsUser->User->find('list');
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
		$this->TeamsUser->id = $id;
		if (!$this->TeamsUser->exists()) {
			throw new NotFoundException(__('Invalid teams user'));
		}
		if ($this->TeamsUser->delete()) {
			$this->Session->setFlash(__('Teams user deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Teams user was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	public function ajaxAdd() {
		if ($this->RequestHandler->isAjax()) {
			Configure::write('debug', 0);
			$this->layout = 'ajax';
            if (!empty($this->data)) {
			
                $this->TeamsUser->create();
                $this->TeamsUser->set($this->data['TeamsUser']);
                if($this->TeamsUser->validates()) {
                    if ($this->TeamsUser->save($this->data)) {
                        $message = __('The TU has been saved.', true);
                        $data = $this->data;
                        $this->set('success', compact('message', 'data'));
                    }
                } else {
                    $message = __('The TU could not be saved. Please, try again.', true);
                    $TeamsUser = $this->TeamsUser->invalidFields();
                    $data = compact('TeamsUser');
                    $this->set('errors', compact('message', 'data','TeamsUser'));
                }
            }
        }
		
		else{
	
		$teams = $this->TeamsUser->Team->find('list');
		$users = $this->TeamsUser->User->find('list');
		$this->set(compact('teams', 'users'));
		
		$this->render('add2');
		
		}
	}
	

	
	
	

}
