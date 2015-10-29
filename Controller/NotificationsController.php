<?php
App::uses('AppController', 'Controller');
/**
 * Notifications Controller
 *
 * @property Notification $Notification
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class NotificationsController extends AppController {

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
            //'index',
            //'view',
            //'add',
            //'edit',
            'approve',
            
            //'byTeam',
            
                        ))) {
            return true;
        }
                        
    if (in_array($this->action, array(
              'team', 
              'delete',
            ))){
                $team_id = (isset($this->request->params['pass'][0]))? $this->request->params['pass'][0]: null;
            if($team_id == null){
                return true;
            }
            
            if ($this->Notification->ReceiveTeam->isControlledBy($team_id, $user)) {
                return true;
            }
        }                        
        
        
        return parent::isAuthorized($user);
    }



/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Notification->recursive = 0;
		$this->set('notifications', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Notification->exists($id)) {
			throw new NotFoundException(__('Invalid notification'));
		}
		$options = array('conditions' => array('Notification.' . $this->Notification->primaryKey => $id));
		$this->set('notification', $this->Notification->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Notification->create();
			if ($this->Notification->save($this->request->data)) {
				$this->Session->setFlash(__('The notification has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The notification could not be saved. Please, try again.'), 'flash/error');
			}
		}
        $types = array(
            '100'=>'System',
            '200'=>'Assistance'
            );
		$parents = $this->Notification->Parent->find('list');
		$receiveTeams = $this->Notification->ReceiveTeam->find('list');
		$sendTeams = $this->Notification->SendTeam->find('list');
		$this->set(compact('tasks', 'recTeams', 'sendTeams', 'types'));
	}
    
    
    public function addSystem() {
        if ($this->request->is('post')) {
            $this->Notification->create();
            if ($this->Notification->save($this->request->data)) {
                $this->Session->setFlash(__('The notification has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The notification could not be saved. Please, try again.'), 'flash/error');
            }
        }
        $types = array(
            '100'=>'System',
            '200'=>'Assistance'
            );
        $parents = $this->Notification->Parent->find('list');
        $receiveTeams = $this->Notification->ReceiveTeam->find('list');
        $sendTeams = $this->Notification->SendTeam->find('list');
        $this->set(compact('tasks', 'recTeams', 'sendTeams', 'types'));
        
        $this->render('add_system');
    }

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Notification->exists($id)) {
			throw new NotFoundException(__('Invalid notification'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Notification->save($this->request->data)) {
				$this->Session->setFlash(__('The notification has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The notification could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Notification.' . $this->Notification->primaryKey => $id));
			$this->request->data = $this->Notification->find('first', $options);
		}
		$notification = $this->Notification->findById($id);
		$recTeams = $this->Notification->ReceiveTeam->find('list');
		$sendTeams = $this->Notification->SendTeam->find('list');
		$this->set(compact('notification', 'recTeams', 'sendTeams'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete_orig($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Notification->id = $id;
		if (!$this->Notification->exists()) {
			throw new NotFoundException(__('Invalid notification'));
		}
		if ($this->Notification->delete()) {
			$this->Session->setFlash(__('Notification deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Notification was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
    
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        if($this->request->is('ajax')){
            $id = $this->request->data('note_id');
        }
        
        $this->Notification->id = $id;
        
        if (!$this->Notification->exists()) {
            throw new NotFoundException(__('Invalid notification'));
        }
        
        if ($this->Notification->delete()) {
            
            $count_inbox = $this->updateSessionInboxCount();
            
            if($this->request->is('ajax')){
                 $data = array(
                    'success'=>true,
                    'message'=>'Deleted',
                    'inbox_count'=>$count_inbox,
                    );
                $this->response->statusCode(200);
                return json_encode($data); 
            }
            // Non-ajax
            $this->Session->setFlash(__('Notification deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }

        $this->Session->setFlash(__('Notification was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }
    
    
    public function team($team=null){
        
        if ($team == null){
            $this->set('hasMultipleTeams', true);
        }

        

        $inbox_count = $this->Notification->getInboxCountByTeam($team);
        $this->set('notifications', $this->Notification->getNotificationsByTeam($team));
        $this->set('inbox_count', $inbox_count);
        $this->set('archive_count', $this->Notification->getArchiveCountByTeam($team));    
                
        $this->set('team_id', $team);
        
        $this->setSessionInboxCount($inbox_count);
        
        if($this->request->is('ajax')){
            if($this->request->data('inbox') == true){
                $this->render('/Elements/notification/notification_list');    
            }
            
        }
        
        $this->render('team');
        //$this->set('data', $this->Notification->getNotificationsByTeam($team));
        //$this->render('/Elements/Utility/debug');
        
    }
    
    
    public function markRead(){
        
        $note_id = $this->request->data['note_id'];
        if(!$note_id){
            return false;
        }
        
        if($this->Notification->markRead($note_id)){
            return true;    
        }
        
        return false;
        
    }

    public function approve(){
        
        if($this->request->is('ajax')){
            $nid = $this->request->data('note_id');
        }
        
        $rs = $this->Notification->findById($nid);
        
        $task = $rs['Notification']['parent_task_id'];
        $team = $rs['Notification']['send_team_id'];
        
        if($this->Notification->SendTeam->TasksTeam->deleteAssistingTeamByTask($task, $team)){
            // Set current notification as approved
            $this->Notification->id = $nid;
            $this->Notification->saveField('is_approved', 1);
            
            $note_count = $this->updateSessionInboxCount();

            if($this->request->is('ajax')){
                 $data = array(
                    'success'=>true,
                    'message'=>'Approved',
                    'n_count'=>$note_count,
                    );
                $this->response->statusCode(200);
                return json_encode($data); 
            }          
            
        }
        
    }
    
    
    public function getInboxCountByTeam($team){
        $rs = $this->Notification->find('count', array(
            'conditions'=>array(
                'OR'=>array(
                    'type_id <'=>100,
                    'AND'=>array(
                        'rec_team_id'=>$team,
                        'is_approved'=>0,            
                    
                    ))),
            ));

        $rs2 = $this->Notification->find('count', array(
            'conditions'=>array(
                'OR'=>array(
                    'type_id <'=>100,
                    'AND'=>array(
                        'rec_team_id'=>$team,
                        'is_approved'=>1,            
                    
                    ))),
            ));


            
        //$this->log($rs);

        $this->set('data', $rs);
        $this->set('data2', $rs2);
        
        $this->render('/Elements/utility/debug');
        
        return $rs;        
    }
    
    public function inbox($team){
        
        $this->set('data', $this->Notification->getInboxByTeam($team));
        
    }
    
    public function addSystemMessage(){
        
    }
    
    // Update session var for notification counter... if there's only one team to show
    public function updateSessionInboxCount(){
        $teams = $this->Session->read('Auth.User.Teams');
                
        if(count($teams) == 1){
            $note_count = $this->Notification->getInboxCountByTeam((int)$teams[0]);    
            $this->Session->write('Notification.count', $note_count);    
        }
        else{$note_count = 0;}
        
        return $note_count;
        
    }
    
    public function setSessionInboxCount($count=0){
        
            $this->Session->write('Notification.count', $count);
        
    }
    
    
    
    
    
    
    
}
