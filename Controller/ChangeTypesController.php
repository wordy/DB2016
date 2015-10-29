<?php
App::uses('AppController', 'Controller');
/**
 * ChangeTypes Controller
 *
 * @property ChangeType $ChangeType
 * @property PaginatorComponent $Paginator
 */
class ChangeTypesController extends AppController {

/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array('Js');

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->ChangeType->recursive = 0;
		$this->set('changeTypes', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ChangeType->exists($id)) {
			throw new NotFoundException(__('Invalid change type'));
		}
		$options = array('conditions' => array('ChangeType.' . $this->ChangeType->primaryKey => $id));
		$this->set('changeType', $this->ChangeType->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ChangeType->create();
			if ($this->ChangeType->save($this->request->data)) {
				$this->Session->setFlash(__('The change type has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The change type could not be saved. Please, try again.'));
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
		if (!$this->ChangeType->exists($id)) {
			throw new NotFoundException(__('Invalid change type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ChangeType->save($this->request->data)) {
				$this->Session->setFlash(__('The change type has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The change type could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('ChangeType.' . $this->ChangeType->primaryKey => $id));
			$this->request->data = $this->ChangeType->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ChangeType->id = $id;
		if (!$this->ChangeType->exists()) {
			throw new NotFoundException(__('Invalid change type'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->ChangeType->delete()) {
			$this->Session->setFlash(__('The change type has been deleted.'));
		} else {
			$this->Session->setFlash(__('The change type could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}}
