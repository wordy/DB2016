<?php
App::uses('AppController', 'Controller');
/**
 * PrintPrefs Controller
 *
 * @property PrintPref $PrintPref
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class PrintPrefsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');
    
    
        public function isAuthorized($user) {
        
        //$this->log($user);
        // Default allows (logged in users)
        if (in_array($this->action, array(
            //2015
            'changePref',
            'resetPref',
            ))) {
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
		$this->PrintPref->recursive = 0;
		$this->set('PrintPrefs', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->PrintPref->exists($id)) {
			throw new NotFoundException(__('Invalid user ignore'));
		}
		$options = array(
		  'contain'=>array(
            'User','Task'),
		  'conditions' => array(
		      'PrintPref.' . $this->PrintPref->primaryKey => $id));
		$this->set('PrintPref', $this->PrintPref->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->PrintPref->create();
			if ($this->PrintPref->save($this->request->data)) {
				$this->Session->setFlash(__('The user ignore has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user ignore could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$users = $this->PrintPref->User->find('list');
		$tasks = $this->PrintPref->Task->find('list');
		$this->set(compact('users', 'tasks'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->PrintPref->exists($id)) {
			throw new NotFoundException(__('Invalid user ignore'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->PrintPref->save($this->request->data)) {
				$this->Session->setFlash(__('The user ignore has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user ignore could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('PrintPref.' . $this->PrintPref->primaryKey => $id));
			$this->request->data = $this->PrintPref->find('first', $options);
		}
		$users = $this->PrintPref->User->find('list');
		$tasks = $this->PrintPref->Task->find('list');
		$this->set(compact('users', 'tasks'));
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
		$this->PrintPref->id = $id;
		if (!$this->PrintPref->exists()) {
			throw new NotFoundException(__('Invalid user ignore'));
		}
		if ($this->PrintPref->delete()) {
			$this->Session->setFlash(__('User ignore deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User ignore was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
 
    public function changePref(){
        if ($this->request->is('post')) {
            
            $user_id = $this->Auth->user('id');
            $tid = $this->request->data('task');
            $hd = ($this->request->data('hide_detail'))? $this->request->data('hide_detail'): 0;
            $ht = ($this->request->data('hide_task'))? $this->request->data('hide_task'): 0;
            
            // Both 0 is equivalent to no preference
            if($ht == 0 && $hd == 0){
                if($this->PrintPref->delAllByUserTask($user_id, $tid)){
                    $data = array(
                        'success'=>true,
                        'hide_task'=>0,
                        'hide_detail'=>0,
                        'message'=>'Print preferences reset.',
                        );
                    
                    return json_encode($data);
                }    
            }
           
            $old_pref = $this->PrintPref->find('first', array(
                'conditions'=> array(
                    'user_id'=>$user_id, 
                    'task_id'=>$tid)));
            
            $data = array();
            
            // Old rec exists so update it by setting an ID. If no ID is set, a new rec is created
            if(!empty($old_pref)){
                $data['PrintPref']['id'] = $old_pref['PrintPref']['id'];     
            }
            
            // If task is hidden, details are hidden too
            if($ht == 1){ $hd = 0;}
            
            $data['PrintPref']=array(
                'hide_task'=>$ht,
                'hide_detail'=>$hd,
                'user_id'=>$user_id,
                'task_id'=>$tid
            );
            
            if($this->PrintPref->save($data)){
               $data = array(
                    'success'=>true,
                    'hide_detail'=>$hd,
                    'hide_task'=>$ht,
                    'message'=>'Preference updated.',
                    );
                $this->response->statusCode(200);
                return json_encode($data); 
                
            }
            else{
                $this->response->type('json');
                $this->response->statusCode(401);
                $this->response->body(json_encode(array(
                    'success' => false, 
                    'message' => "Sorry, your preference could not be saved. Please try again.")));
                $this->response->send();
                $this->_stop();    
            }
        }
    }

    public function resetPref(){
        $user_id = $this->Auth->user('id');
        
        if($this->PrintPref->delAllByUser($user_id)){
            $this->Session->setFlash(__('Your preferences were reset.'), 'flash/success', array(), 'print_pref');
            $this->redirect(array('controller'=>'tasks','action' => 'userPrint'));    
        }
        else{
            $this->Session->setFlash(__('Could not reset your preferences. Please try again.'), 'flash/error', array(), 'print_pref');
            $this->redirect(array('controller'=>'tasks','action' => 'userPrint')); 
        }
    }
  
    
    
    
    
    
    
    
    
    
}
