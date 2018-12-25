<?php
App::uses('AppController', 'Controller');
/**
 * Assignments Controller
 *
 * @property Assignment $Assignment
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class AssignmentsController extends AppController {

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
        /*
        if (in_array($this->action, array(
                'edit', 
                'delete',
            ))){
                $task_id = $this->request->params['pass'][0];
            
            if ($this->Assignment->isControlledBy($task_id, $user)) {
                return true;
            }
        }
*/
        return parent::isAuthorized($user);
    }


/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Assignment->recursive = 1;
		$this->set('assignments', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Assignment->exists($id)) {
			throw new NotFoundException(__('Invalid assignment'));
		}
		$options = array(
		      'conditions' => array(
		          'Assignment.' . $this->Assignment->primaryKey => $id
              ),
              'contain'=>array('Role', 'Task')
          );
		$this->set('assignment', $this->Assignment->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Assignment->create();
			if ($this->Assignment->save($this->request->data)) {
				$this->Session->setFlash(__('The assignment has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The assignment could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$roles = $this->Assignment->Role->find('list');
        
        foreach($roles as $id =>$n){
            $roles[$id] = '@'.$n;
        }
		$tasks = $this->Assignment->Task->allTasksList();
		$this->set(compact('roles', 'tasks'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Assignment->exists($id)) {
			throw new NotFoundException(__('Invalid assignment'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Assignment->save($this->request->data)) {
				$this->Session->setFlash(__('The assignment has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The assignment could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Assignment.' . $this->Assignment->primaryKey => $id));
			$this->request->data = $this->Assignment->find('first', $options);
		}
		$roles = $this->Assignment->Role->find('list');
		$tasks = $this->Assignment->Task->find('list');
		$this->set(compact('roles', 'tasks'));
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
		$this->Assignment->id = $id;
		if (!$this->Assignment->exists()) {
			throw new NotFoundException(__('Invalid assignment'));
		}
		if ($this->Assignment->delete()) {
			$this->Session->setFlash(__('Assignment deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Assignment was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
    
    public function getAll(){
        $rs = $this->Assignment->find('all', array(
            'contain'=>array(
                'Role', 
                'Task'=>array(
                    'fields'=>array(
                        'id', 
                        'team_code',
                        'start_time',
                        'end_time',
                        'task_type',
                        'short_description', 
                        'details'
                    )
                ), 
                'Task.TasksTeam'=>array(
                    'fields'=>array('team_code','task_role_id')
                )
            )
        ));
        
        $this->set('data', $rs);
        $this->render('/Elements/Utility/debug');
    }

    public function getByTeam($team=null){
         if ($this->request->is('ajax')) {
             $team = $this->request->query('team');
             $abyteam = $this->Assignment->Role->getListByTeam($team);
             
             $data = $tdata = array();
             
             foreach($abyteam as $team => $acts){
                 $tdata[$team][0]=array('id'=>0,'text'=>"<None>");
                 foreach($acts as $k =>$han){
                     $tdata[$team][] = array('id'=>(int)$k, 'text'=>$han); 
                 }
                 
                 $data[]=array('text'=>$team, 'children'=>$tdata[$team]);
             }
             return json_encode($data);
         }
    }    
    
    public function getByUser($team=null){
         if ($this->request->is('ajax')) {
             //$team = $this->request->query('team');
             $team = AuthComponent::user('Teams');
             $abyteam = $this->Assignment->Role->getListByTeam($team);
             
             $data = $tdata = array();
             
             foreach($abyteam as $team => $acts){
                 $tdata[$team][0]=array('id'=>0,'text'=>"<None>");
                 foreach($acts as $k =>$han){
                     $tdata[$team][] = array('id'=>(int)$k, 'text'=>$han); 
                 }
                 
                 $data[]=array('text'=>$team, 'children'=>$tdata[$team]);
             }
             return json_encode($data);
         }
    }    
    
    public function getByRoles($roles){
        $this->set('data', array($this->Assignment->getTaskIdsByRoles($roles)));
        $this->render('/Elements/debug');
    }
    
    
    
    
}
