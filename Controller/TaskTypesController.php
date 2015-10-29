<?php
App::uses('AppController', 'Controller');
/**
 * TaskTypes Controller
 *
 * @property TaskType $TaskType
 * @property PaginatorComponent $Paginator
 */
class TaskTypesController extends AppController {
    
    public $components = array('Paginator');
    
/**
 * Components
 *
 * @var array
 */
	

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
    
        // If we get here, definitely not authorized...    
        //$this->Session->setFlash(__('Sorry, you\'re not authorized to access that. Your permissions don\'t allow you to modify that task (TasksController). '), 'flash/auth_error');
        //$this->redirect(array('controller' => 'tasks', 'action' => 'home'));
        return parent::isAuthorized($user);
        //$this->redirect($this->referer());

        //return false;
    }

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->TaskType->recursive = 0;
		$this->set('taskTypes', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->TaskType->exists($id)) {
			throw new NotFoundException(__('Invalid task type'));
		}
		$options = array('conditions' => array('TaskType.' . $this->TaskType->primaryKey => $id));
		$this->set('taskType', $this->TaskType->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->TaskType->create();
			if ($this->TaskType->save($this->request->data)) {
				$this->Session->setFlash(__('The task type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The task type could not be saved. Please, try again.'), 'flash/error');
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
		if (!$this->TaskType->exists($id)) {
			throw new NotFoundException(__('Invalid task type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->TaskType->save($this->request->data)) {
				$this->Session->setFlash(__('The task type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The task type could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('TaskType.' . $this->TaskType->primaryKey => $id));
			$this->request->data = $this->TaskType->find('first', $options);
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
		$this->TaskType->id = $id;
		if (!$this->TaskType->exists()) {
			throw new NotFoundException(__('Invalid task type'));
		}
		if ($this->TaskType->delete()) {
			$this->Session->setFlash(__('Task type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Task type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}

    public function makeList(){
        $rs = $this->TaskType->makeListByCategory();
        return $rs;
    }



}
