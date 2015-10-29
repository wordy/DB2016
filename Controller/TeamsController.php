<?php
App::uses('AppController', 'Controller');
/**
 * Teams Controller
 *
 * @property Team $Team
 * @property PaginatorComponent $Paginator
 */
class TeamsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
    public $paginate = array('order'=>array('Team.code'=>'ASC'));
        
    public function isAuthorized($user) {
        // Default allows (logged in users)
        if (in_array($this->action, array(
            'makeList',
            'home',
            ))) {
            return true;
        }
        
        return parent::isAuthorized($user);
        //return false;
    }
    

/**
 * index method
 *
 * @return void
 */
	public function index() {
        $cont= array(
            'TaskColor'=>array(
                'fields'=>array('TaskColor.code')),
        );

        $this->Paginator->settings = array(
        'contain'=>$cont,
        'limit'=>50,
    );
		$this->set('teams', $this->paginate('Team'));
	}
    
/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Team->exists($id)) {
			throw new NotFoundException(__('Invalid team'));
		}
        $options = array(
		  'recursive'=>1,
		  'contain'=>array(
            'TeamsUser'=>array(
                'User'=>array(
                    'fields'=>array('user_role')
                ),
                'order'=>'TeamsUser.user_handle ASC'
            )
            ),
		  'conditions' => array(
		      'Team.' . $this->Team->primaryKey => $id
              )
        );
		
		$this->set('team', $this->Team->find('first', $options));
        $this->set('teams', $this->Team->find('list'));
	}

/**
 * add method
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
            // Convert color hex code into id of related TaskColor record
            $tcode = $this->request->data('Team.Color');
            $tcid = $this->Team->TaskColor->getIdByCode($tcode);
            $this->request->data('Team.task_color_id', $tcid);
            
			$this->Team->create();
			if ($this->Team->save($this->request->data)) {
				$this->Session->setFlash(__('The team has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The team could not be saved. Please, try again.'), 'flash/error');
			}
		}
        $taskColors = $this->Team->TaskColor->getAvailColorsList();
        $this->set('aColors', $taskColors);
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Team->exists($id)) {
			throw new NotFoundException(__('Invalid team'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
		    $tcode = $this->request->data('Team.Color');
            $tcid = $this->Team->TaskColor->getIdByCode($tcode);
            $this->request->data('Team.task_color_id', $tcid);
            
			if ($this->Team->save($this->request->data)) {
				$this->Session->setFlash(__('The team has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The team could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
		    
			$options = array('conditions' => array('Team.' . $this->Team->primaryKey => $id));
			$this->request->data = $this->Team->find('first', $options);
            $taskColors = $this->Team->TaskColor->getAvailColorsList();
            
            // Include team's own current color as default in list (i.e. no change)
            $curr_id = $this->request->data('Team.task_color_id');
            
            $curr_rs = $this->Team->TaskColor->find('first', array('conditions'=>array(
                'id'=>$curr_id,
                )));
            
            $curr_col = array();
            $curr_col[$curr_rs['TaskColor']['code']] = $curr_rs['TaskColor']['name'];  
            $aPlusColors = array_merge($curr_col, $taskColors);
            $this->set('aPlusColors', $aPlusColors);
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
		$this->Team->id = $id;
		if (!$this->Team->exists()) {
			throw new NotFoundException(__('Invalid team'));
		}
		if ($this->Team->delete()) {
			$this->Session->setFlash(__('Team deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Team was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}

    public function makeList(){
        $rs = $this->Team->listTeamCodeByCategory();
        return $rs;
    }
    
    public function teamIdCodeList(){
        $rs = $this->Team->teamIdCodeList(); 
        if(!empty($this->request->params['requested'])){ 
            return array('teamIdCodeList'=> $rs);
        }
    }

    //2015
    public function home($team_code = null){
        // If no code is specified, check if user has == 1 team, if so, assume they meant to go there    
        if($team_code == null){
            $userTeams = $this->Session->read('Auth.User.TeamsList');

            if(count($userTeams) == 1){
                $team_code = reset($userTeams);  // First (i.e. only) team; Not $userTeams[0] b/c key is team_id
            }
        }
        
        $ztlist = $this->Team->Zone->zoneTeamList();
        $zoneTeamCodeList = Hash::combine($ztlist, '{n}.Team.{n}.id', '{n}.Team.{n}.code', '{n}.Team.{n}.zone');
        $zoneNameTeamCodeList = Hash::combine($ztlist, '{n}.Team.{n}.id', '{n}.Team.{n}.code', '{n}.Team.{n}.zone_name');
        $this->set('zoneTeamCodeList', $zoneTeamCodeList);
        $this->set('zoneNameTeamCodeList', $zoneNameTeamCodeList);
        

        if(!empty($team_code) && $this->Team->existsByTeamCode($team_code)){
            $team_id = $this->Team->getTeamIdByCode($team_code);    
            $or = $this->Team->Task->getOpenRequestsByTeam($team_id);
            $ow = $this->Team->Task->getOpenWaitingByTeam($team_id);
            $utasks = $this->Team->Task->getUrgentByTeam($team_id);
            
            $this->set('urgentByTeam', $utasks);
            $this->set('open_tasks', $or);
            $this->set('waiting_tasks', $ow);
            $this->set('team_code', $team_code);
            $this->set('team_id', $team_id);
        }    
        //$this->render('/Elements/task/open_req');
  }
  
  


//EOF
}
//EOF

