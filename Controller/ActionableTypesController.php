<?php
App::uses('AppController', 'Controller');
/**
 * ActionTypes Controller
 *
 * @property ActionType $ActionType
 * @property PaginatorComponent $Paginator
 */
class ActionableTypesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
    public function isAuthorized($user) {
        // Default allows (logged in users)
        if (in_array($this->action, array(
            'makeList',
            ))) {
            return true;
        }
        
        // Team lead and above can add
        // Teams users can add a task to is restricted in Task->add() by role.
        /*
        if(in_array($this->action, array(
            'add'
            ))){
        
            if($user['user_role_id'] > 5){
                return true;
            }
        }
        
        // The owner of a post can edit and delete it
        if (in_array($this->action, array('edit', 'delete'))) {
            $task_id = $this->request->params['pass'][0];
            
            if ($this->Task->isControlledBy($task_id, $user)) {
                return true;
            }
            
         
        }*/
    
        return parent::isAuthorized($user);
    }


/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->ActionableType->recursive = 0;
		$this->set('actionableTypes', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ActionableType->exists($id)) {
			throw new NotFoundException(__('Invalid actionable type'));
		}
		$options = array('conditions' => array('ActionableType.' . $this->ActionableType->primaryKey => $id));
		$this->set('actionableType', $this->ActionableType->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ActionableType->create();
			if ($this->ActionableType->save($this->request->data)) {
				$this->Session->setFlash(__('The actionable type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The actionable type could not be saved. Please, try again.'), 'flash/error');
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->ActionableType->exists($id)) {
			throw new NotFoundException(__('Invalid actionable type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ActionableType->save($this->request->data)) {
				$this->Session->setFlash(__('The actionable type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The actionable type could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('ActionableType.' . $this->ActionableType->primaryKey => $id));
			$this->request->data = $this->ActionableType->find('first', $options);
		}
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
		$this->ActionableType->id = $id;
		if (!$this->ActionableType->exists()) {
			throw new NotFoundException(__('Invalid actionable type'));
		}
		if ($this->ActionableType->delete()) {
			$this->Session->setFlash(__('Actionable type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Actionable type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}

    public function makeList(){
        $rs = $this->ActionableType->makeList();
        return $rs;
    }










}
