<?php
App::uses('AppController', 'Controller');
/**
 * Zones Controller
 *
 * @property Zone $Zone
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ZonesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Zone->recursive = 1;
		$this->set('zones', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Zone->exists($id)) {
			throw new NotFoundException(__('Invalid zone'));
		}
		$options = array('conditions' => array('Zone.' . $this->Zone->primaryKey => $id));
		$this->set('zone', $this->Zone->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Zone->create();
			if ($this->Zone->save($this->request->data)) {
				$this->Session->setFlash(__('The zone has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The zone could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$users = $this->Zone->User->find('list');
		$this->set(compact('users'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Zone->exists($id)) {
			throw new NotFoundException(__('Invalid zone'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Zone->save($this->request->data)) {
				$this->Session->setFlash(__('The zone has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The zone could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Zone.' . $this->Zone->primaryKey => $id));
			$this->request->data = $this->Zone->find('first', $options);
		}
		$users = $this->Zone->User->find('list');
		$this->set(compact('users'));
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
		$this->Zone->id = $id;
		if (!$this->Zone->exists()) {
			throw new NotFoundException(__('Invalid zone'));
		}
		if ($this->Zone->delete()) {
			$this->Session->setFlash(__('Zone deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Zone was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
    
    
    public function ztlist(){
        $rs = $this->Zone->find('all', array(
            'contain'=>array('Team'),
            
        ));
        
        $this->set('data', $rs);
        
        $this->render('/Elements/Utility/debug');
    }
    

    
    
    
    
    
    
    
    
    
    
    
    
    
}
