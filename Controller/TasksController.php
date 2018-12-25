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
        if (in_array($this->action, 
            array(
                //'index',
                'add',
                'view', 
                'compile',
                'timeShift',
                'addShift',
                'remShift',
                'resetShift',
                'search',
                'userPrint',
                'compilePrint',
                'linkable',
                'details',
                'checkPid',
                'getTaskById',
                'digest',
                'pdfFromSearch',
                
                //2018
                'byRole', 
                )
            )){
            return true;
        }
        
        // The owner of a task can edit and delete it
        if (in_array($this->action, 
            array(
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
 * Retrive task details by id
 * Used in loops to find parent->parent->parent
 * @param int $id Task id
 * @since 2015
 */
    public function getTaskById($id){
        return $this->Task->findById($id);
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
            'Change',
            'Assignment'
                );
        $this->Paginator->settings = array(
            'contain'=>$cont,
            'recursive'=>-1,
            'limit'=>50,
            'order'=> array(
            'Task.id'=>'desc')
        );
        $this->set('teams', $this->Task->Team->listTeamCodeByCategory());
        $this->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());
        
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
                'Task.' . $this->Task->primaryKey => $id)
        );
        
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
 * @return void
 */

 public function add() {
     
     //$this->log($this->request->data);
        if($this->request->is('ajax')){
            $this->autoLayout = false;
            $this->autoRender = false;
        }        
        
        if ($this->request->is('post')) {
            //$this->log($this->request->data);
            $t_ctrl = $this->request->data('Task.time_control');
            $new_tt = $this->request->data('TeamRoles');
            $new_assign = $this->request->data('Task.Assignments');
            
            //unset($this->request->data['Task']['Assignments']);
            //$this->request->data['Assignments'] = $new_assign;
            //$this->Task->data['Assignments'] = $new_assign;
            
            $this->Task->data['TeamRoles'] =  $new_tt;
            //$this->Task->data['Assignments'] =  $new_assign;
            //$this->log("asses");
            //$this->log($this->request->data['Assignments']);

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
                'TasksTeam',
                'Assignment',
            ), 
            'conditions' => array(
                'Task.' . $this->Task->primaryKey => $id
            )
        );

        $task = $this->Task->find('first', $options);

        if ($this->request->is('get')) {
            //throw new MethodNotAllowedException();
            $this->set('assignments', $this->Task->Assignment->Role->getListByTeam($task['Task']['team_id']));
        }

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
        $this->Task->id = $id;
        if (!$this->Task->exists()) {
            //throw new NotFoundException(__('Invalid task'));
        }
        
        if ($this->Task->delete($id, true)) {
            if($this->request->is('ajax')){
                $this->layout = false;
                $this->autoRender = false;
                $this->response->statusCode(200);
                //return "Task deleted";
                return json_encode(array('success'=>true, 'message'=>'Task deleted')); 
                //exit;       
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

/**************************
 * 
 * MAIN COMPILE FUNCTION
 * 
 * ***********************/

    public function compile(){
        //$this->log($this->request->data);
        $user_id = $this->Auth->user('id');
        $usrCurrPage = $this->Session->read('Auth.User.Compile.page');
        $order = $conditions = $settings = array();
        $limit = 25;
        $sort = '';
        $comp_is_same = true; // Used to determine if we need to save settings to session
        $usePaging = true;
        
        $curViewType = 0;

        // Set up to compare settings as submitted vs. stored in user session variable
        $tmp_sess = $this->Session->read('Auth.User.Compile');
        $tmp_tl = $this->Session->read('Auth.User.Timeline');
        $tmp_req = $this->request->data('Compile');
        $qview = (int)$this->request->query('view');
        $qehr = (int)$this->request->query('hr');
        
        if($tmp_req["view_type"] == 2 || $qview == 2 || ($this->request->is('get') && $tmp_sess['view_type']==2 && !$this->request->query('task'))){
            //$this->log('hit view type =2 in tc');
            $comp_is_same = false;
            $usePaging = false;
            $curViewType = 2;
            $this->Session->write('Auth.User.Compile.view_type',2);    
            
            if(isset($tmp_tl['hour']) && $qehr == 0){
                $settings['timeline_hr'] = $tmp_tl['hour'];
                $this->Session->write('Auth.User.Timeline.hour',$settings['timeline_hr']);
            } 
            elseif($qehr>=6 && $qehr<=30){
                $settings['timeline_hr'] = $qehr;
                $this->Session->write('Auth.User.Timeline.hour', $qehr);
            } 
            else{
                $settings['timeline_hr'] = 6;
                $this->Session->write('Auth.User.Timeline.hour',6);
            }
            
            if(!empty($tmp_req['Teams'])){
                $settings['Teams'] = $tmp_req['Teams'];
            }
            elseif(!empty($tmp_sess['Teams'])){
                $settings['Teams'] = $tmp_sess['Teams'];
            }
            
            $settings['view_type'] = 2;
        
            //$event_date = Configure::read('EventDate');
            $event_date = "2018-02-10";
            
            $settings['tl_start_date'] = date('Y-m-d H:i:s', strtotime($event_date)+60*$settings['timeline_hr']*60);
            $settings['tl_end_date'] = date('Y-m-d H:i:s', strtotime($event_date)+(60*$settings['timeline_hr']*60)+(59*60)+59);

            $settings['start_date'] = $tmp_sess['start_date'];
            $settings['end_date'] = $tmp_sess['end_date'];
            //$settings['end_date'] = $teams;


            //$this->Session->write('Auth.User.Timeline.hour',$settings['timeline_hr']);
            $this->Session->write('Auth.User.Timeline.start',$settings['tl_start_date']);
            $this->Session->write('Auth.User.Timeline.end',$settings['tl_end_date']);


            //$settings['end_date'] = $event_date;
            $settings['order'] = array('Task.start_time ASC');
        
            $this->set('timeline_hr', $settings['timeline_hr']);
        }
        // Posted => Process new compile settings
        elseif($this->request->is('post')){
            //$this->log($this->request->data);
            // If we were paging, pass the settings into the next request
            if($this->params['paging']){
                $this->Paginator->settings = $this->params['paging'];
            }
            //$this->log($tmp_req);
            $ucomp = ($tmp_sess)?: array();
            $ncomp = ($tmp_req)?: array();
            $ucomp['Teams'] = ($ucomp['Teams'])?: array();
            $ncomp['Teams'] = ($ncomp['Teams'])?: array();

            // Compare teams
            $tdiff1 = array_diff($ucomp['Teams'], $ncomp['Teams']);
            $tdiff2 = array_diff($ncomp['Teams'], $ucomp['Teams']);

            // Compare settings OTHER than teams; array_diff doesen't like sub-arrays            
            unset($ucomp['Teams']); 
            unset($ncomp['Teams']); 
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
            //$this->log('hit get');
            $settings = $this->Session->read('Auth.User.Compile');
            $comp_is_same = false;

            // Querystring Params
            $qSingle = $this->request->query('task');
            //$this->log($qSingle);
            
            //$this->log($qSingle);
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

        }//END Get
        
        //$this->log('settings before made safe');
        //$this->log($settings);

        // Process settings, set defaults if necessary        
        $cc = $this->Task->makeCompileConditions($settings);
        //debug($cc);
        //$this->log("cleaned conditions");
        //$this->log($cc);
        $teams = $cc['teams'];
        $conditions = $cc['conditions'];
        $order = $cc['order'];
        $contain = $cc['contain'];
        $limit = $cc['limit'];
        $fields = $cc['fields'];

        // If viewing a single task, overwrite conditions. $page = 1 important so paginator won't be
        // out of bounds (i.e. with $page > 1 set elsewhere in user's conditions).
        if(!empty($qSingle)){
            $conditions = array('Task.id'=>$qSingle);
            $limit = 1;
            $contain = array('Assist','TasksTeam','Assignment');
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
                    //'fields'=> $fields,
                )
            );           

            $tasks = $this->Paginator->paginate('Task');
        }
        else{
            $tasks = $this->Task->find('all', array(
                'contain'=>$contain,
                'conditions'=>$conditions,
                'order'=>$order,
                'fields'=>$fields,
            ));
        }
        
        // Set and store new compile settings, if different
        //if(!$comp_is_same && $curViewType<>2){
        if(!$comp_is_same){
            $this->Session->write('Auth.User.Compile', $settings);
        }       
    
        // Settings for Compile Options
        //$this->request->data('Compile', $settings);
        $this->set('cSettings', $settings);
        $this->set('tasks', $tasks);
        $this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        $this->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());
        $this->set('zoneNameTeamList', $this->Task->Team->Zone->listZoneNameTeamIdTeamCode());
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('user_shift', $this->Session->read('Auth.User.Timeshift'));
        //$this->set('roles', $this->Task->Team->Role->getListByTeam());
        
        $AUTH_teams = AuthComponent::user('Teams');
        $user_roles = $this->Task->Team->Role->getByTeams($AUTH_teams);

        $this->set('roles', $user_roles);
        //$this->set('roles', $this->roles);

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
        elseif($settings['view_type']==2){
            //$this->render('/Elements/task/compile_hourly');    
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
    }

    public function byRole(){
        $CONST_roles = $this->Task->Team->Role->getListByTeam();
        $this->set('rolesByTeam', $this->Task->Team->Role->getListByTeam());
        $this->set('rolesList', $this->Task->Team->Role->getList());
        
        if($this->request->is('get') && $this->request->query('view') != "pdf"){
            return $this->render('compile_by_roles');
        }
        
        $conditions = $contain = $order = $fields = $tasks = array();
        $start_date = $end_date = $view = '';

        //*********************TEMP***********************
        $start_date = "2018-01-01";
        $end_date = "2020-01-01";
        //*********************TEMP***********************

        $order = array('Task.start_time'=>'asc');
        $conditions['start_time >='] = $start_date;
        $conditions['end_time <='] = $end_date;
        $contain = array(
            'TasksTeam'=>array(
                'fields'=>array('TasksTeam.team_id', 'TasksTeam.team_code', 'TasksTeam.task_role_id')
            ),
            'Assignment'
        );

        $sessionAUC = $this->Session->read('Auth.User.Compile');

        $this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        $this->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());
        $this->set('zoneNameTeamList', $this->Task->Team->Zone->listZoneNameTeamIdTeamCode());
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());

        
        //What roles to look up? If posted, use those, if PDF, used the last ones requested (saved to prefs)
        if($this->request->is('post')){
            $userRoles = $this->request->data('SelectedRoles');
            $this->Session->write('Auth.User.CompileByRole.Roles', $userRoles);
        }
        elseif($this->request->is('get') && $this->request->query('view') == 'pdf'){
            //$this->log('hit get and pdf');
            $userRoles = AuthComponent::user('CompileByRole.Roles');
            //$this->log('user roles from auth ');$this->log($userRoles);
        }

        
        if(!empty($userRoles)){
            $tids = $this->Task->Assignment->getTaskIdsByRoles($userRoles);
        //$this->log($tids);    
            if(!empty($tids)){
                $conditions[] = array('Task.id'=>$tids);

                $tasks = $this->Task->find('all', array(
                    'contain'=>$contain,
                    'conditions'=>$conditions,
                    'order'=>$order,
                    'fields'=>$fields,
                ));
            }
        }

        $this->set('userRoles', $userRoles);
        $this->set('tasks', $tasks);
        $this->set('start_date', $start_date);
        $this->set('end_date', $end_date);

        
        if($this->request->is('ajax')){
            $this->autoLayout = false;
            $this->autoRender = false;
            return $this->render('/Elements/task/tasks_table_by_role');
        }
        
        //$this->log($this->request);
        if($this->request->is('get') && $this->request->query('view') == 'pdf'){
            $date = date('Y-m-d');
            $user = AuthComponent::user('handle');
            $ename = Configure::read('EventShortName');
            $cstart = Configure::read('CompileStart');
            $cend = Configure::read('CompileEnd');

            $view = new View($this, false);
            $view->viewPath='Elements/task';  // Directory inside view directory to search for .ctp files
            //$view->viewPath='Tasks';  // Directory inside view directory to search for .ctp files
            $view->layout='compile_pdf';
           
            //$view->set ('tasks', $tasks); // set your variables for view here
            //$view->set ('term', $term);
            //$view->set ('start_date', $start_date);
            //$view->set ('end_date', $end_date);
            //$view->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
            //$view->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());
    
            $html=$view->render('tasks_table_by_role');
            //$html=$view->render('compile_by_roles');

            $cdate = date('M j, Y');

            $this->Mpdf->init(array('format'=>'A4-L'));

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
              'even' => array()
            );
    
            $this->Mpdf->SetFooter($footer);
            $this->Mpdf->WriteHTML($html);
            $this->Mpdf->Output($ename.' Custom Role Plan '.$user.'-'.$date.'.pdf', 'D');
            
            //$this->redirect(array('controller'=>'tasks', 'action'=>'userPrint'));
            $this->layout=false;
            $this->render(false);
        }
        /*
        else{
            //$this->layout = 'compile_pdf';
            return $this->render('compile_by_roles');
            //$this->render('/Elements/task/tasks_table_pdf');
        }*/
        
    }

    public function details($task_id = null, $view_first = null){
        if (!$this->Task->exists($task_id)) {
            throw new NotFoundException(__('Invalid task'));
        }
        
        $options = array(
            'contain' => array(
                'Assignment',
                'TasksTeam',
                'Assist' => array(
                    'order' => array('Assist.created DESC')
                ),
                'Assist.TasksTeam',
                'Parent'
            ), 
            'conditions' => array(
                'Task.'.$this->Task->primaryKey => $task_id
            )
        );
        
        $task = $this->Task->find('first', $options);

        if(isset($task['Task']['time_control']) && isset($task['Task']['time_offset'])){
            $to = $this->Task->offsetToMinSecParts($task['Task']['time_offset']);
            $task['Offset']['type'] = isset($task['Task']['time_offset_type'])? $task['Task']['time_offset_type'] : -1;
            $task['Offset']['minutes'] = $to['min'];
            $task['Offset']['seconds'] = $to['sec'];
        }

        $allowTRoles = $this->Task->TasksTeam->getPossibleRolesByTask($task['Task']['team_id'],$task['Task']['id']); 
        $aInControl = $this->Task->Team->listAssistingAndControlledByUser($task['Task']['id']);
        $linkable = $this->Task->linkableParentsList($task['Task']['team_id'], $task['Task']['parent_id'], $task['Task']['id']);
        
        $this->set('task', $task);
        $this->set('teams', $this->Task->Team->listTeamCodeByCategory());
        $this->set('controlled_teams', $this->Task->Team->listControlledTeamCodeByCategory());
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        //$this->set('assignments', $this->Task->Assignment->Role->getListByTeam($task['Task']['team_id']));
        //$this->set('roles', $this->Task->Team->Role->getListByTeam());
        
        $AUTH_teams = AuthComponent::user('Teams');
        $user_roles = $this->Task->Team->Role->getByTeams($AUTH_teams);

        $this->set('roles', $user_roles);
        
        
        $this->set(compact('allowTRoles', 'aInControl', 'linkable'));
        
        if(isset($view_first)){
            $this->set('view_first', $view_first);
        }
        
       $this->render('/Elements/task/details');
  }

    //2017: Used to move a single `task_id` by `secs`  
    public function timeshiftTask(){
        if($this->request->is('ajax')){
            $task = (int)$this->request->data('task_id');
            $secs = (int)$this->request->data('secs');

            if($task > 0 && $secs != 0){
                if($this->Task->incrementTaskTime(array($task), $secs)){
                    $this->response->statusCode(200);
                    //return json_encode(array('success'=>true, 'message'=>'Task time shifted.')); 
                }
                else {
                    $this->response->statusCode(401);
                }
            }
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
                    
        //User Print Preferences
        $upref = $this->Task->PrintPref->getUserPrefsByType($user_id);
        $tasks = $this->Paginator->paginate('Task');
        $this->set('tasks', $tasks);
        $this->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());
        $this->set('PrintPrefs', $upref);
        
        if($this->request->is('ajax')){
            return $this->render('/Elements/print_preference/print_pref_list');
        }
        
        $this->render('/Elements/print_preference/print_pref');
    }

    //2015
    public function search($q=null){
         $cstart = Configure::read('CompileStart');
        $cend = Configure::read('CompileEnd');       
        //$cs = Configure::read('CompileStart');
        //$ce = Configure::read('CompileEnd');
        
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
                    ),
                    'AND'=> array(
                        'Task.start_time >=' =>$cstart,
                        'Task.end_time <=' =>$cend,
                    )                
                ),
                'order'=>'Task.start_time ASC',
                )
            );
        }
        $this->set('tasks', $rs);
        $this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        $this->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());
        $this->set ('start_date', $cstart);
        $this->set ('end_date', $cend);
        $this->set('teams', $this->Task->Team->listTeamCodeByCategory());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('search_term', $q);
    
        $this->render('search');
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
        $this->set(compact('linkable', 'team', 'current', 'child'));
        $this->set('_serialize', array('linkable', 'team', 'current', 'child'));

        $this->render('/Elements/task/linkable_parents_list');       
    }
    

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

    public function pdfFromUserSettings(){
        $settings = $this->Session->read('Auth.User.Compile');
        
        $settings['view_type'] = 1;

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
        
        $view = new View($this, false);
        //$view->viewPath='Tasks/pdf';  // Directory inside view directory to search for .ctp files
        //$view->layout='pdf/default'; // if you want to disable layout
        $view->viewPath='Elements/task';  // Directory inside view directory to search for .ctp files
        //$view->layout='default'; // if you want to disable layout
        $view->layout='compile_pdf';
       
        $view->set ('tasks', $tasks); // set your variables for view here
        $view->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        $view->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());
        //$this->set(compact('tasks','teamIdCodeList','zoneTeamCodeList'));
        //$html=$view->render('compile_pdf');
        $html=$view->render('tasks_table_pdf');

        $cdate = date('M j, Y');
        $this->Mpdf->init(array('format'=>'A4-L'));
    
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
          'even' => array()
        );

        $this->Mpdf->SetFooter($footer);
        $this->Mpdf->WriteHTML($html);
        // setting output to I, D, F, S
        $this->Mpdf->Output($ename.' Plan_'.$user.'-'.$date.'.pdf', 'D');
        
        $this->redirect(array('controller'=>'tasks', 'action'=>'userPrint'));
        $this->layout=false;
        $this->render(false);
    }

    public function pdfFromSearch($term){
        $contain = array(
            'TasksTeam'=>array(
                'fields'=>array('TasksTeam.team_id', 'TasksTeam.team_code', 'TasksTeam.task_role_id'))
        );
        
        $date = date('Y-m-d');
        $user = AuthComponent::user('handle');
        $ename = Configure::read('EventShortName');
        $cstart = Configure::read('CompileStart');
        $cend = Configure::read('CompileEnd');

        $tasks = $this->Task->find('all', array(
            'contain'=>$contain,
            'conditions'=>array(
                'OR' => array(
                    'Task.short_description LIKE' => "%$term%",
                    'Task.details LIKE' => "%$term%"
                ),
                
                'AND'=> array(
                    'Task.start_time >=' =>$cstart,
                    'Task.end_time <=' =>$cend,
                )
                 
                
            ),
            'order'=>'Task.start_time ASC',
        ));

        $view = new View($this, false);
        $view->viewPath='Elements/task';  // Directory inside view directory to search for .ctp files
        $view->layout='compile_pdf';
       
        $view->set ('tasks', $tasks); // set your variables for view here
        $view->set ('term', $term);
        $view->set ('start_date', $cstart);
        $view->set ('end_date', $cend);
        $view->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        $view->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());

        $html=$view->render('tasks_table_from_search');

        $cdate = date('M j, Y');
        $this->Mpdf->init(array('format'=>'A4-L'));
    
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
          'even' => array()
        );

        $this->Mpdf->SetFooter($footer);
        $this->Mpdf->WriteHTML($html);
        $this->Mpdf->Output($ename.' Plan_'.$user.'-'.$date.'.pdf', 'D');
        
        //$this->redirect(array('controller'=>'tasks', 'action'=>'userPrint'));
        $this->layout=false;
        $this->render(false);
    }

    public function digest($team){
        //$tids = $this->Task->digestByTeam($team);
        $rs = $this->Task->getDigestDataByTeam($team);
                
        $this->set('team_code', $rs['Team']['team_code']);
        $this->set('next_meeting', $rs['next_meeting']);
        $this->set('recent_requests', $rs['recent_requests']);
        $this->set('recent_links', $rs['recent_links']);
        $this->set('urgent_tasks', $rs['urgent_tasks']);
        $this->render('/Elements/task/digest');
    }
  
    public function manageDigest(){
        $teams_list = $this->Task->Team->listTeams();
        $teams_ids = array_keys($teams_list);
        $counts = $this->Task->Team->Task->getDigestDataCountByTeams($teams_ids);

        $this->set('data', $counts);
        $this->set('digestUsers', $this->Task->Team->TeamsUser->getDigestUsersByTeam());
        $this->set('teamsList', $teams_list);
        $this->render('/Elements/digest/team_digest');
    }

    public function sendDigestToTeam($team){
        $emails = $this->Task->sendDigestToTeam($team);
        $this->set('data', $teamsusers);
        
        if($emails['success'] == true){
            $this->Session->setFlash(__('Digest sent to team successfully'), 'flash/email_list', array('sent'=>$emails['sent']));
        }
        else{
            $this->Session->setFlash(__('Sending to some users failed.'), 'flash/email_list', array('failed'=>$failed));    
        }
      
        return $this->redirect($this->referer());
    }















