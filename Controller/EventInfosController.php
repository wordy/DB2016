<?php
App::uses('AppController', 'Controller');
/**
 * EventInfos Controller
 *
 * @property EventInfo $EventInfo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class EventInfosController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');

    public function isAuthorized($user) {
        // Default allows (logged in users)
        if(in_array($this->action, array('info', 'add'))){
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
		$this->EventInfo->recursive = 0;
		$this->set('eventInfos', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->EventInfo->exists($id)) {
			throw new NotFoundException(__('Invalid event info'));
		}
		$options = array('conditions' => array('EventInfo.' . $this->EventInfo->primaryKey => $id));
		$this->set('eventInfo', $this->EventInfo->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
	    //$this->log('hellllllllllo');
		if ($this->request->is('post') || $this->request->is('put')) {
                $this->EventInfo->create();
                if ($this->EventInfo->save($this->request->data)) {
                    
                    if($this->request->is('ajax')){
                        $this->response->statusCode(200);
                        $this->response->body(json_encode(array('success'=>true, 'message'=>'Info successfully added')));
                        //return $this->response->body(json_encode(array('success'=>false, 'message'=>'There was a problem adding new info. Please try again.')));
                    }
                    else{
                        $this->Session->setFlash(__('The event info has been saved'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    }
                    
                } else {
                    if($this->request->is('ajax')){
                        $this->response->statusCode(401);
                        $this->response->body(json_encode(array('success'=>false, 'message'=>'There was a problem adding new info. Please try again.')));
                        //return false;
                        
                    }
                    $this->Session->setFlash(__('The event info could not be saved. Please, try again.'), 'flash/error');
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
		if (!$this->EventInfo->exists($id)) {
			throw new NotFoundException(__('Invalid event info'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->EventInfo->save($this->request->data)) {
				$this->Session->setFlash(__('The event info has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The event info could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('EventInfo.' . $this->EventInfo->primaryKey => $id));
			$this->request->data = $this->EventInfo->find('first', $options);
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
		$this->EventInfo->id = $id;
		if (!$this->EventInfo->exists()) {
			throw new NotFoundException(__('Invalid event info'));
		}
		if ($this->EventInfo->delete()) {
			$this->Session->setFlash(__('Event info deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Event info was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
    
    
    public function info() {
        $this->request->data = $this->EventInfo->find('first', array('order'=>array('EventInfo.id DESC')));
        //$this->log($this->request->data);
    }
    
    
    
    
    
    
    
}
