<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Tasks Controller
 *
 * @property Task $Task
 * @property PaginatorComponent $Paginator
 */
class TasksController extends AppController {

/**
 * Components
 *
 * @var array
 */
    public $components = array('Paginator', 'Mpdf.Mpdf');

    /*
    public function beforeFilter() {
        // Allow only the view and index actions.
        //$this->Auth->allow('view', 'index');   
        parent::beforeFilter();
        
    }
     */

    public function isAuthorized($user) {
        
        //$this->log($user);
        // Default allows (logged in users)
        if (in_array($this->action, array(
            //'index',
            'add',
            'view', 
            'actionable',
            'compile',
            
            //2016
            'linkable',
            'details',
            'urgent',
            'urgentByUser',
            'checkPid',
            'getTaskById',
            'digest',
                        
            //2015
            'addShift',
            'timeShift',
            'remShift',
            'search',
            'userPrint',
            'resetShift',
            'compilePrint', 
            //'addTo',
            //'makeLinkableParentsList',
            //'compileUser',
            //'details',

            ))) {
            return true;
        }
        
        // The owner of a post can edit and delete it
        if (in_array($this->action, array(
                'edit', 
                'delete',
            ))){
                $task_id = $this->request->params['pass'][0];
            
            if ($this->Task->isControlledBy($task_id, $user)) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }



/**
 * index method
 *
 * @return void
 * 
 */
    public function index() {
        $cont= array(
            'Team'=>array(
                'fields'=>array('id','code')),
            'TasksTeam',
            'Change'
                );
        $this->Paginator->settings = array(
            'contain'=>$cont,
            'recursive'=>-1,
            'limit'=>50,
            'order'=> array(
            'Task.id'=>'desc')
        );
        $this->set('teams', $this->Task->Team->listTeamCodeByCategory());
        $this->set('zoneTeamCodeList', $this->Task->Team->zoneTeamCodeList());
        
       // $this->set('teams', $this->Task->Team->find('list'));
        $this->set('showTeamColors', TRUE);
        $this->set('tasks', $this->paginate());
        $this->render('index');
    }
    
 
/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function view($id = null) {
        if (!$this->Task->exists($id)) {
            throw new NotFoundException(__('Invalid task'));
        }

        $owa = date('Y-m-d', strtotime("-1 weeks"));

        $cont = array(
            'Comment',
            'TasksTeam',
        	'Change'=>array(
                'conditions'=>array('Change.created >'=>$owa),
                'fields'=>array(
                    'Change.created')),
            'Parent',
            'Assist',
            'Assist.TasksTeam'
            );
                
        $options = array(
            'contain'=>$cont, 
            'conditions' => array(
                'Task.' . $this->Task->primaryKey => $id));
        $task = $this->Task->find('first', $options);
        
        $this->set('task', $task);
        $this->set('teamsList', $this->Task->Team->find('list'));
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('controlled_teams', $this->Task->Team->listControlledTeamCodeByCategory());
        $this->set('user_controls', $this->Session->read('Auth.User.Teams'));
        $this->set('teams', $this->Task->Team->listTeamCodeByCategory());
        //$this->set('data', $task);
        $teams = $this->Task->Team->listTeamCodeByCategory();
        $this->set('teams', $teams); 
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('aInControl', $this->Task->Team->listAssistingAndControlledByUser($task['Task']['id'])); 
        
        if($this->request->is('ajax')){
            $this->autoLayout = false;
            $this->autoRender = false;
        }

        $this->render('view'); 
    }
 
/**
 * add method
 *
 * @return void
 */

 public function add() {
     
     //$this->log($this->request->data);
        if($this->request->is('ajax')){
            $this->autoLayout = false;
            $this->autoRender = false;
        }        
        
        if ($this->request->is('post')) {
            $t_ctrl = $this->request->data('Task.time_control');
            $new_tt = $this->request->data('TeamRoles');
            
            $this->Task->data['TeamRoles'] =  $new_tt;

            // Add task color based on lead team
            $lead_team = $this->request->data('Task.team_id');
            if($lead_team){
                $tcid = $this->Task->Team->getTaskColorIdByTeamId($lead_team);
                $this->request->data('Task.task_color_id', $tcid);
            }

            $this->Task->create();
            if ($this->Task->save($this->request->data)) {
                $new_tid = $this->Task->id;    
                
                if($this->request->is('ajax')){
                    $this->response->statusCode(200);
                    $this->set('message', __('New task created.'));
                    return $this->render('/Elements/task/validation_error');
                }
                
                $this->Session->setFlash(__('The task was successfully created'), 'flash/success');
                $this->redirect(array('action' => 'compile'));
            } // End of saving new data
                            
            else { //Task didn't save
                if($this->request->is('ajax')){
                    $this->response->statusCode(400);
                    $errors = $this->Task->validationErrors;
                    $emsg = Hash::extract($errors,'{s}.{n}');
                    $this->set('message', __('The task could not be saved due to these errors:'));
                    $this->set('errors', $emsg);
                    return $this->render('/Elements/task/validation_error');
                    
                } 
            
                $this->Session->setFlash(__('The task couldn\'t be saved. Please check the input and try again.'), 'flash/error');
            }
        } // not post
        $taskTypes = $this->Task->TaskType->makeListByCategory();
        $taskColors = $this->Task->TaskColor->makeCodeAndNameList();
        $actionableTypes = $this->Task->ActionableType->find('list');
        $teams = $this->Task->Team->listTeamCodeByCategory();
         
        $this->set('controlled_teams', $this->Task->Team->listControlledTeamCodeByCategory());
        $this->set(compact('actionableTypes', 'taskTypes', 'taskColors', 'teams'));
        //$this->layout="default";
        $this->render('add');        
    }

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function edit($id = null) {
        //$this->log($this->request->data);
        if (!$this->Task->exists($id)) {
            throw new NotFoundException(__('Invalid task'));
        }
        // Editing a task        
        if ($this->request->is('post') || $this->request->is('put')) {
            $new_troles = $this->request->data('TeamRoles');
            $this->Task->data['TeamRoles'] = $new_troles;
            $new_offset = $this->request->data('Offset');
            $this->Task->data['Offset'] = $new_offset;

            // Add task color based on lead team
            $lead_team = $this->request->data('Task.team_id');
            if($lead_team){
                $tcid = $this->Task->Team->getTaskColorIdByTeamId($lead_team);
                $this->request->data('Task.task_color_id', $tcid);
            }
            // Save edits            
            if ($this->Task->save($this->request->data['Task'])) {
                $new_tid = $this->request->data['Task']['id'];
                //$this->Session->setFlash(__('The task was successfully updated.'), 'flash/success');
                //$this->redirect($this->referer());
                if($this->request->is('ajax')){
                    $this->response->statusCode(200);
                    $this->set('message', __('Task saved successfully'));
                    return $this->render('/Elements/task/validation_error');
                }
            } 
            else { // Didn't Savve
                //$this->Session->setFlash(__('The task could not be saved. Please check for missing required fields and try again.'), 'flash/error');
                if($this->request->is('ajax')){
                    $this->response->statusCode(400);
                    $errors = $this->Task->validationErrors;
                    $emsg = Hash::extract($errors,'{s}.{n}');
                    $this->autoLayout = false;
                    $this->autoRender = false;
                    $this->set('message', __('The task could not be saved due to these errors:'));
                    $this->set('errors', $emsg);
                    return $this->render('/Elements/task/validation_error');
                }            
            }
        }//Not Post
        
        $options = array(
            'contain'=>array(
                'TasksTeam'
            ), 
            'conditions' => array(
                'Task.' . $this->Task->primaryKey => $id
            )
        );

        $task = $this->Task->find('first', $options);
        $taskTypes = $this->Task->TaskType->makeListByCategory();
        $actionableTypes = $this->Task->ActionableType->find('list');
        $teams = $this->Task->Team->listTeamCodeByCategory();
        $this->set('controlled_teams', $this->Task->Team->listControlledTeamCodeByCategory());
        $this->set(compact('task','taskTypes','actionableTypes','teams'));

        if($this->request->is('ajax')){
            $this->autoLayout = false;
            $this->autoRender = false;
            return array($task, $taskTypes, $teams);
        }
        //$this->layout = 'default';
        //if(!$this->request->is('ajax')){
        $this->render('/Elements/task/tab_edit');    
        //}
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
        //if (!$this->request->is('post')) {
          //  throw new MethodNotAllowedException();
        //}
        $this->Task->id = $id;
        if (!$this->Task->exists()) {
            throw new NotFoundException(__('Invalid task'));
        }
        
        //$this->Task->saveField('is_deleted', 1);
        
        
        if ($this->Task->delete($id, true)) {
            if($this->request->is('ajax')){
                $this->layout = false;
                $this->autoRender = false;
                $this->response->statusCode(200);
                //return "Task deleted";
                echo json_encode(array('success'=>true, 'message'=>'Task deleted')); 
                exit;       
            }

            $this->Session->setFlash(__('Task was successfully deleted'), 'flash/success');
            $this->redirect(array('action' => 'compile'));
        }
        // Wasn't deleted
        if($this->request->is('ajax')){
            $this->layout = false;
            $this->autoRender = false;
            $this->response->statusCode(400);
            return "Task could not be deleted";
        }
        $this->Session->setFlash(__('Task was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'compile'));
    }

    public function compile(){
        //$this->log($this->request->data('Compile'));
        $user_id = $this->Auth->user('id');
        $usrCurrPage = $this->Session->read('Auth.User.Compile.page');
        $order = $conditions = array();
        $limit = 25;
        $sort = '';
        $comp_is_same = true; // Used to determine if we need to save settings to session
        $usePaging = true;
        
       // Posted => Process new compile settings
        if($this->request->is('post')){
            //$this->log($this->request->data);
            // If we were paging, pass the settings into the next request
            if($this->params['paging']){
                $this->Paginator->settings = $this->params['paging'];
            }
            // Set up to compare settings as submitted vs. stored in user session variable
            $tmp_sess = $this->Session->read('Auth.User.Compile');
            $tmp_req = $this->request->data('Compile');
            $ucomp = ($tmp_sess)?: array();
            $ncomp = ($tmp_req)?: array();
            $ucomp['Teams'] = ($ucomp['Teams'])?: array();
            $ncomp['Teams'] = ($ncomp['Teams'])?: array();

            // Compare teams
            $tdiff1 = array_diff($ucomp['Teams'], $ncomp['Teams']);
            $tdiff2 = array_diff($ncomp['Teams'], $ucomp['Teams']);

            // Compare settings OTHER than teams; array_diff doesen't like sub-arrays            
            unset($ucomp['Teams']); unset($ncomp['Teams']); 
            $comp_settings_diff = array_diff_assoc($ncomp, $ucomp);
  
            if(!empty($tdiff1) || !empty($tdiff2) || !empty($comp_settings_diff)){
                $comp_is_same = false;
            }
            //Get compile params as submitted or set defaults
            $settings = $this->Task->makeSafeCompileSettings($tmp_req);
            
            if(!$comp_is_same){
                $settings['page'] = 1;
            }
            
        }
        elseif($this->request->is('get')){
            $settings = $this->Session->read('Auth.User.Compile');
            $comp_is_same = false;

            // Querystring Params
            $qSingle = $this->request->query('task');
            // PDF, Plain, Threaded, Rundown, Lead Only, Open Requests, Action Items, Recent
            $qView = $this->request->query('view'); 
            $qSort = $this->request->query('sort');
            $qPage = $this->request->query('page');
            $qSrc = $this->request->query('src');
            $qTeams = $this->request->query('team');
            $qStart = $this->request->query('start');
            $qEnd = $this->request->query('end');
            $qDetails = $this->request->query('details');
            $qLinks = $this->request->query('links');
            $qThreaded = $this->request->query('threaded');

            if(isset($qStart)){
                $settings['start_date'] = $qStart;
            }
            if(isset($qEnd)){    
                $settings['end_date'] = $qEnd;
            }
            if(isset($qDetails)){
                $settings['view_details'] = $qDetails;
            }
            if(isset($qLinks)){
                $settings['view_links'] = $qLinks;
            }
            if(isset($qThreaded)){
                $settings['view_threaded'] = $qThreaded;
            }
            if(isset($qPage)){
                $settings['page'] = $page = $qPage;
                $this->Session->write('Auth.User.Compile.page', $qPage);
            }
            // Didn't submit a request, use saved value
            if(!$qPage && !$qSrc){
                $settings['page'] = $page = $usrCurrPage;
            }
            // Paging from compile. PaginatorHelper does ?src=compile for page=1, but ?src=compile&page=## for all others
            elseif(!$qPage && ($qSrc == 'compile')){
                $settings['page'] = $page = 1;
            }
            // When refreshing list in "Success" ajax callbacks (edit, add, delete)
            elseif(!$qPage && ($qSrc == 'action' || $qSrc == 'ajax')){
                $settings['page'] = $page = $usrCurrPage;
            }

        }

        // Process settings, set defaults if necessary        
        $cc = $this->Task->makeCompileConditions($settings);
        //$this->log($cc);
        $teams = $cc['teams'];
        $conditions = $cc['conditions'];
        $order = $cc['order'];
        $contain = $cc['contain'];
        $limit = $cc['limit'];

        // If viewing a single task, overwrite conditions. $page = 1 important so paginator won't be
        // out of bounds (i.e. with $page > 1 set elsewhere in user's conditions).
        if(isset($qSingle) && !empty($qSingle)){
            $conditions = array('Task.id'=>$qSingle);
            $limit = 1;
            $page = 1;
            $this->set('single_task', (int)$qSingle);
        }
        elseif(isset($qView) && ($qView == 'plain' || $qView == 'excel')){
            $usePaging = false;
            $upref = $this->Task->PrintPref->getUserPrefsByType($this->Auth->user('id'));
            $this->set('printPrefs', $upref);
            
        }
        elseif(isset($qSearch)){
            $conditions = array(
                'OR' => array(
                    'Task.short_description LIKE' => "%$qSearch%",
                    'Task.details LIKE' => "%$qSearch%"
                )    
            );
            $order = 'Task.start_time ASC';
            $this->set('search_term', $qSearch);
        }
        elseif(isset($qView) && $qView == 'pdf'){
            // Use current compile + user visibility settings (hide details/hide tasks)
            // Currently forces download of PDF in browser
            $this->pdfFromUserSettings();
            
            /*
            // Get and set print prefs (used to hide tasks in printed view)
            $upref = $this->Task->PrintPref->getUserPrefsByType($this->Auth->user('id'));
            $this->set('printPrefs', $upref);*/
        }
        
        //$this->log($conditions);
        $page = (isset($page))? $page : 1;


        if($usePaging){
            $this->Paginator->settings = array(
                'Task'=>array(
                    'contain'=>$contain,
                    'paramType'=>'querystring',
                    'limit'=>($limit)?: 25,
                    'conditions'=>$conditions,
                    'order'=>$order,
                    'page'=> $page,
                )
            );           

            $tasks = $this->Paginator->paginate('Task');
        }
        else{
            $tasks = $this->Task->find('all', array(
                'contain'=>$contain,
                'conditions'=>$conditions,
                'order'=>$order,
            ));
        }
        
        $tlist = $this->Task->Team->zoneTeamList();
        $teamIdCodeList = array();
        foreach ($tlist as $zcode => $zteams){
            foreach ($zteams as $tid =>$tcode){
                $teamIdCodeList[$tid] = $tcode;   
            }
        }
        
        $zoneTeamList = array();
        $zoneTeamCodeList = array();
        foreach ($tlist as $zone => $tids){
            $zoneTeamList[$zone] = array_keys($tids);
            foreach ($tids as $tid => $tcode){
                $zoneTeamCodeList[$zone][$tid] = $tcode;    
            }
        }
        // Set and store new compile settings, if different
        if(!$comp_is_same){
            $this->Session->write('Auth.User.Compile', $settings);
        }       
    
        // Settings for Compile Options
        $this->request->data('Compile', $settings);
        $this->set('cSettings', $settings);
        $this->set('tasks', $tasks);
        $this->set('teamIdCodeList', $teamIdCodeList);
        $this->set('zoneTeamCodeList', $zoneTeamCodeList);
        $this->set('zoneNameTeamList', $this->Task->Team->zoneNameTeamCodeList());
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('user_shift', $this->Session->read('Auth.User.Timeshift'));

        if(isset($qView) && $qView == 'plain'){
            $this->layout = 'compile_pdf';
            $this->render('/Elements/task/tasks_table');
        }
        // PDF View, in browser
        elseif(isset($qView) && $qView == 'pib'){
            $upref = $this->Task->PrintPref->getUserPrefsByType($this->Auth->user('id'));
            $this->set('printPrefs', $upref);

            $this->layout = 'compile_pdf';
            $this->render('/Elements/task/tasks_table_pdf');
        }
        elseif(isset($qView) && $qView == 'excel'){
            $this->layout = 'pdf/default';
            $this->render('/Tasks/pdf/compile');
        }
        
        if($this->request->is('ajax')){
            return $this->render('/Elements/task/compile_screen');
        }
        // Uncomment these to view PDF's content in browser:
        //$this->layout = 'pdf/default';
        //$this->render('/Tasks/pdf/compile');

        // These are set by default, uncomment to control view/layout        
        //$this->layout = 'default';    
        //$this->render('compile_pdf');
        
        // 2016 PDF View (in browser):
        //$this->layout = 'compile_pdf';    
        //$this->render('/Elements/task/tasks_table');
    }

    public function urgentByUser(){
        $user_id = $this->Auth->user('id');
        $order = array();
        $conditions = array();
        $limit = 10;
        $sort = '';
        //$new_prefs = array();
        //NOTE: DBALL Setting. Default time to treat changes as "new" (Default is 7 days)
        //$date_str = strtotime(date('Y-m-d').'-7 days');
        //$new_limit = date('Y-m-d', $date_str);
    
            // If we were paging, pass the settings into the next request
            if($this->params['paging']){
                $this->Paginator->settings = $this->params['paging'];
            }
            // Set up to compare settings as submitted vs. stored in user session variable
            $ucomp = $this->Session->read('Auth.User.Compile');
            
            $settings = $this->Task->makeSafeCompileSettings($ucomp);
            
            // Enforced for Urgent
            $settings['view_type']=399;

        // Process settings, set defaults if necessary        
        $cc = $this->Task->makeCompileConditions($settings);
        
        $teams = $cc['teams'];
        $conditions = $cc['conditions'];
        $order = $cc['order'];
        $contain = $cc['contain'];
        $limit = $cc['limit'];
       
        $this->Paginator->settings = array(
            'Task'=>array(
                'contain'=>$contain,
                'limit'=>10,
                'conditions'=>$conditions,
                'order'=>$order,
        ));

        $nextMeeting = $this->nextOpsMeeting();
        
        //$this->log($nextMeeting);
        
        $this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());

        $tasks = $this->Paginator->paginate('Task');
//$this->log($tasks);
        if(!empty($this->request->params['requested'])){ 
            return array('utasks'=> $tasks, 'nextMeeting'=>$nextMeeting);
        } 
        $this->render('compile');
    }

    public function urgent($pdf = null){
        $user_id = $this->Auth->user('id');
        
        $order = array();
        $conditions = array();
        $limit = 50;
        $sort = '';
        //$new_prefs = array();
        //NOTE: DBALL Setting. Default time to treat changes as "new" (Default is 7 days)
        //$date_str = strtotime(date('Y-m-d').'-7 days');
        //$new_limit = date('Y-m-d', $date_str);
    
            // If we were paging, pass the settings into the next request
            if($this->params['paging']){
                $this->Paginator->settings = $this->params['paging'];
            }
            // Set up to compare settings as submitted vs. stored in user session variable
            $ucomp = $this->Session->read('Auth.User.Compile');
            
            $settings = $this->Task->makeSafeCompileSettings($ucomp);
            
            $settings['view_type']=4;

        
        // Process settings, set defaults if necessary        
        $cc = $this->Task->makeCompileConditions($settings);
        
        $teams = $cc['teams'];
        $conditions = $cc['conditions'];
        $order = $cc['order'];
        $contain = $cc['contain'];
        $limit = $cc['limit'];
        //$sort = $cc['sort'];
        

       
            $this->Paginator->settings = array(
                'Task'=>array(
                    'contain'=>$contain,
                    'limit'=>(!$limit)? 100: $limit,
                    'conditions'=>$conditions,
                    'order'=>$order,
            ));
                    
            $tasks = $this->Paginator->paginate('Task');
            
//            $this->request->data('Compile', $settings);
            
        
        $this->set('tasks', $tasks);
        $this->set('teams', $this->Task->Team->listTeamCodeByCategory());
        $this->set('teamsList', $this->Task->Team->find('list'));
        $this->set('taskColors', $this->Task->TaskColor->makeCodeAndNameList());
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('cSettings', $settings);
        $this->set('cteams', $teams);
        $this->set('view_type', $settings['view_type']);
        $this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        
        // These are set by default, uncomment to control view/layout        
        //$this->layout = 'default';    
        //$this->render('compile');
        
        
    }

    public function details($task_id=null){
        $options = array(
            'contain'=>array(
                //'Comment',
                'TasksTeam',
                'Assist'=>array(
                    'order'=>array('Assist.created DESC')
                ),
                'Assist.TasksTeam',
                'Parent'
                ), 
            'conditions' => array(
                'Task.' . $this->Task->primaryKey => $task_id));
        $task = $this->Task->find('first', $options);

        if(isset($task['Task']['time_control']) && isset($task['Task']['time_offset'])){
            $to = $this->Task->offsetToMinSecParts($task['Task']['time_offset']);
            $task['Offset']['sign'] = $to['sign'];
            $task['Offset']['minutes'] = $to['min'];
            $task['Offset']['seconds'] = $to['sec'];
        }

        //$this->set('task', $task);
        //$this->set('tid', $task_id);
        $allowTRoles = $this->Task->TasksTeam->getPossibleRolesByTask($task['Task']['team_id'],$task['Task']['id']); 
        $aInControl = $this->Task->Team->listAssistingAndControlledByUser($task['Task']['id']);
        $taskTypes = $this->Task->TaskType->makeListByCategory();
        $linkable = $this->Task->linkableParentsList($task['Task']['team_id'], $task['Task']['parent_id'], $task['Task']['id']);
        $actionableTypes = $this->Task->ActionableType->find('list');
        $teams = $this->Task->Team->listTeamCodeByCategory();
        $this->set('controlled_teams', $this->Task->Team->listControlledTeamCodeByCategory());
        $this->set(compact(
            'allowTRoles',
            'task', 
            'aInControl',
            //'tid',
            'linkable',
            'taskTypes',
            'actionableTypes', 
            'teams')
        );

       $this->render('/Elements/task/details');
  }
    
    public function addShift(){
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    
        if($this->request->is('ajax')){
            $added = $this->request->data('task');
            $cts = $this->Session->read('Auth.User.Timeshift');
            $user = $this->Session->read('Auth.User');
            
            // Verify user owns the task to be added
            if($this->Task->isControlledBy($added, $user)){
                if(!$cts || !in_array($added, $cts)){
                    $cts[] = $added;
                    $this->Session->write('Auth.User.Timeshift', $cts);
                }
                    
                $num_cts = (count($cts)>=0)? count($cts): 0;
                    
                $data = array(
                    'success'=>true,
                    'message'=>"Task added to time shift list",
                    'ts_count'=>$num_cts,
                    );
                //$this->response->statusCode(200);
                return json_encode($data);
            }
            else{ //Does not control
                $this->response->type('json');
                $this->response->statusCode(401);
                
                $this->response->body(json_encode(array(
                    'success' => false, 
                    'message' => "Sorry, your permissions don't allow you to add that task")));
                //$this->response->send();
                $this->_stop();
                    
                //return json_encode($data);
                
            }
        }
        
     $this->render('time_shift');
    }

    public function remShift(){
     
        if($this->request->is('ajax')){
            $this->layout = false;
            $this->autoRender = false;
            $deld = $this->request->data('task');
            $cts = $this->Session->read('Auth.User.Timeshift');
            
            if(in_array($deld, $cts)){
                $key = array_search($deld, $cts);
                
                if($key!==false){
                    unset($cts[$key]);
                }
            }

            $this->Session->write('Auth.User.Timeshift', $cts);

            $num_cts = count($cts);
            
            $data = array(
                'success'=>true,
                'message'=>"Task removed from time shift list",
                'ts_count'=>$num_cts,
                );
            
            return json_encode($data);
        }
        
        
     //$this->render('time_shift');
    }
    
    public function resetShift(){
        $this->Session->write('Auth.User.Timeshift', array());
    }
 
    public function timeShift(){
        if ($this->request->is('post')) {
            $u_ts = $this->Session->read('Auth.User.Timeshift');
            
            $t_mins = (int)$this->request->data('Shift.min');
            $t_secs = (int)$this->request->data('Shift.sec');
            $t_int = 60*$t_mins + $t_secs;
            
            if($this->Task->incrementTaskTime($u_ts, $t_int)){
                $this->Session->write('Auth.User.Timeshift', array());
                $this->Session->setFlash(__('Your tasks were shifted and your list has been reset.'), 'flash/success', array(), 'compile');
                $this->redirect(array('controller'=>'tasks','action' => 'compile'));    
            }
            else {
                $this->Session->setFlash(__('Tasks could not be shifted. Please try again.'), 'flash/error', array(), 'compile');
            }
        } 
        // Not post
        else {
            $tasks = $this->Task->userTimeshift();
            $this->set('tasks', $tasks);
    
            if($this->request->is('ajax')){
                return $this->render('/Elements/task/time_shift_table');
            }
         
             $this->render('time_shift');
        }
    }

    public function userPrint(){
        $page = 1;
        $ses = $this->Session->read('Auth.User.Compile');
        
    	// Enforce rundown view:
        //$ses['view_type']=1;
        $user_id = $this->Auth->user('id');
        
        //Sanitize/parse settings saved in session
        $cond =  $this->Task->makeCompileConditions($ses);

        $contain = $cond['contain'];
        $limit = $cond['limit'];
        $conditions = $cond['conditions'];
        $order = $cond['order'];
        
        // If requested via ajax, send page user was looking at last
        // This is sent in view/tasks/compile
        if($this->request->is('ajax')){
            $page = (!empty($this->request->data['page']))? $this->request->data['page']: 1;
        }
        
        $this->Paginator->settings = array(
            'Task'=>array(
                'contain'=>$contain,
                'page'=>$page,
                'limit'=>50,
                'conditions'=>$conditions,
                'order'=>$order,
        ));
        
        // Pass back the paging params
        if($this->params['paging']){
            $this->Paginator->settings = $this->params['paging'];
        }
                    
        $tlist = $this->Task->Team->zoneTeamList();
        $zoneTeamCodeList = array();
            foreach ($tlist as $zone => $tids){
                foreach ($tids as $tid => $tcode){
                    $zoneTeamCodeList[$zone][$tid] = $tcode;    
                }
            }
        
        //User Print Preferences
        $upref = $this->Task->PrintPref->getUserPrefsByType($user_id);
        $tasks = $this->Paginator->paginate('Task');
        $this->set('tasks', $tasks);
        //$this->set('controlled_teams', $this->Task->Team->listControlledTeamCodeByCategory());
        $this->set('zoneTeamCodeList', $zoneTeamCodeList);
//        $this->set('taskColors', $this->Task->TaskColor->makeCodeAndNameList());
//        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
//        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('PrintPrefs', $upref);
        
        if($this->request->is('ajax')){
            return $this->render('/Elements/print_preference/print_pref_list');
        }
        
        $this->render('/Elements/print_preference/print_pref');
    }

    //2015
    public function search($q=null){
        if ($this->request->is('post')){
            $q = $this->request->data('Search.term');
        }
        
        if(strlen($q) < 3){
            $q = NULL;
            $rs = array();
        }
        else{
            $date_str = strtotime(date('Y-m-d').'-7 days');
            $new_limit = date('Y-m-d', $date_str);
            $uc = $this->Session->read('Auth.User.Compile');
            
            $contain = array(
                'TasksTeam'=>array(
                    'fields'=>array(
                        'TasksTeam.team_id',
                        'TasksTeam.team_code',
                        'TasksTeam.task_role_id',
                        )),
                'Change'=>array(
                    'conditions'=>array('Change.created >'=>$new_limit),
                    'fields'=>array(
                        'Change.created')),
                'Assist',
            );
            
            $rs = $this->Task->find('all', array(
                'contain'=>$contain,
                'conditions'=>array(
                    'OR' => array(
                        'Task.short_description LIKE' => "%$q%",
                        'Task.details LIKE' => "%$q%"
                    )    
                ),
                'order'=>'Task.start_time ASC',
                )
            );
        }
        $this->set('tasks', $rs);
        $this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        $this->set('zoneTeamCodeList', $this->Task->Team->zoneTeamCodeList());
        
        //$this->set('user_controls', $this->Session->read('Auth.User.Teams'));
        $this->set('teams', $this->Task->Team->listTeamCodeByCategory());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        
        //$this->set('controlled_teams', $this->Task->Team->listControlledTeamCodeByCategory());
        $this->set('search_term', $q);
    
        //$this->render('compile');
        $this->render('search');
           //$this->render('/Elements/task/task_search');
           //$this->render('/Elements/task/drive');
    }

    public function linkable($team=null, $current=null, $child_task=null){
        if($this->request->is('ajax')){
            $team = $this->request->data('team');
            $current = $this->request->data('current');
            $child_task = $this->request->data('child');
        }

        $nl = $this->Task->linkableParentsList($team, $child_task);
        
        if(!empty($this->request->params['requested'])){ 
            return $nl;
        }
        $this->set('linkable', $nl);
        $this->set('team', $team);
        $this->set('current', $current);
        $this->set('child_task', $child_task);
        $this->set(compact('linkable','team','current','child'));
        $this->set('_serialize', array('linkable','team','current','child'));

        $this->render('/Elements/task/linkable_parents_list');       
    }
    
    
    public function digest($team){
        //$tids = $this->Task->digestByTeam($team);
        $rs = $this->Task->getDigestDataByTeam($team);
                
        $this->set('team_code', $rs['team_code']);
        $this->set('next_meeting', $rs['next_meeting']);
        //$this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        //$this->set('zoneTeamCodeList', $this->Task->Team->zoneTeamCodeList());
        $this->set('recent_requests', $rs['recent_requests']);
        $this->set('recent_links', $rs['recent_links']);
        $this->render('/Elements/task/digest');
        
    }
  /*  
    public function getDigestByTeam($team){
        
        $ticl = $this->Task->Team->teamIdCodeList();
        
        foreach($ticl as $tid => $tcode){
            
        }
        
        //$tids = $this->Task->digestByTeam($team);
        $rs = $this->Task->getDigestByTeam($team);
                
        $this->set('team_code', $rs['team_code']);
        $this->set('next_meeting', $rs['next_meeting']);
        $this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        //$this->set('zoneTeamCodeList', $this->Task->Team->zoneTeamCodeList());
        $this->set('recent_requests', $rs['recent_requests']);
        $this->set('recent_links', $rs['recent_links']);
        $this->render('/Elements/task/digest');
        
    }
*/
    // 2015
    public function sendDigestAll(){
        // Retrieves only users subscribed to digest. Return is of form array(team_id=>array(uid=>array(id, email, etc.)))
        $teamsusers = $this->Task->Team->TeamsUser->getDigestUsersByTeam();
        $errors = array();
        
        // Get data once per team
        foreach($teamsusers as $team => $users){
            $tdata = $this->Task->getDigestDataByTeam($team);
            
            // Send to each subscribed user; Update last digest date
            foreach($users as $k => $user){
                $sent = $this->Task->sendDigestToUser($user, $tdata, true);
                
                if($sent['success'] == false){
                    $errors[] = 'Failed to send to '.$sent['email'];
                }
            }    
        }
        
        if(!empty($errors)){
            $this->Session->setFlash('Some emails failed', 'flash/error_list', array('errors'=>$errors));
        }
        else{
            $this->Session->setFlash('Digest successfully sent to all', 'flash/success');
        }
        return $this->redirect($this->referer());
    }


/*
    public function openReqByTeam($team){
        $d2 = $this->Task->TasksTeam->openRequestsByTeam($team);
        $now = date('Y-m-d H:i:s');
        
        $rs = $this->Task->find('all', array(
            'conditions'=>array(
                'Task.id'=>$d2,
                //'Task.end_time >='=>$now
            ),
            'contain'=>array(
                'TasksTeam'
            )
        ));
        $this->set('data2', $d2);
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('tasks', $rs);
        $this->set('teams', $this->Task->Team->listTeamCodeByCategory());
        $this->set('controlled_teams', $this->Task->Team->listControlledTeamCodeByCategory());
        $this->set('teamsList', $this->Task->Team->find('list'));
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        
        $this->render('compile');
        //$this->render('/Elements/Utility/debug');       
    }
*/

/*
    public function getStartTimeByTask($task){
        $stime = null;
        $rs = $this->Task->findById($task);
        
        if($rs['Task']['start_time']){
            $stime = $rs['Task']['start_time'];
        }
        
        if(!empty($this->request->params['requested'])){ 
            return $stime;
        }
        
        if($this->request->is('ajax')){
            return $stime;
        } 

        $this->set('data', $stime);
        $this->set('data2', $rs);
        $this->render('/Elements/Utility/debug');    
    }
*/
    public function checkPid(){
        if (!$this->request->is('ajax')) {
            throw new MethodNotAllowedException();
        }
        
        $this->layout = false;
        $this->autoRender = false;
        $task = $this->request->data('task');
        $new_par = $this->request->data('parent');
            
        if($task && $new_par){
            $c_in_p = $this->Task->isChildInPidChain($task, $new_par);
            
            $data = array(
                'allow_parent'=>!$c_in_p,
            );    
            
        return json_encode($data);
    }
        
        
     //$this->render('time_shift');
    }

    public function nextOpsMeeting(){
        return $this->Task->getNextOpsMeeting();
    }

/***********************
 *TESTING
 * 
 * 
 ***********************/
 
 
  /*
    public function changeChildStartEndTime($parent_tid){
        $rs = $this->Task->changeChildStartEndTime($parent_tid);
        
        $this->set('data', $rs);
        $this->render('/Elements/Utility/debug');
    }
 

    public function makeView(){
        
        $rs = $this->request->pass;
        $rs2 = $this->request->named;
        
        
        $this->set('data', $rs);
        $this->set('data2', $rs2);
        $this->render('/Elements/Utility/debug');
        
    }

    public function tcChain($task){
        $chain = $this->Task->getRootPidChain($task);
            
        
        $this->set('data', $chain);
        //$this->set('data2', $tcc);
        $this->render('/Elements/Utility/debug');
        
    }



   
    public function addNew(){
        $teams = $this->Task->Team->listTeamCodeByCategory();
        $this->set('data', $teams);
        
        $d2 = array_keys(array_values($teams));
        
        $this->set('data2', $d2);
        
        $this->render('/Elements/Utility/debug');       
        
    }
    
    public function testThreaded($task){
        
        $rs = $this->Task->find('threaded', array(
            'conditions'=>array(
                'Task.id'=>$task    
            ),
            'recursive'=>1,
        ));
        
        $this->set('data', $this->Task->find('threaded', array('recursive'=>-1, 'conditions'=>array('Task.id'=>$task))));
        
        $this->render('/Elements/Utility/debug');
    }



    public function testHello(){
        
        $this->set('data', $this->sayHello());
        
        $this->render('/Elements/Utility/debug');
        
    }
    
 //   App::uses('CakeEmail', 'Network/Email');


function testMail () {
    $Email = new CakeEmail('gmail');
    $Email->from(array('DBOpsCompiler@gmail.com' => 'DBOps Compiler'));
    $Email->to('bplogins@gmail.com');
    $Email->replyTo('DBOpsCompiler@gmail.com');
    $Email->subject('About');
    $Email->send('My message');
    $Email->log=true;
    
    $this->set('data', $Email);
    $this->render('/Elements/Utility/debug');
}

    public function testTT($team){  
        $roles = array(1,2,3,4);
        
        $rs = $this->Task->TasksTeam->find('all', array(
            'conditions'=>array(
                'TasksTeam.team_id'=>$team,
                'TasksTeam.task_role_id' => $roles
            ),
            'contain'=>array('Task','Task.TasksTeam','Task.Assist','Task.Change','Task.Comment'),
            'order'=>array('Task.start_time ASC')
        ));
        $tlist = $this->Task->Team->zoneTeamList();
        $teamIdCodeList = array();
        foreach ($tlist as $zcode => $zteams){
            foreach ($zteams as $tid =>$tcode){
                $teamIdCodeList[$tid] = $tcode;   
            }
        }
        
        $zoneTeamList = array();
        $zoneTeamCodeList = array();
            foreach ($tlist as $zone => $tids){
                $zoneTeamList[$zone] = array_keys($tids);
                foreach ($tids as $tid => $tcode){
                    $zoneTeamCodeList[$zone][$tid] = $tcode;    
                }
            }
        
        $this->set('teamIdCodeList', $teamIdCodeList);
        $this->set('zoneTeamCodeList', $zoneTeamCodeList);
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('controlled_teams', $this->Task->Team->listControlledTeamCodeByCategory());
        $this->set('user_controls', $this->Session->read('Auth.User.Teams'));
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('user_shift', $this->Session->read('Auth.User.Timeshift'));
        //$this->set('cSettings', $settings);
       
    //$this->set('data', $rs);
    //$this->render('/Elements/Utility/debug');
	$this->set('tasks',$rs);
    $this->render('compile_2');
}
*/
    public function pdfFromUserSettings(){
        $settings = $this->Session->read('Auth.User.Compile');

        // Process settings, set defaults if necessary        
        $cc = $this->Task->makeCompileConditions($settings);
        $conditions = $cc['conditions'];
        $order = $cc['order'];
        $contain = $cc['contain'];

        $user = AuthComponent::user('handle');
        $date = date('Y-m-d');

        $ename = Configure::read('EventShortName');

        $tasks = $this->Task->find('all', array(
            'conditions'=>$conditions,
            'contain'=>$contain,
            'order'=>$order));

        $upref = $this->Task->PrintPref->getUserPrefsByType($this->Auth->user('id'));
        $this->set('printPrefs', $upref);
        $this->set('cSettings', $settings);
        



        $tlist = $this->Task->Team->zoneTeamList();
        $teamIdCodeList = array();
        foreach ($tlist as $zcode => $zteams){
            foreach ($zteams as $tid =>$tcode){
                $teamIdCodeList[$tid] = $tcode;   
            }
        }
        
        $zoneTeamList = array();
        $zoneTeamCodeList = array();
        foreach ($tlist as $zone => $tids){
            $zoneTeamList[$zone] = array_keys($tids);
            foreach ($tids as $tid => $tcode){
                $zoneTeamCodeList[$zone][$tid] = $tcode;    
            }
        }

        $view = new View($this,false);
        //$view->viewPath='Tasks/pdf';  // Directory inside view directory to search for .ctp files
        //$view->layout='pdf/default'; // if you want to disable layout
        $view->viewPath='Elements/task';  // Directory inside view directory to search for .ctp files
        //$view->layout='default'; // if you want to disable layout
        $view->layout='compile_pdf';
       
        $view->set ('tasks', $tasks); // set your variables for view here
        $view->set('teamIdCodeList', $teamIdCodeList);
        $view->set('zoneTeamCodeList', $zoneTeamCodeList);
        //$this->set(compact('tasks','teamIdCodeList','zoneTeamCodeList'));
        //$html=$view->render('compile_pdf');
        $html=$view->render('tasks_table_pdf');

        $cdate = date('M j, Y');
         
        
        //$this->log($html);
//$this->render('compile_pdf');



//$this->set('data', $html);
//$this->render('/Elements/Utility/debug');





        //$html = $this->render('/Task/pdf/compile');
        
     $this->Mpdf->init(array('format'=>'A4-L'));
     //$this->Mpdf->AddPage('','','','','on');
    //$this->Mpdf->setFooter("Page {PAGENO} of {nb}");
    
    
$footer = array (
  'odd' => array (
    'L' => array (
      'content' => 'As of '.$cdate,
      'font-size' => 9,
      'font-style' => '',
      'font-family' => 'serif',
      'color'=>'#333'
    ),
    'C' => array (
      'content' => '',
      'font-size' => 10,
      'font-style' => 'B',
      'font-family' => 'serif',
      'color'=>'#000000'
    ),
    'R' => array (
      'content' => 'Page {PAGENO} of {nb}',
      'font-size' => 9,
      'font-style' => '',
      'font-family' => 'serif',
      'color'=>'#333'
    ),
    'line' => 1,
  ),
  'even' => array ()
);

/*  
$this->Mpdf->defaultfooterfontsize=10;
$this->Mpdf->defaultfooterfontstyle='B';
$this->Mpdf->defaultfooterline=1;
*/    
        //$this->Mpdf->setFooter("Page {PAGENO} of {nb}");
    
    $this->Mpdf->SetFooter($footer);
    

     //$this->Mpdf->WriteHTML('Section 2 - No Footer');
     
     
    //$this->Mpdf->AddPage('','','','','on'); 
    $this->Mpdf->WriteHTML($html);
    /*
    $this->Mpdf->SetFooter(array (
  'odd' => array (
    'L' => array (
      'content' => '{PAGENO}',
      'font-size' => 10,
      'font-style' => 'B',
      'font-family' => 'serif',
      'color'=>'#000000'
    ),
    'C' => array (
      'content' => 'yomomma',
      'font-size' => 10,
      'font-style' => 'B',
      'font-family' => 'serif',
      'color'=>'#000000'
    ),
    'R' => array (
      'content' => 'My document',
      'font-size' => 10,
      'font-style' => 'B',
      'font-family' => 'serif',
      'color'=>'#000000'
    ),
    'line' => 1,
  ),
  'even' => array ('L' => array (
      'content' => '{PAGENO}',
      'font-size' => 10,
      'font-style' => 'B',
      'font-family' => 'serif',
      'color'=>'#000000'
    ),
    'C' => array (
      'content' => 'yomomma',
      'font-size' => 10,
      'font-style' => 'B',
      'font-family' => 'serif',
      'color'=>'#000000'
    ),
    'R' => array (
      'content' => 'My document',
      'font-size' => 10,
      'font-style' => 'B',
      'font-family' => 'serif',
      'color'=>'#000000'
    ),
    'line' => 1,)
));*/
    // setting filename of output pdf file
    //$this->Mpdf->SetWatermarkText("Draft");
    //$this->Mpdf->showWatermarkText = true;
    $this->Mpdf->Output($ename.' Plan_'.$user.'-'.$date.'.pdf', 'D');
    
    
    // setting output to I, D, F, S
    //$this->Mpdf->Output('file.pdf', 'D');

    // you can call any mPDF method via component, for example:
    $this->redirect(array('controller'=>'tasks', 'action'=>'userPrint'));
    $this->layout=false;
    $this->render(false);
    
    }

    public function testThreaded(){
	
    
    
    $this->set('data', $rs);
    $this->render('/Elements/Utility/debug');
}

    // 2016 -- Used to find parent_parent etc
    public function getTaskById($task_id){
        
        $rs = $this->Task->find('first', array(
            'conditions'=> array(
                'Task.id'=>$task_id
            ),
        ));
        return $rs;
    }

    public function openReq($team){
        $or = $this->Task->getOpenRequestsByTeam($team);
        $ow = $this->Task->getOpenWaitingByTeam($team);
        
        
        $tlist = $this->Task->Team->zoneTeamList();
        $teamIdCodeList = array();
        foreach ($tlist as $zcode => $zteams){
            foreach ($zteams as $tid =>$tcode){
                $teamIdCodeList[$tid] = $tcode;   
            }
        }
        
        $zoneTeamList = array();
        $zoneTeamCodeList = array();
        foreach ($tlist as $zone => $tids){
            $zoneTeamList[$zone] = array_keys($tids);
            foreach ($tids as $tid => $tcode){
                $zoneTeamCodeList[$zone][$tid] = $tcode;    
            }
        }

        $this->set('open_tasks', $or);
        $this->set('waiting_tasks', $ow);
        $this->set('teamIdCodeList', $teamIdCodeList);
        $this->set('zoneTeamCodeList', $zoneTeamCodeList);
    
        //$this->render('compile_pdf');
        $this->render('/Elements/task/open_req');    
    }
    
    public function openWaiting($team){
        $rs = $this->Task->getOpenWaitingByTeam($team);
        
        $tlist = $this->Task->Team->zoneTeamList();
        $teamIdCodeList = array();
        foreach ($tlist as $zcode => $zteams){
            foreach ($zteams as $tid =>$tcode){
                $teamIdCodeList[$tid] = $tcode;   
            }
        }
        
        $zoneTeamList = array();
        $zoneTeamCodeList = array();
        foreach ($tlist as $zone => $tids){
            $zoneTeamList[$zone] = array_keys($tids);
            foreach ($tids as $tid => $tcode){
                $zoneTeamCodeList[$zone][$tid] = $tcode;    
            }
        }

        $this->set('tasks', $rs);
        $this->set('teamIdCodeList', $teamIdCodeList);
        $this->set('zoneTeamCodeList', $zoneTeamCodeList);
    
        $this->render('compile_pdf');    
    }

    public function getOpenWaitingByTeam($team){
        $tids = $this->Task->TasksTeam->getOpenWaitingByTeam($team);
        
        $this->virtualFields['priority_date'] = 'MIN(`Task`.`due_date`, `Task`.`end_time`)';
        $contain = array(
            'Task'=>array(
                'fields'=>array(
                    'Task.id',
                    'Task.start_time',
                    'Task.end_time',
                    'Task.short_description',
                    'Task.task_type',
                    'Task.team_code',
                    'Task.task_color_code',
                    'Task.time_control',
                    'Task.time_offset',
                    'Task.priority_date',
                )
            ),
        
            'Assist'=>array(
                'fields'=>array(
                    'Assist.id',
                    'Assist.start_time',
                    'Assist.end_time',
                    'Assist.short_description',
                    'Assist.task_type',
                    'Assist.team_code',
                    'Assist.task_color_code',
                    'Assist.time_control',
                    'Assist.time_offset',
                )
            ),
            'Comment',
            //'Assist.Assist',
            'Parent'=>array(
                'fields'=>array(
                    'Parent.id',
                    'Parent.parent_id',
                    'Parent.start_time',
                    'Parent.end_time',
                    'Parent.short_description',
                    'Parent.task_type',
                    'Parent.team_code',
                    'Parent.task_color_code',
                    'Parent.time_offset',
                    'Parent.time_control',
                )
            ),
            'TasksTeam'=>array(
                'fields'=>array(
                    'TasksTeam.team_id',
                    'TasksTeam.team_code',
                    'TasksTeam.task_role_id',
                    )),
            'Change'=>array(
                'conditions'=>array('Change.created >'=>$owa),
                'fields'=>array(
                    'Change.created'))
        ); 
        
        
        $rs = $this->find('all', array(
            'conditions'=>array(
                'Task.id'=>$tids
            ),
            'contain'=>$contain,
            'order'=>'Task.priority_date ASC',
        ));
        
        $tlist = $this->Task->Team->zoneTeamList();
        $teamIdCodeList = array();
        foreach ($tlist as $zcode => $zteams){
            foreach ($zteams as $tid =>$tcode){
                $teamIdCodeList[$tid] = $tcode;   
            }
        }
        
        $zoneTeamList = array();
        $zoneTeamCodeList = array();
        foreach ($tlist as $zone => $tids){
            $zoneTeamList[$zone] = array_keys($tids);
            foreach ($tids as $tid => $tcode){
                $zoneTeamCodeList[$zone][$tid] = $tcode;    
            }
        }

        $this->set('tasks', $rs);
        $this->set('teamIdCodeList', $teamIdCodeList);
        $this->set('zoneTeamCodeList', $zoneTeamCodeList);
    
        $this->render('/Elements/task/open_req');    
        
    }


////////////////////////OLD/DEPRECATED FUNCTIONS/////////////////////////////////

  
/////END DEPRECATED//////////////////////////////// 
 

///////// EOF
}
///////// EOF