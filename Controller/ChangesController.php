<?php
App::uses('AppController', 'Controller');
/**
 * Changes Controller
 *
 * @property Change $Change
 * @property PaginatorComponent $Paginator
 */
class ChangesController extends AppController {
    

/**
 * Components
 * @var array
 */
	public $components = array('Paginator');
    public $helpers = array('Js');
    
    public $paginate = array(
        'Change'=>array(
        'order'=>array(
            'Change.id'=> 'DESC')));

    public function isAuthorized($user) {

        // Allowed for all logged in users
        if (in_array($this->action, array(
            'pageChanges',
            'add',
            ))){
                
            return true;
        }
        
        // If we get here, not authorized        
        //$this->Session->setFlash(__('Sorry, you\'re not authorized to access that.'), 'flash/auth_error');
        return parent::isAuthorized($user);
        }
   



/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Change->recursive = 0;
        $this->Paginator->settings = $this->paginate;
		$this->set('changes', $this->paginate('Change'));
	}

/**
 * view method
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Change->exists($id)) {
			throw new NotFoundException(__('Invalid change'));
		}
		$options = array('recursive'=>1, 'conditions' => array('Change.' . $this->Change->primaryKey => $id));
		$this->set('change', $this->Change->find('first', $options));
	}

/**
 * add method
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Change->create();
			if ($this->Change->save($this->request->data)) {
				$this->Session->setFlash(__('The change has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The change could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$tasks = $this->Change->Task->find('list');
        $users = $this->Change->User->find('list');
        $changeTypes = $this->Change->ChangeType->find('list');
		$this->set(compact('tasks', 'users', 'changeTypes'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Change->exists($id)) {
			throw new NotFoundException(__('Invalid change'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Change->save($this->request->data)) {
				$this->Session->setFlash(__('The change has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The change could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Change.' . $this->Change->primaryKey => $id));
			$this->request->data = $this->Change->find('first', $options);
		}
		$tasks = $this->Change->Task->find('list');
		$changeTypes = $this->Change->ChangeType->find('list');
        $teams = $this->Change->Task->Team->find('list');
        $users = $this->Change->User->find('list');
		$this->set(compact('tasks', 'changeTypes','teams','users'));
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
		$this->Change->id = $id;
		if (!$this->Change->exists()) {
			throw new NotFoundException(__('Invalid change'));
		}
		if ($this->Change->delete()) {
			$this->Session->setFlash(__('Change deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Change was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
    
    // Used in task_view_nav_nav_buttons
    public function pageChanges($task_id = null){

        $this->Paginator->settings = array(
            'Change'=>array(
                'limit'=>5,
                'conditions'=>array(
                    'Change.task_id'=>$task_id),
                'order'=>'Change.created DESC'
            )
        );

        
        
        
        //$this->log($paging);
        $changes = $this->paginate('Change');
                $paging = $this->params['paging'];
                
                //$paging['Change']['options']['model'] = 'change';
        
        //$this->log('paging var from pageChagnes in controller');        
        //$this->log($paging);
        $this->set('changes', $changes);
        $this->set('task', $task_id);
        $this->set('paging', $paging);
        
        if (!empty($this -> request -> params['requested'])){ 
            ($this->request->params['paging'])? $paging = $this->request->params['paging']: $paging = array();
            
            return array('changes'=>$changes, 'paging' => $paging, 'task'=>$task_id);
        }

        if($this->request->is('ajax')){
            $paging = ($this->request->params['paging'])? array_merge($this->request->params['paging'], $this->params['paging']): array();
            
            return $this->render('/Elements/change/changes_by_task');
            //return;
        }
            $this->render('/Elements/change/changes_by_task');
        
    }

    function recent($team, $task){
        $rs = $this->Change->getRecentRoleChangesByTeamAndTask($team, $task);
        
        $this->set('data', $rs);
        
        $this->render('/Elements/Utility/debug');
        
    }

    function recentChildren($team){
        $this->set('data', $this->Change->getRecentChildrenByTeam($team));
        
        $this->render('/Elements/Utility/debug');
    }



///////// EOF
}
///////// EOF