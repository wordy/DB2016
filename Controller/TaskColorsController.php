<?php
App::uses('AppController', 'Controller');
/**
 * TaskColors Controller
 *
 * @property TaskColor $TaskColor
 * @property PaginatorComponent $Paginator
 */
class TaskColorsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
    
    public function isAuthorized($user) {
        // Default allows (logged in users)
        if (in_array($this->action, array(
            'makeCodeNameList',
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
	    $this->Paginator->settings = array(
                'TaskColor'=>array(
                    'limit'=>100,
                    'order'=>'TaskColor.name ASC',
            ));
        
		$this->TaskColor->recursive = 0;
		$this->set('taskColors', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->TaskColor->exists($id)) {
			throw new NotFoundException(__('Invalid task color'));
		}
		$options = array('conditions' => array('TaskColor.' . $this->TaskColor->primaryKey => $id));
		$this->set('taskColor', $this->TaskColor->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
        if ($this->request->is('post')) {
            $this->TaskColor->create();
            if ($this->TaskColor->save($this->request->data)) {
                $this->Session->setFlash(__('The task color has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The task color could not be saved. Please, try again.'), 'flash/error');
            }
        }
    }
    
    public function ajaxAdd() {
        if ($this->request->is('post')) {
            $this->TaskColor->create();
            if ($this->TaskColor->save($this->request->data)) {
                //$this->Session->setFlash(__('The task color has been saved'), 'flash/success');
                //$this->redirect(array('action' => 'index'));
                return $this->response->statusCode(200);
                
            } else {
                $this->response->statusCode(400);
                $this->Session->setFlash(__('The task color could not be saved. Please, try again.'), 'flash/error');
            }
        }
        $this->render('ajax_add');
    }

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->TaskColor->exists($id)) {
			throw new NotFoundException(__('Invalid task color'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->TaskColor->save($this->request->data)) {
				$this->Session->setFlash(__('The task color has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The task color could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('TaskColor.' . $this->TaskColor->primaryKey => $id));
			$this->request->data = $this->TaskColor->find('first', $options);
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
		$this->TaskColor->id = $id;
		if (!$this->TaskColor->exists()) {
			throw new NotFoundException(__('Invalid task color'));
		}
		if ($this->TaskColor->delete()) {
			$this->Session->setFlash(__('Task color deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Task color was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}

    public function makeCodeNameList(){
        $rs = $this->TaskColor->makeCodeAndNameList();
        return $rs;
    }
    
    
    public function getAvailColorsList(){
        
        $tc = $this->TaskColor->getAvailColorsList();
          
        $this->set('data', $tc);
        
        $this->render('/Elements/utility/debug');
        
    
    }
    
    
    
    
    
    
    
    
    
    
    

}