/*
    public function eventHourly($hr=null){
        $pref_teams = array();
        $pref_hour = AuthComponent::user('Compile.Timeline.hour');
        $pref_teams = array_values(AuthComponent::user('Compile.Teams'));
        
        //debug($teams);
        $hour = 0;
        if(!isset($hr)){
            if(isset($pref_hour)){
                $hr = $pref_hour;
            }
        }    
        if(isset($hr) && $hr >= 0 && $hr <=30){
            $hour = min($hr, 30);    
        }
        else{
            $hour = 6;
        }
        
        //$team_id = $this->Task->TasksTeam->Team->getTeamIdByCode($team);
        //debug($team_id);
        //$edate = Configure::read('EventDate');
        $edate = "2018-02-10";
        
        $time_s = date('Y-m-d H:i:s', strtotime($edate)+$hour*60*60);
        $time_e = date('Y-m-d H:i:s', strtotime($time_s) + (59*60)+59);
        $user_id = $this->Auth->user('id');
        $usrCurrPage = $this->Session->read('Auth.User.Compile');
        $order = $conditions = array();
        
        $conditions['AND'] = array('Task.team_id'=>$pref_teams);
        $conditions['OR'] = array(
            array(  //Starts during, ends after
                'AND'=>array(
                    'Task.start_time >='=> $time_s,
                    'Task.start_time <='=> $time_e,
                ),
                'Task.end_time >'=> $time_e,               
            ),
            array( //Starts before, ends during
                'Task.start_time <='=> $time_e,
                'AND'=>array(
                    'Task.end_time >='=> $time_s,
                    'Task.end_time <='=> $time_e,   
                )
            ),
            array(  //Start before, ends after
                'Task.start_time <='=> $time_s,
                'Task.end_time >'=> $time_e,               
            ),
        );
        
        $rs = $this->Task->find('all', array(
            'contain'=>array(
                'Assignment'=>array(
                    'conditions'=>array(
                        'Assignment.id >' => 0,
                    ),
                    'fields'=>array(
                        'role_handle',
                    )
                )
            ),
            'order'=>array('Task.start_time ASC'),
            'conditions'=>$conditions,
            'fields'=>array('id', 'team_code', 'task_type', 'start_time', 'end_time', 'short_description')
        ));
        
        $tasks = Hash::combine($rs,'{n}.Task.id','{n}');
        
        //$this->set('current_team_code', $team_code);
        $this->set('hour', $hour);
        $this->set('time_range', array('start'=>$time_s, 'end'=>$time_e));
        $this->set('tasks', $tasks);
        $this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        $this->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());
        $this->set('zoneNameTeamList', $this->Task->Team->Zone->listZoneNameTeamIdTeamCode());
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        
        $this->Session->write('Auth.User.Timeline.teams', $pref_teams);
        $this->Session->write('Auth.User.Timeline.hour', $hour);
        
        if($this->request->is('post')){
            
        }
        $this->render('by_role');
        //$this->render('/Elements/Utility/debug');
    }
*/

