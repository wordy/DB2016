<?php
App::uses('AppController', 'Controller');
/**
 * Actors Controller
 *
 * @property Actor $Actor
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class ActorsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Actor->recursive = 0;
		$this->set('actors', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Actor->exists($id)) {
			throw new NotFoundException(__('Invalid actor'));
		}
		$options = array('conditions' => array('Actor.' . $this->Actor->primaryKey => $id));
		$this->set('actor', $this->Actor->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Actor->create();
			if ($this->Actor->save($this->request->data)) {
				$this->Session->setFlash(__('The actor has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The actor could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$teams = $this->Actor->Team->find('list');
		$users = $this->Actor->User->find('list');
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
		if (!$this->Actor->exists($id)) {
			throw new NotFoundException(__('Invalid actor'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Actor->save($this->request->data)) {
				$this->Session->setFlash(__('The actor has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The actor could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Actor.' . $this->Actor->primaryKey => $id));
			$this->request->data = $this->Actor->find('first', $options);
		}
		$teams = $this->Actor->Team->find('list');
		$users = $this->Actor->User->find('list');
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
		$this->Actor->id = $id;
		if (!$this->Actor->exists()) {
			throw new NotFoundException(__('Invalid actor'));
		}
		if ($this->Actor->delete()) {
			$this->Session->setFlash(__('Actor deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Actor was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
