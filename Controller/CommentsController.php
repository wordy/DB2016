<?php
App::uses('AppController', 'Controller');
/**
 * Comments Controller
 *
 * @property Comment $Comment
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CommentsController extends AppController {

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
            'pageComments',
            'byTask',
                        
            ))) {
            return true;
        }
        
        /*
        if (in_array($this->action, array(
                'edit', 
                'delete',
            ))){
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
		$this->Comment->recursive = 0;
		$this->set('comments', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Comment->exists($id)) {
			throw new NotFoundException(__('Invalid comment'));
		}
		$options = array('conditions' => array('Comment.' . $this->Comment->primaryKey => $id));
		$this->set('comment', $this->Comment->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Comment->create();
			if ($this->Comment->save($this->request->data)) {
				$this->Session->setFlash(__('The comment has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The comment could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$tasks = $this->Comment->Task->allTasksList();
		$users = $this->Comment->User->find('list');
		$this->set(compact('tasks', 'users'));
	}
    
    public function addTo() {
        if(!$this->request->is('ajax')){
            throw new MethodNotAllowedException();
        }

        if ($this->request->is('post')) {
            $this->Comment->create();
            if ($this->Comment->save($this->request->data)) {
                $this->response->statusCode(200);    
                $this->set('message', __('Comment added'));
                return $this->render('/Elements/task/validation_error');
            } else {
                $this->response->statusCode(400);
                $errors = $this->Comment->validationErrors;
                $emsg = Hash::extract($errors,'{s}.{n}');
                $this->autoLayout = false;
                $this->autoRender = false;
                $this->set('message', __('Your comment could not be saved due to these errors:'));
                $this->set('errors', $emsg);
                return $this->render('/Elements/task/validation_error');
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
		if (!$this->Comment->exists($id)) {
			throw new NotFoundException(__('Invalid comment'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Comment->save($this->request->data)) {
				$this->Session->setFlash(__('The comment has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The comment could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Comment.' . $this->Comment->primaryKey => $id));
			$this->request->data = $this->Comment->find('first', $options);
		}
		$tasks = $this->Comment->Task->find('list');
		$users = $this->Comment->User->find('list');
		$this->set(compact('tasks', 'users'));
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
        
        if($this->request->is('ajax')){
            $id = $this->request->data('cid');
        }
    
		$this->Comment->id = $id;
		if (!$this->Comment->exists()) {
			throw new NotFoundException(__('Invalid comment'));
		}
		if ($this->Comment->delete()) {
		    
            if($this->request->is('ajax')){
                return $this->response->statusCode(200);
            }
            
			$this->Session->setFlash(__('Comment deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		} // Didn't delete
            if($this->request->is('ajax')){
                $this->response->statusCode(200);
            }
		
		$this->Session->setFlash(__('Comment was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
    
    public function task($task_id){
        if (!$this->Comment->Task->exists($task_id)) {
            throw new NotFoundException(__('Invalid task'));
        }

        $rs = $this->Comment->find('all', array(
            'conditions'=>array(
                'Comment.task_id'=>$task_id
            ),
        ));

        $this->set('comments',$rs);
        $this->set('tid', $task_id);
        $this->render('/Elements/comment/by_task');
        
    }
    
    // 2016
    public function pageComments($task_id = null){

        $this->Paginator->settings = array(
            'Comment'=>array(
                'limit'=>5,
                'conditions'=>array(
                    'Comment.task_id'=>$task_id),
                'order'=>'Comment.created DESC')
        );
                
        $comments = $this->paginate('Comment');
        $this->set('comments', $comments);
        $this->set('team', $this->Comment->Task->getLeadByTask($task_id));
        $this->set('tid', $task_id);
        $paging = $this->params['paging'];
        //$paging['Comment']['options']['model'] = 'comment';
        
        
        $this->set('paging', $paging);
        
        if (!empty($this -> request -> params['requested'])){ 
            ($this->request->params['paging'])? $paging = $this->request->params['paging']: $paging = array();
            return array('comments'=>$comments, 'paging' => $paging, 'task'=>$task_id);
        }

        if($this->request->is('ajax')){
            //$this->autoRender = false;
            //$this->autoLayout = false;
            
            return $this->render('/Elements/comment/by_task');
        }

        $this->render('/Elements/comment/by_task');
    }
    
// 2016
    public function byTask($task_id = null){

        $comments = $this->Comment->find('all', array(
            'conditions'=>array(
                'Comment.task_id'=>$task_id),
            'order'=>'Comment.created DESC'
        ));

        //$comments = $this->paginate('Comment');
        $this->set('comments', $comments);
        $this->set('team', $this->Comment->Task->getLeadByTask($task_id));
        $this->set('tid', $task_id);
        //$paging = $this->params['paging'];
        //$paging['Comment']['options']['model'] = 'comment';
        
        
        //$this->set('paging', $paging);
        
        if (!empty($this -> request -> params['requested'])){ 
            //($this->request->params['paging'])? $paging = $this->request->params['paging']: $paging = array();
            return array('comments'=>$comments, 'task'=>$task_id);
        }

        if($this->request->is('ajax')){
            //$this->autoRender = false;
            //$this->autoLayout = false;
            
            return $this->render('/Elements/comment/by_task');
        }

        $this->render('/Elements/comment/by_task');
    }












//EOF
}
//EOF
