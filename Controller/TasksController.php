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
 * @var array
 */
    public $components = array('Paginator', 'Mpdf.Mpdf');
    
    //public $CONST_EventDate = Configure::read('EventDate');

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
                'quickAdd',
                'timeshiftTask',
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
 * @return void
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
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function view($id = null) {
        if (!$this->Task->exists($id)) {
            throw new NotFoundException(__('Invalid task'));
        }

        $owa = date('Y-m-d', strtotime("-1 weeks"));

        $contain = array(
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
            'contain'=>$contain, 
            'conditions' => array(
                'Task.id' => $id)
        );
        
        //debug($this->Task->Team->listControlledTeamCodeByCategory());
        //debug(AuthComponent::user('TeamsByZone'));
        
        $this->set('task', $this->Task->find('first', $options));
        $this->set('teamsList', $this->Task->Team->find('list'));
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        //$this->set('controlled_teams', $this->Task->Team->listControlledTeamCodeByCategory());
        $this->set('controlled_teams', AuthComponent::user('TeamsByZone'));
        $this->set('user_controls', $this->Session->read('Auth.User.Teams'));
        $this->set('teams', $this->Task->Team->listTeamCodeByCategory());
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
        if ($this->request->is('post')) {
            //$this->log($this->request->data);
            $t_ctrl = $this->request->data('Task.time_control');
            $new_tt = $this->request->data('TeamRoles');
            $new_assign = $this->request->data('Task.Assignments');
            
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
                    $this->autoLayout = false;
                    $this->autoRender = false;
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
         
        $this->set('controlled_teams', AuthComponent::user('TeamsByZone'));
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
        $this->set('controlled_teams', AuthComponent::user('TeamsByZone'));
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

/*************************************************
    MAIN COMPILE FUNCTION

    Default Compile settings: 
        Teams: AuthComponent::user('Teams')
        Start Date: Config::read('CompileStart')
        End Date: Config::read('CompileEnd');
        View Type: 1 (rundown)
  
**************************************************/

    public function compile_NEW(){
        //$this->log($this->request->data);
        //$user_id = $this->Auth->user('id');
        //$usePaging = true;
        //$usrcurViewType = 0;
        $order = $conditions = $settings = array();
        $limit = 25;
        $sort = '';
        $comp_is_same = $usePaging = true; // Used to determine if we need to save settings to session

        //*********TEMP***************
        $event_date = Configure::read('EventDate');
        //$event_date = "2018-02-09";

        // Set up to compare settings as submitted vs. stored in user session variable
        //$tmp_sess = $this->Session->read('Auth.User.Compile');
        //$tmp_tl = $this->Session->read('Auth.User.Timeline');

        $old_compile = $AU_Compile = $this->Session->read('Auth.User.Compile');
        //$old_compile = $AU_Compile = AuthComponent::user('Compile');
        $AU_timeline = $this->Session->read('Auth.User.Timeline');
        $AUC_page = isset($AU_Compile['page'])? $AU_Compile['page']:null;

        $new_compile = $TRD_Compile = $this->request->data('Compile');
        
        $qView = $this->request->query('view'); 
        $qSingle = $this->request->query('task');
        $qPage = $this->request->query('page');
        $qHr = (int)$this->request->query('hr');
        $qSrc = $this->request->query('src');
        
        // If we were paging, pass the settings into the next request
        if($this->params['paging']){
            //$this->log($this->params['paging']);
            $this->Paginator->settings = $this->params['paging'];
        }
        
        
        if(isset($qView) && $qView == 2 || $this->request->is('post') && (is_array($TRD_Compile) && isset($TRD_Compile['view_type']) && $TRD_Compile['view_type'] == 2)){
        //if(isset($qView) && $qView == 2 || ($this->request->is('get') && $AU_Compile['view_type'] == 2 || $this->request->is('post') && (is_array($TRD_Compile) && isset($TRD_Compile['view_type']) && $TRD_Compile['view_type'] == 2))){
            //$this->log('hit view type =2 in TasksController');
            $comp_is_same = false;
            $usePaging = false;
            $curViewType = 2;
            
            if(isset($AU_timeline['hour']) && $qHr == 0){
                $settings['timeline_hr'] = $AU_timeline['hour'];
            } 
            elseif($qHr >= 6 && $qHr <= 30){
                $settings['timeline_hr'] = $qHr;
            } 
            else{
                $settings['timeline_hr'] = 6;
            }

            $settings['Teams'] = (!empty($TRD_Compile['Teams']))? $TRD_Compile['Teams'] : $AU_Compile['Teams'];
            $settings['tl_start_date'] = date('Y-m-d H:i:s', strtotime($event_date)+60*$settings['timeline_hr']*60);
            $settings['tl_end_date'] = date('Y-m-d H:i:s', strtotime($event_date)+(60*$settings['timeline_hr']*60)+(59*60)+59);
            $settings['view_type'] = 2;
            $settings['order'] = array('Task.start_time ASC');
            $settings['start_date'] = $AU_Compile['start_date'];
            $settings['end_date'] = $AU_Compile['end_date'];
            //$settings['end_date'] = $event_date;
            
            $out = array('start'=>$settings['tl_start_date'],'end'=>$settings['tl_end_date'],'hour'=>$settings['timeline_hr']);
            //$this->Session->write('Auth.User.Timeline.hour',$settings['timeline_hr']);
            $this->Session->write('Auth.User.Timeline.start', $settings['tl_start_date']);
            $this->Session->write('Auth.User.Timeline.end', $settings['tl_end_date']);
            $this->Session->write('Auth.User.Timeline.hour', $settings['timeline_hr']);
            $this->Session->write('Auth.User.Compile.view_type', 2);    

            $this->set('timeline_hr', $settings['timeline_hr']);
        }//END TIMELINE
        // Posted => Process new compile settings
        elseif($this->request->is('post')){
            
            //$this->log($this->request->data);

            $settings['view_type'] = $TRD_Compile['view_type'];
            $settings['page'] = isset($TRD_Compile['page'])?$TRD_Compile['page']:1;
            
            // Compare teams
            $old_teams = ($old_compile['Teams'])?:array();
            $new_teams = ($new_compile['Teams'])?:array();
            $chg_teams = (array_diff($new_teams, $old_teams))?:array();
            
            // Compare settings OTHER than teams; array_diff doesen't like sub-arrays            
            unset($old_compile['Teams']);
            unset($new_compile['Teams']);
            
            $chg_sets = array();
            if(is_array($new_compile) && is_array($old_compile)){
                $chg_sets = (array_diff_assoc($new_compile, $old_compile))?:array();    
            }
            
            if(!empty($chg_teams) || !empty($chg_sets)){                        
                $comp_is_same = false;
            }
            
            $settings = $this->Task->makeSafeCompileSettings($TRD_Compile);
            
            //if(!$comp_is_same){
                //$settings['page'] = 1;
            //}
        }//END POST
        elseif($this->request->is('get') && $this->request->query('src')=='compile' && $this->request->query('paging') == 1){
            $AUCP = $this->Session->read('Auth.User.CompileParams');
            ////$this->log('hit reuse settings');
            //$this->log($AUCP);
            $teams = $AUCP['teams'];
            $conditions = $AUCP['conditions'];
            $order = (isset($AUCP['order']))?$AUCP['order']:array();
            $contain = (isset($AUCP['contain']))?$AUCP['contain']:array();
            $limit = (isset($AUCP['limit']))?$AUCP['limit']:array();
            $fields = (isset($AUCP['fields']))?$AUCP['fields']:array();
            $page = ($this->request->data('page'))?:1;
            
            goto SkipToPaging;
        
        }//End GET
        
        elseif($this->request->is('get') && $this->request->query('paging') == null){
            //$this->log($this->request->query);
            $this->log('hit generic get');
            $comp_is_same = false;
            $settings = $AU_Compile;

            if(!empty($qView)){
                if($qView == 'plain' || $qView == 'pdf' || $qView = 'excel'){
                    //if($qView == 'plain'){
                    $settings['view_type'] = 1;        
                }
            }

            if($qPage>0){
                $settings['page'] = $page = $qPage;
                $this->Session->write('Auth.User.Compile.page', $qPage);
            }
            // Didn't submit a request, use saved value
            elseif(!$qPage && !$qSrc){
                $settings['page'] = $page = $AUC_page;
            }
            // Paging from compile. PaginatorHelper does ?src=compile for page=1, but ?src=compile&page=## for all others
            elseif(!$qPage && ($qSrc == 'compile')){
                $settings['page'] = $page = 1;
            }
            // When refreshing list in "Success" ajax callbacks (edit, add, delete)
            elseif(!$qPage && ($qSrc == 'action' || $qSrc == 'ajax')){
                $settings['page'] = $page = $AUC_page;
            }
        }
        
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
        
        $this->Session->write('Auth.User.CompileParams', compact('teams','conditions','orer','contain','limit','field','page'));    
        

        // If viewing a single task, overwrite conditions. $page = 1 important so paginator won't be
        // out of bounds (i.e. with $page > 1 set elsewhere in user's conditions).
        if(!empty($qSingle)){
            $conditions = array('Task.id'=>$qSingle);
            $limit = 1;
            $contain = array('Assist','TasksTeam','Assignment');
            $page = 1;
            $this->set('single_task', (int)$qSingle);
        }
        elseif($qView == 'pdf'){
            // Uses user's currently selected compile settings and forces download of PDF in browser
            $this->pdfFromUserSettings();
        }
        elseif($qView == 'plain' || $qView == 'excel'){
            $usePaging = false;
            $upref = $this->Task->PrintPref->getUserPrefsByType($this->Auth->user('id'));
            $this->set('printPrefs', $upref);
            
        }
        elseif(isset($qSearch)){
            $conditions = array(
                'OR' => array(
                    'Task.short_description LIKE' => "%$qSearch%",
                    'Task.details LIKE' => "%$qSearch%"
                ));
            $order = 'Task.start_time ASC';
            $this->set('search_term', $qSearch);
        }
    
        $page = (isset($page))? $page : 1;

SkipToPaging:
        if($usePaging){
            $this->Paginator->settings = array(
                'Task'=>array(
                    'contain'=>$contain,
                    'paramType'=>'querystring',
                    'limit'=>($limit)?: 25,
                    'conditions'=>$conditions,
                    'order'=>$order,
                    'page'=> $page,
                    'fields'=> $fields,
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
        if(!$comp_is_same){
            $this->Session->write('Auth.User.Compile', $settings);
        }       
    
        // Settings for Compile Options
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

        if($this->request->is('ajax')){
            return $this->render('/Elements/task/compile_screen');
        }
        
        //$this->log('value of qView before choosing layout: '.$qView);
        if($qView == 'plain'){
            $this->layout = 'compile_pdf';
            $this->render('/Elements/task/tasks_table');
        }
        elseif($qView == 'excel'){
            $this->layout = 'pdf/default';
            $this->render('/Tasks/pdf/compile');
        }
        // PDF View, in browser
        elseif($qView == 'pib'){
            $upref = $this->Task->PrintPref->getUserPrefsByType($this->Auth->user('id'));
            $this->set('printPrefs', $upref);

            $this->layout = 'compile_pdf';
            $this->render('/Elements/task/tasks_table_pdf');
        }
    }//end compile

    public function compile(){
        //$this->log($this->request->data);
        //$user_id = $this->Auth->user('id');
        //$usePaging = true;
        //$usrcurViewType = 0;
        $order = $conditions = $settings = array();
        $limit = 25;
        $sort = '';
        $comp_is_same = $usePaging = true; // Used to determine if we need to save settings to session

        $event_date = Configure::read('EventDate');

        // Set up to compare settings as submitted vs. stored in user session variable
        $old_compile = $AU_Compile = $this->Session->read('Auth.User.Compile');
        $AU_timeline = $this->Session->read('Auth.User.Timeline');
        $AUC_page = isset($AU_Compile['page'])? $AU_Compile['page']:null;

        $new_compile = $TRD_Compile = $this->request->data('Compile');

        //$this->log($this->params);
        
        // If we were paging, pass the settings into the next request
        //if($this->params['paging']){
            //$this->log($this->params['paging']);
        //    $this->Paginator->settings = $this->params['paging'];
        //}
        if($this->request->query('view') == 2 || $this->request->is('post') && (is_array($TRD_Compile) && isset($TRD_Compile['view_type']) && $TRD_Compile['view_type'] == 2)){
        //if(isset($qView) && $qView == 2 || ($this->request->is('get') && $AU_Compile['view_type'] == 2 || $this->request->is('post') && (is_array($TRD_Compile) && isset($TRD_Compile['view_type']) && $TRD_Compile['view_type'] == 2))){
            //$this->log('hit view type =2 in TasksController');
            $comp_is_same = false;
            $usePaging = false;
            $curViewType = 2;
            
            if(isset($AU_timeline['hour']) && $qHr == 0){
                $settings['timeline_hr'] = $AU_timeline['hour'];
            } 
            elseif($qHr >= 6 && $qHr <= 30){
                $settings['timeline_hr'] = $qHr;
            } 
            else{
                $settings['timeline_hr'] = 6;
            }

            $settings['Teams'] = (!empty($TRD_Compile['Teams']))? $TRD_Compile['Teams'] : $AU_Compile['Teams'];
            $settings['tl_start_date'] = date('Y-m-d H:i:s', strtotime($event_date)+60*$settings['timeline_hr']*60);
            $settings['tl_end_date'] = date('Y-m-d H:i:s', strtotime($event_date)+(60*$settings['timeline_hr']*60)+(59*60)+59);
            $settings['view_type'] = 2;
            $settings['order'] = array('Task.start_time ASC');
            $settings['start_date'] = $AU_Compile['start_date'];
            $settings['end_date'] = $AU_Compile['end_date'];
            //$settings['end_date'] = $event_date;
            
            //$this->Session->write('Auth.User.Timeline.hour',$settings['timeline_hr']);
            $this->Session->write('Auth.User.Timeline.start', $settings['tl_start_date']);
            $this->Session->write('Auth.User.Timeline.end', $settings['tl_end_date']);
            $this->Session->write('Auth.User.Timeline.hour', $settings['timeline_hr']);
            $this->Session->write('Auth.User.Compile.view_type', 2);    

            $this->set('timeline_hr', $settings['timeline_hr']);
        }//END TIMELINE
        // Posted => Process new compile settings
        elseif($this->request->is('post')){
            //$this->log($this->request->data);
            $settings['view_type'] = $TRD_Compile['view_type'];
            $settings['page'] = isset($TRD_Compile['page'])?$TRD_Compile['page']:1;
            $comp_is_same = false;
            $settings = $this->Task->makeSafeCompileSettings($TRD_Compile);
        }//END POST
        elseif($this->request->is('get')){
            $qSingle = $this->request->query('task');
            $qView = $this->request->query('view'); 
            $qPage = $this->request->query('page');
            $qHr = (int)$this->request->query('hr');
            $qSrc = $this->request->query('src');
                
            //$this->log($this->request->query);
            //$this->log('hit get');
            $comp_is_same = false;
            $settings = $AU_Compile;

            if(!empty($qView)){
                if($qView == 'plain' || $qView == 'pdf' || $qView = 'excel'){
                    $settings['view_type'] = 1;        
                }
            }

            if($qPage>0){
                $settings['page'] = $page = $qPage;
                $this->Session->write('Auth.User.Compile.page', $qPage);
            }
            // Didn't submit a request, use saved value
            elseif(!$qPage && !$qSrc){
                $settings['page'] = $page = $AUC_page;
            }
            // Paging from compile. PaginatorHelper does ?src=compile for page=1, but ?src=compile&page=## for all others
            elseif(!$qPage && ($qSrc == 'compile')){
                $settings['page'] = $page = 1;
            }
            // When refreshing list in "Success" ajax callbacks (edit, add, delete)
            elseif(!$qPage && ($qSrc == 'action' || $qSrc == 'ajax')){
                $settings['page'] = $page = $AUC_page;
            }
        }//End GET

        // Process settings, set defaults if necessary        
        //$cc = $this->Task->makeCompileConditions($settings);
        $cc = array('teams','conditions','order','contain','limit','fields');
        //debug($cc);
        //$this->log("cleaned conditions");
        //$this->log($cc);
        $teams = $cc['teams'];
        $conditions = $cc['conditions'];
        $order = $cc['order'];
        $contain = $cc['contain'];
        $limit = $cc['limit'];
        $fields = $cc['fields'];
        
        $this->Session->write('Auth.User.CompileParams', compact('teams','conditions','order','contain','limit','fields','page'));
        

        // If viewing a single task, overwrite conditions. $page = 1 important so paginator won't be
        // out of bounds (i.e. with $page > 1 set elsewhere in user's conditions).
        if(!empty($qSingle)){
            $conditions = array('Task.id'=>$qSingle);
            $limit = 1;
            $contain = array('Assist','TasksTeam','Assignment');
            $page = 1;
            $this->set('single_task', (int)$qSingle);
        }
        elseif($qView == 'pdf'){  // Uses user's currently selected compile settings and forces download of PDF in browser
            $this->pdfFromUserSettings();
        }
        elseif($qView == 'plain' || $qView == 'excel'){
            $usePaging = false;
            $upref = $this->Task->PrintPref->getUserPrefsByType($this->Auth->user('id'));
            $this->set('printPrefs', $upref);
        }
        elseif(isset($qSearch)){
            $conditions = array(
                'OR' => array(
                    'Task.short_description LIKE' => "%$qSearch%",
                    'Task.details LIKE' => "%$qSearch%"
                ));
            $order = 'Task.start_time ASC';
            $this->set('search_term', $qSearch);
        }
    
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
                    'fields'=> $fields,
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
        if(!$comp_is_same){
            $this->Session->write('Auth.User.Compile', $settings);
        }       
    
        // Settings for Compile Options
        $this->set('cSettings', $settings);
        $this->set('tasks', $tasks);
        $this->set('teamIdCodeList', $this->Task->Team->teamIdCodeList());
        $this->set('zoneTeamCodeList', $this->Task->Team->Zone->listZoneCodeTeamIdTeamCode());
        $this->set('zoneNameTeamList', $this->Task->Team->Zone->listZoneNameTeamIdTeamCode());
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        //$this->set('user_shift', $this->Session->read('Auth.User.Timeshift'));
        $this->set('user_shift', AuthComponent::user('Timeshift'));
        //$this->set('roles', $this->Task->Team->Role->getListByTeam());
        
        //$AUTH_teams = AuthComponent::user('Teams');
        $user_roles = $this->Task->Team->Role->getByTeams(AuthComponent::user('Teams'));
        $this->set('roles', $user_roles);

        if($this->request->is('ajax')){
            return $this->render('/Elements/task/compile_screen');
        }
        
        //$this->log('value of qView before choosing layout: '.$qView);
        if($qView == 'plain'){
            $this->layout = 'compile_pdf';
            $this->render('/Elements/task/tasks_table');
        }
        elseif($qView == 'excel'){
            $this->layout = 'pdf/default';
            $this->render('/Tasks/pdf/compile');
        }
        // PDF View, in browser
        elseif($qView == 'pib'){
            $upref = $this->Task->PrintPref->getUserPrefsByType(AuthComponent::user('id'));
            $this->set('printPrefs', $upref);

            $this->layout = 'compile_pdf';
            $this->render('/Elements/task/tasks_table_pdf');
        }
    }//end compile
    
    public function byRole(){
        $AUTH_teams = AuthComponent::user('Teams');
            
        $CONST_roles = $this->Task->Team->Role->getListByTeam();
        //$this->set('rolesByTeam', $this->Task->Team->Role->getListByTeam());
        $this->set('rolesByTeam', $this->Task->Team->Role->getListByTeam($AUTH_teams));
        $this->set('rolesList', $this->Task->Team->Role->getList());
        
        if($this->request->is('get') && $this->request->query('view') != "pdf"){
            return $this->render('compile_by_roles');
        }
        
        $conditions = $contain = $order = $fields = $tasks = array();
        $start_date = $end_date = $view = '';

        $start_date = Configure::read('CompileStart');
        $end_date = Configure::read('CompileEnd');

        //*********************@TODO TEMP***********************
        //$start_date = "2018-10-01";
        //$end_date = "2020-01-01";
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
                'Assignment'=>$this->stdContain['Assignment'],
                'TasksTeam'=>$this->stdContain['TasksTeam'],
                'Assist' => array(
                    'order' => array('Assist.created DESC')
                ),
                //'Assist.TasksTeam',
                'Parent'=>$this->stdContain['Parent']
            ), 
            'conditions' => array(
                'Task.'.$this->Task->primaryKey => $task_id
            ),
            'fields'=>$this->stdFields
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
        $this->set('controlled_teams', AuthComponent::user('TeamsByZone'));
        $this->set('actionableTypes', $this->Task->ActionableType->makeList());
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('aInControl', $aInControl);
        
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
                    return json_encode(array('success'=>true, 'message'=>'Task time shifted.')); 
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
        $ses['view_type'] = 1;
        
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

    //Find linkable parent tasks from $team.  Current selecion is $current for task.id = $child.
    public function linkable($team=null, $current=null, $child=null){
        if($this->request->is('ajax')){
            $team = $this->request->data('team');
            $current = $this->request->data('current');
            $child = $this->request->data('child');
        }

        $linkable = $this->Task->linkableParentsList($team, $child);
        
        if(!empty($this->request->params['requested'])){ 
            return $linkable;
        }
        
        $this->set('linkable', $linkable);
        $this->set('team', $team);
        $this->set('current', $current);
        $this->set('child_task', $child);
        //$this->set(compact('linkable', 'team', 'current', 'child'));
        $this->set('_serialize', array('linkable', 'team', 'current', 'child'));

        $this->render('/Elements/task/linkable_parents_list');       
    }
    

    public function checkPid(){
        if (!$this->request->is('ajax')) {
            throw new MethodNotAllowedException();
        }
        
        $task = $this->request->data('task');
        $new_par = $this->request->data('parent');
        $this->layout = false;
        $this->autoRender = false;
            
        if($new_par){
            $data = array('allow_parent'=>!$this->Task->isChildInPidChain($task, $new_par));    
            return json_encode($data);
        }
    }

    public function nextOpsMeeting(){
        return $this->Task->getNextOpsMeeting();
    }

    public function pdfFromUserSettings(){
        
        $AU_id = AuthComponent::user('id');
        $settings = $this->Session->read('Auth.User.Compile');
        
        if($settings['view_type'] ==2){
            $settings['view_type'] = 1;
        }

        //$this->log($settings);
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

        $upref = $this->Task->PrintPref->getUserPrefsByType($AU_id);
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
        //$this->log($this->Mpdf);
        $cdate = date('g:iA M j, Y');
        $this->Mpdf->init(array('format'=>'A4-L'));
    
        $footer = array (
          'odd' => array (
            'L' => array (
              'content' => 'As of '.$cdate,
              'font-size' => 9,
              'font-style' => '',
              'font-family' => 'sans-serif',
              'color'=>'#333'
            ),
            'C' => array (
              'content' => '',
              'font-size' => 10,
              'font-style' => 'B',
              'font-family' => 'sans-serif',
              'color'=>'#000000'
            ),
            'R' => array (
              'content' => 'Page {PAGENO} of {nb}',
              'font-size' => 9,
              'font-style' => '',
              'font-family' => 'sans-serif',
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



    public function quickAdd(){
        $this->set('taskTypes', $this->Task->TaskType->makeListByCategory());
        $this->set('taskColors', $this->Task->TaskColor->makeCodeAndNameList());
        $this->set('actionableTypes', $this->Task->ActionableType->find('list'));
        $this->set('teams', $this->Task->Team->listTeamCodeByCategory());
        $this->set('roles', $this->Task->Team->Role->getByTeams(AuthComponent::user('Teams')));
        
        
        return $this->render('/Elements/task/quick_add2');
        
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