/*
    public function teamEventHourly(){
        $q_team = $this->request->query('team');
        $q_hour = $this->request->query('hour');
        $ses_team = $this->Session->read('Auth.User.Timeline.team');
        $ses_hour = $this->Session->read('Auth.User.Timeline.hour');
        
        $hour_t = $hour = 0;
        
        $team = null;
        if($q_team == "ALL" || $q_team == "all"){
            $team = null;
        }
        elseif($this->Task->Team->existsByTeamCode($q_team)){
            $team = $q_team;
            //debug($q_team);
        }
        
        if($q_hour){
            $hour_t = $q_hour;
        }
        elseif($ses_hour && !$q_hour){
            $hour_t = $ses_hour;
        }
        else{
            $hour_t = 10; // Defaults to 10am on event day
        }

        if(is_numeric($hour_t) && $hour_t<=24){
            $hour = $hour_t;
        }
        else{
            $am = stripos($hour_t, 'am');
            $pm = stripos($hour_t, 'pm');
            
            if($am){
                $hour = substr($hour_t, 0, $am);
                if($hour > 11){
                    $hour = $hour - 12;
                }
            }
            elseif($pm){
                $hour = substr($hour_t, 0, $pm);
                if($hour != 12){
                    $hour = substr($hour_t, 0, $pm)+12;
                }
            }
        }

    
        
        
        $team_id = $this->Task->TasksTeam->Team->getTeamIdByCode($team);
        //debug($team_id);
        $edate = Configure::read('EventDate');
        
        $team_code = (!empty($team_id))? strtoupper($team) : "ALL";
        
        $time_s = date('Y-m-d H:i:s', strtotime($edate)+$hour*60*60);
        $time_e = date('Y-m-d H:i:s', strtotime($time_s) + (59*60)+59);
        $user_id = $this->Auth->user('id');
        $usrCurrPage = $this->Session->read('Auth.User.Compile');
        $order = $conditions = array();
        
        if($team_id){
            $conditions['AND'] = array('Task.team_id'=>$team_id);
            //array_push($conditions, array('Task.team_id'=>$team_id));
        }
        $conditions['OR'] = array(
            array(  //Starts during, ends after
                'AND'=>array(
                    'Task.start_time >='=> $time_s,
                    'Task.start_time <='=> $time_e,
                ),
                'Task.end_time >'=> $time_e,               
            ),
            array( //Starts before, ends during
                'Task.start_time <='=> $time_e,
                'AND'=>array(
                    'Task.end_time >='=> $time_s,
                    'Task.end_time <='=> $time_e,   
                )
            ),
            array(  //Start before, ends after
                'Task.start_time <='=> $time_s,
                'Task.end_time >'=> $time_e,               
            ),
        );
        
        $rs = $this->Task->find('all', array(
            'contain'=>array(
                'Assignment'=>array(
                    'conditions'=>array(
                        'Assignment.id >' => 0,
                    ),
                    'fields'=>array(
                        'role_handle',
                    )
                )
            ),
            'order'=>array('Task.start_time ASC'),
            'conditions'=>$conditions,
            'fields'=>array('id', 'team_code', 'task_type', 'start_time', 'end_time', 'short_description')
        ));
        
        $tasks = Hash::combine($rs,'{n}.Task.id','{n}');
        $this->set('current_team_code', $team_code);
        $this->set('time_range', array('start'=>$time_s, 'end'=>$time_e));
        $this->set('tasks', $tasks);
        $this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        $this->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());
        $this->set('zoneNameTeamList', $this->Task->Team->Zone->listZoneNameTeamIdTeamCode());
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        
        $this->Session->write('Auth.User.Timeline.team', $team);
        $this->Session->write('Auth.User.Timeline.hour', $hour);
        
        if($this->request->is('post')){
            
        }
        $this->render('by_actor');
        //$this->render('/Elements/Utility/debug');
    }

*/


/*
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
*/


/*    
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
*/





///////// EOF
}
///////// EOF