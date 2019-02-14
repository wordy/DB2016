<?php
    if (AuthComponent::user('id')){
        $userTeams = AuthComponent::user('Teams');
    }
    
    $single_task = (isset($single_task))? $single_task:0;
    $search_term = (isset($search_term))? $search_term:null;
    $start_date = $this->Session->read('Auth.User.Compile.start_date');
    $end_date = $this->Session->read('Auth.User.Compile.end_date');
    $comp_teams = $this->Session->read('Auth.User.Compile.Teams');
    $sort = $this->Session->read('Auth.User.Compile.sort');
    $view_type = $this->Session->read('Auth.User.Compile.view_type');
    //$view_links = ($this->Session->read('Auth.User.Compile.view_links'))? 1:0;
    //$view_details = ($this->Session->read('Auth.User.Compile.view_details'))? 1:0;
    //$view_threaded = ($this->Session->read('Auth.User.Compile.view_threaded'))? 1:0;
    $view_threaded = 1;
    $timeline_hr = $this->Session->read('Auth.User.Compile.timeline_hr');
    $user_shift = $this->Session->read('Auth.User.Timeshift');
    $timeshift_mode = $this->Session->read("Auth.User.Timeshift.Mode");
    $timeshift_unit = $this->Session->read("Auth.User.Timeshift.Unit");
    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));  

    $this->Paginator->options(array(
        'update' => '#taskListWrap',
        'evalScripts' => true,
        'before' => $this->Js->get('#global-busy-indicator')->effect('fadeIn', array('buffer' => false)),
        'complete' => $this->Js->get('#global-busy-indicator')->effect('fadeOut', array('buffer' => false)),
        'url' => array('controller' => 'tasks', 'action' => 'compile','?'=>array('src'=>'compile', 'paging'=>true))
    ));

    //$cURL = $this->params->here;
    if($single_task != 0){
        $view_type = 1;
        $this->Js->buffer("
            // Show details right away if viewing single task
            $('#taskListWrap').find('.task-panel-heading').trigger('click');
            //var tph = $('#taskListWrap').find('.task-panel-heading');
            //$('div.isChild').show();
            //$('div.divTaskDetails').show();  
            //$('div.taskLinkages').show();   
        ");
    }
    elseif($view_type == 1){
        $this->Js->buffer("
            var ses_threaded = ".$view_threaded.";
            if(ses_threaded == 1){
                //$('div.isChild').hide();
            }
            
            /*TEMP*/
            
            $('#coDateRange').attr('disabled', false);
            $('#coSort').attr('readonly', false);
        ");
    }
    elseif($view_type == 10){
        $this->Js->buffer("
            $('#coDateRange').attr('disabled', false);
            $('#coSort').attr('readonly', false);
        ");
    }
    elseif($view_type == 500){
        $this->Js->buffer("
            $('#coTeams').addClass('hidden');
            var shtml = '<i class=\"fa fa-eye\"></i> Show Completed';
            var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Completed';                                   
            $('#aiHideComp').on('click', function(){
                if($(this).hasClass('ai_hidden')){
                    $('div.aiCompleted').show();
                    $(this).removeClass('ai_hidden').addClass('ai_shown');
                    $(this).html(hhtml);    
                }
                else{
                    $('div.aiCompleted').hide();
                    $(this).removeClass('ai_shown').addClass('ai_hidden');
                    $(this).html(shtml); 
                }
            });
            $('#coDateRange').attr('disabled', true);
            $('#coSort').attr('readonly', true);
        ");
    }    
    else{
        $this->Js->buffer("
            $('#coDateRange').attr('disabled', true);
            $('#coSort').attr('readonly', true);
        ");
    }
    
    if($timeshift_mode){
        $this->Js->buffer("
            $('.task-buttons').each(function(){
                if($(this).hasClass('canCollapse')){
                    $(this).hide();    
                }
            });
        ");
    }
    
    $this->Js->buffer("
        $('.alert-success').not('.nofade').delay(3000).fadeOut('fast');
        
        $('#coViewChildren').on('change',function(e){
            if(!$(this).is(':checked')){
                $('div.parentIsVis').hide();                 
            }
            else{
                $('div.parentIsVis').show();
            }
        });

        $('#coViewChildren').trigger('change');
        
        
        

    ");

    // Variables for what's currently being shown
    $viewTeams = array();
    $viewMessage = $viewRange = $viewTeamsStr = $viewLeadText = '';
    $viewStartDate = date('M j, Y', strtotime($start_date));
    $viewEndDate = date('M j, Y', strtotime($end_date));
    $viewSort = (int)$sort;
    $viewSort = ($viewSort == 0) ? 'ascending start time':'descending start time';    
            
    if(!empty($comp_teams)){
        foreach($comp_teams as $k=>$tid){
            $viewTeams[] = $teamIdCodeList[$tid];
        }
    
        // Oxford comma on teams list
        $viewTeamsCount = count($viewTeams);
        $last_t = array_pop($viewTeams);
        $noLastTeam = implode(', ', $viewTeams);
        
        if ($noLastTeam){
            if($viewTeamsCount > 2){
                $noLastTeam .= ', and ';    
            }
            elseif($viewTeamsCount == 2) {
               $noLastTeam .= ' and '; 
            }
        }
    
        $viewTeamStr = $noLastTeam.$last_t;
    }else{
        $viewTeamStr = '<No Teams>';
    }
    
    // Single Task
    if(!empty($single_task)){ 
        $viewMessage = 'Viewing a single task.';
    }
    elseif(isset($search_term)){
        $viewMessage = 'Viewing search results for <b>'.strtoupper($search_term).' </b>';
    }
    // Threaded
    elseif($view_type == 888){
        $viewMessage = 'Viewing tasks for <b>'.$viewTeamStr.'</b> in a <b>threaded</b> view from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by <b>'.$viewSort.'.</b>';  
    }
    // Rundown
    elseif($view_type == 1){
        $viewMessage = 'Viewing rundown for <b>'.$viewTeamStr.'</b> from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by <b>'.$viewSort.'.</b>';    
    }
    // Timeline
    elseif($view_type == 2){
        $viewMessage = 'Viewing Event Timeline for <b>'.$viewTeamStr.'</b> ordered by <b>'.$viewSort.'.</b>';
        $viewLeadText = 'Navigate forward and backwards in time, from 6am event day to 6am the day after.';    
    }
    // Lead Only
    elseif($view_type == 10){
        $viewMessage = 'Viewing tasks lead by <b>'.$viewTeamStr.'</b> from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by <b>'.$viewSort.'.</b>';
        $viewLeadText = 'Showing only tasks where the selected teams are the task Lead';
    }
    // Requests
    elseif($view_type == 30){
        $viewMessage = 'Viewing tasks where <b>'.$viewTeamStr.'</b> have Open Requests <b>from</b> other teams on <b>ANY</b> date, ordered by <b>'.$viewSort.'.</b>';
    }
    // Requests
    elseif($view_type == 31){
        $viewMessage = 'Viewing tasks where <b>'.$viewTeamStr.'</b> have Open Requests <b>to</b> other teams on <b>ANY</b> date, ordered by <b>'.$viewSort.'.</b>';
    }
    // Action Items
    elseif($view_type ==  500){
        $viewMessage = 'Showing <b>Action Items</b> from <b>ALL</b> teams from <b>ANY</b> date, ordered by <b>ascending start date.</b>';
        $viewLeadText = 'Showing Action Items for the Ops Team.  These are tasks that are important to all Ops teams.';
    }
    // Recently Modified
    elseif($view_type ==  100){
        $viewMessage = 'Viewing <b>recently modified</b> tasks involving <b>'.$viewTeamStr.'</b> from <b>ANY</b> date, ordered by <b>descending modified date.</b> (i.e most recent first)';
    }
/*
<span class="btn btn-default getTaskButton" data-tid=2771>Get Task Details</span>
<span class="btn btn-default getTaskButton" data-tid=2776>Get Task Details</span>
 */
?>
<h1><?php 
    $view = $this->Session->read('Auth.User.Compile.view_type');
    if(isset($single_task) && !empty($single_task)){
        echo 'Viewing Single Task';
    }
    elseif(isset($search_term) && !empty($search_term)){
        echo '<i class="fa fa-search"></i> &nbsp;Search Results For "'.$search_term.'"';
    }
    elseif($view == 1){
        echo '<i class="fa fa-gears"></i> &nbsp;Task Rundown'; 
    }
    elseif($view == 2){
        echo '<i class="fa fa-tasks"></i> &nbsp;Event Timeline'; 
    }
    elseif($view == 10){
        echo '<i class="fa fa-bookmark-o"></i> &nbsp;Lead Tasks Only'; 
    }
    elseif($view == 30){
        echo '<i class="fa fa-life-saver"></i> &nbsp;Open Requests - Owing'; 
    }
    elseif($view == 31){
        echo '<i class="fa fa-hourglass-half"></i> &nbsp;Open Requests - Waiting'; 
    }
    elseif($view == 100){
        echo '<i class="fa fa-refresh"></i> &nbsp;Recently Modified Tasks'; 
    }
    elseif($view == 500){
        echo '<i class="fa fa-flag"></i> &nbsp;Action Items';
    }
    else{
        echo '<i class="fa fa-gears"></i> &nbsp;Compiled Tasks'; 
    } 
?></h1>

<p class="lead"><?php echo $viewLeadText;?></p>

<div class="alert alert-info" role="alert">
<?php if (!empty($single_task) || !empty($search_term)){ ?>
    <div class="row">
        <div class="col-md-9"><?php echo $viewMessage; ?></div>
        <div class="col-md-3 hidden-print"><a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'compile'))?>" class="btn btn-default ai_hidden pull-right"> <i class="fa fa-gears"></i> Back to Compiled Tasks</a></div>
    </div>
<?php                        
    }elseif (isset($view_type) && $view_type != 500){ ?>
        <div class="row">
            <div class="col-md-12"><?php echo $viewMessage; ?></div>
        </div>
<?php                        
    }elseif ($view_type == 500){ ?>
        <div class="row">
            <div class="col-md-9"><?php echo $viewMessage; ?></div>
            <div class="col-md-3 hidden-print"><button type="button" id="aiHideComp" class="btn btn-primary pull-right"><i class="fa fa-eye-slash"></i> Hide Completed</button></div>
        </div>
<?php                        
    }else { echo $viewMessage; }
?></div>

<?php
//View 2 ==> Hourly 
if ($view == 2 && empty($single_task)){
    $userTLhr = AuthComponent::user('Timeline.hour');
    $hour = ($userTLhr>=0 && $userTLhr <=30)? $userTLhr: 6;
    
    echo $this->element('/task/compile_hourly', array(
        'hour'=>$hour,
        'tasks' => $tasks,
        'timeline_hr'=>$hour,
    ));  
} 
else{

if (!empty($tasks)){ 
    $displayed_tasks = Hash::extract($tasks,'{n}.Task.id');
    
    //debug($displayed_tasks);
    
?>
    
<div class="tasks index">
<?php if(!$single_task && $this->Paginator->param('current')):?>
    <div class="row hidden-print">
        <div class="col-xs-12" style="margin-bottom: -10px;">
            <div style="text-align:right;">
                <div>
                    <ul class="pagination sm-bot-marg" style="margin-top:0px; margin-left: auto; margin-right:auto">
                    <?php
                        $prev_lab = ($view_type == 100) ? 'Newer':'Earlier';
                        $next_lab = ($view_type == 100) ? 'Older':'Later';
                        echo $this->Paginator->prev('< ' . __($prev_lab), array('tag' => 'li'), null, array('class' => 'pagPrev disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                        echo $this->Paginator->numbers(array('separator' => '', 'class'=>'pagNum', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
                        echo $this->Paginator->next(__($next_lab) . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                    ?>
                    </ul><!-- /.pagination -->
                </div>
                <p><?php  echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records of {:count} total, starting on {:start}, ending on {:end}'))); ?></p>
            </div>
        </div><!-- /.index -->
    </div>
<?php endif;

    // Hold days of tasks
    $cur_t_day = $prev_t_day = $last_t_day = $last_t_hr = $cur_t_day = $cur_t_hr = $last_mod_day = '';
    $task_counter = 0;

    /******************************
     *  START of FOREACH $tasks   *          
     ******************************/
    foreach ($tasks as $k => $task):
        $inUsrShift = $userControls = $uControlsInvolved = $hasComment = $commentCount = $hasDueDate = $hasDueSoon = $hasActionable = $hasChange = $hasNewChange = $hasAssignment = $actor = false;
        $hoursAreSame = $daysAreSame = $modDaysAreSame = $onEday = $isPastDue = $isTimeshiftable = $isTimeControlled = false;
        $teamsInvolved = array();
        
        $tid = $task['Task']['id']; 
        $taskTO = $taskTO_type = 0;
        $teamsInvolved = Hash::extract($task['TasksTeam'],'{n}.team_id');    
        $controlledInInvolved = array_intersect($teamsInvolved, $userTeams);
        $jsCIN = array();

        foreach($controlledInInvolved as $k => $team_id){
            $jsCIN[$team_id] = $teamIdCodeList[$team_id];
        }
        $jsonCIN = json_encode($jsCIN);
    
        if(!empty($controlledInInvolved)){ $uControlsInvolved = true;}
    
        // Figure out task start date & hr & created date.  Used to group tasks by relevant headers
        $cur_t_day = date('Y-m-d', strtotime($task['Task']['start_time']));
        $cur_t_hr = date('H', strtotime($task['Task']['start_time']));
        $cur_mod_day = date('Y-m-d', strtotime($task['Task']['modified']));
        
        if($last_t_day == $cur_t_day){ $daysAreSame = true;}
        if($last_mod_day == $cur_mod_day){ $modDaysAreSame = true;}
        if($cur_t_day == $eday){ $onEday = true;}
        if($cur_t_hr == $last_t_hr){ $hoursAreSame = true; }
        
        //Hide/show elements based on permissions.
        if(in_array($task['Task']['team_id'], $userTeams)){ $userControls = true; }
        if(in_array($task['Task']['id'], $user_shift)){ $inUsrShift = true; }
        
        if(!empty($task['Task']['due_date'])){
            $dueString = strtotime($task['Task']['due_date']);
            $hasDueDate = true;
            // Highlights due 1 week from now
            if(($dueString >= $today_str) && ($dueString < $owfn)){ $hasDueSoon = true; }
            // Highlights past due tasks
            if($dueString < $today_str) {
                $hasDueSoon = true;
                $isPastDue = true; 
            }
        }
        if(!empty($task['Task']['actionable_type'])){ $hasActionable = true; }
        if(!empty($task['Task']['time_control']) && (isset($task['Task']['time_offset']))){
            $isTimeControlled = true; 
            $taskTO = $task['Task']['time_offset'];
        }
        if(!empty($task['Task']['time_offset_type']) && (isset($task['Task']['time_offset_type']))){
            $taskTO_type = $task['Task']['time_offset_type'];
        }
        if(!empty($task['Comment'])){
            $hasComment = true;
            $commentCount = count($task['Comment']); 
        }
        if(!empty($task['Change'])){
            $hasChange = true;
            $hasNewChange = true;
            $numChange = count($task['Change']);
        }
        if(!empty($task['Assignment'])){
            $hasAssignment = true;
        }
        // Can timeshift be used? Yes if user controls task and it's not time controlled by another task
        if(!$isTimeControlled && $userControls){
            $isTimeshiftable = true;
        }
?>
    <div class="row <?php echo ($task_counter > 0 && (!$daysAreSame xor $hoursAreSame) && !empty($task['Task']['parent_id']) && empty($task['Assist']))?'parentIsVis':'';?> <?php echo ($isPastDue)? 'past_due':'';?> <?php echo (isset($task['Task']['actionable_type_id']) && $task['Task']['actionable_type_id'] > 300)? 'aiCompleted':'';?>">
        <div class="col-xs-12">
        <?php
            // For recently modified, use "modified date" as basis for grouping tasks for display
            if($view_type == 100 && !$single_task && !$modDaysAreSame){
                echo '<h4 class="great">';
                echo ($cur_mod_day != $today)? $this->Time->timeAgoInWords($cur_mod_day, array('accuracy' => array('day' => 'day'), 'end' => '1 week', 'format' => 'M d')):'Today';
                echo '</h4>';
            }
            // Otherwise, use task start date/time
            elseif(!$onEday && !$daysAreSame && ($view_type != 100 || $single_task)){
                echo '<h4 class="great">'.date('M j', strtotime($cur_t_day)).'</h4>';
            }
            elseif($onEday && !$daysAreSame && ($view_type != 100 || $single_task)){
                echo '<h4 class="eday"><i class="fa fa-diamond"></i> '.date('g A', strtotime($task['Task']['start_time'])).' Event Day</h4>';
            }
            elseif($onEday && !$hoursAreSame && ($view_type != 100 || $single_task)){
                echo '<h4 class="eday"><i class="fa fa-diamond"></i> '.date('g A', strtotime($task['Task']['start_time'])).'</h4>';
            }
        ?>
        </div>
    </div>

    <div class="row <?php echo (!empty($task['Task']['parent_id']) && empty($task['Assist']))?'parentIsVis':'';?> <?php echo (isset($task['Task']['actionable_type_id']) && $task['Task']['actionable_type_id'] > 300)? 'aiCompleted':'';?>">
        <div class="col-md-12"><!-- BEGIN individual task-->
            <?php 
            /*
            echo $this->element('task/compile_view_single_task', compact(
                'task', 
                'view_type', 
                'userControls', 
                'uControlsInvolved',
                'jsonCIN',
                'hasDueDate',
                'isPastDue',
                'hasActionable',
                'hasChange',
                'hasNewChange',
                'numChange',
                'hasAssignment',
                'hasComment',
                'commentCount',
                'isTimeControlled',
                'isTimeshiftable',
                'timeshift_unit',
                'timeshift_mode',
                'taskTO',
                'taskTO_type'));*/
            ?>
            
            <div id="Task<?php echo $task['Task']['id'];?>" data-tid=<?php echo ($task['Task']['id']);?> data-task_id=<?php echo ($task['Task']['id']);?> data-team_id=<?php echo $task['Task']['team_id'];?> data-start_time="<?php echo $task['Task']['start_time'];?>" data-uconinv="<?php echo ($uControlsInvolved)?TRUE:FALSE;?>" data-cin='<?php echo $jsonCIN; ?>' id="tid<?php echo ($task['Task']['id']); ?>" class="panel panel-default task-panel" style="border-left: 8px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>">
                <div class="panel-heading task-panel-heading">
                    <div class="row">
                        <div class="col-xs-12 col-sm-3 col-md-3">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 sm-bot-marg">
                             <?php
                                if($view_type != 100){
                                    echo $this->Ops->startTimeFriendly($task['Task']['start_time'], $task['Task']['end_time'], array(
                                        'date'=>true,
                                        'line_break_duration'=>false, 
                                        'line_break_multiday'=>false, 
                                        'duration'=>true));
                                }
                                else { // When showing by created date, must show task date since overall tasks are sorted by created date (not task date)
                                    echo date('M j g:i A', strtotime($task['Task']['start_time']));
                                } 
                            ?>
                                    
                                </div>
                                <div class="col-xs-12 col-sm-12 sm-bot-marg">
                                <?php echo $this->Ops->makeTeamsSig($task['TasksTeam'], $zoneTeamCodeList, $userControls);?>    
                                </div>  
                            </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-9 col-md-7">
                            <div class="row">
                                <div class="csTaskDetails col-xs-12"><?php
                                        echo '<span class="lead"><strong><em>'.$task['Task']['task_type'].': </em></strong>&nbsp;'.$task['Task']['short_description'].'</span>';
                                        
                                        if($hasAssignment){
                                            echo '<div class="sm-top-marg">';
                                            foreach($task['Assignment'] as $n =>$ass){
                                                echo '<button type="button" class="btn btn-orange btn-xs noProp"><i class="fa fa-at"></i>&nbsp;'.$ass['role_handle'].'</button>';    
                                            }
                                            echo '</div>';
                                        }
                                   
                                        if (strlen($task['Task']['details'])>5){
                                            //echo '<pre>'.$task['Task']['details'].'</pre>';
                                            echo '<div class="divTaskDetails">';
                                            echo '<hr align="left" style="width: 98%; margin-bottom:0.3em; margin-top:0.3em; border-top: 1px solid #999;"/>';
                                            echo nl2br($task['Task']['details']);
                                            echo '</div>';
                                        }else{
                                            echo '';                                            } 
                                    ?>
                                </div>
                            </div>
            
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="task-buttons <?php if($isTimeshiftable){echo 'canCollapse';};?>" style="text-align: right;">
                                    <?php 
                                        if($hasDueDate){
                                            echo '<button type="button" class="btn btn-danger btn-xs noProp"><i class="fa fa-bell-o"></i>&nbsp;'.$this->Time->format('M d', $task['Task']['due_date']).'</button>';
                                        }
                                        if($hasActionable){
                                            echo '<button type="button" class="btn btn-danger btn-xs noProp"><i class="fa fa-flag fa-lg"></i>&nbsp;'.$task['Task']['actionable_type'].'</button>';
                                        }
                                        if($hasChange && $hasNewChange){
                                            echo '<button type="button" class="btn btn-success btn-xs noProp"><i class="fa fa-exchange"></i>&nbsp;'.$numChange.'</button>';
                                        }
                                        if($hasComment){
                                            echo '<button type="button" class="btn btn-primary btn-xs noProp"><i class="fa fa-comment-o"></i>&nbsp;'.$commentCount.'</button>';
                                        }
                                        if(($isTimeControlled) && ($taskTO != 0)){
                                            if($taskTO_type == -1 || $taskTO_type == 1){
                                                echo '<button type="button" class="btn btn-xs btn-info noProp">'.$this->Ops->offsetToFriendly($taskTO, $taskTO_type).' <i class="fa fa-clock-o"></i>&nbsp;</button>';    
                                            }
                                            elseif ($taskTO_type == -2 || $taskTO_type == 2) {
                                                echo '<button type="button" class="btn btn-xs btn-info noProp"><i class="fa fa-clock-o"></i>&nbsp;'.$this->Ops->offsetToFriendly($taskTO, $taskTO_type).'</button>';    
                                            }
                                        }
                                        else if(($isTimeControlled) && ($taskTO == 0)){
                                            echo ($taskTO_type == -1 || $taskTO_type == 1)? '<button type="button" class="btn btn-xs btn-info noProp">'.$this->Ops->offsetToFriendly($taskTO, $taskTO_type).' <i class="fa fa-clock-o"></i>&nbsp;</button>':'<button type="button" class="btn btn-xs btn-info noProp"><i class="fa fa-clock-o"></i>&nbsp;'.$this->Ops->offsetToFriendly($taskTO, $taskTO_type).'</button>';    
                                        }
                                            
                                        echo '<span class="taskTs"><span class="tsCheck id="hide'.$task['Task']['id'].'" data-tid="'.$task['Task']['id'].'" /><span class="taskTimeshift" data-taskid="hide'.$task['Task']['id'].'"></span></span>';
                                    
            /*
             *                             <!--<div class="btn-group" role="group">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                              <li><a href="#">Dropdown link</a></li>
                                              <li><a href="#">Dropdown link</a></li>
                                            </ul>
                                        </div>-->
             * 
             */                        
                                    ?> 
                                    </div>
                                </div>
                            </div>
                            
                        <?php if($isTimeshiftable):?>
                            <div class="pull-right task-timeShift <?php echo (!$timeshift_mode)?'hide':'';?>">
                                <div class="tsButtonWrap">
                                    <div class="row xs-bot-marg">
                                        <div class="col-xs-12">
                                            <span><i class="fa fa-clock-o"></i> <b>Shift Task</b></span><br/>
                                            <div class="input-group xs-bot-marg">
                                                <input type="number" min="-120" max="120" class="form-control input-sm tsInputMag noProp" value="0">
                                                <span class="input-group-btn"><button type="button" class="btn btn-primary btn-sm noProp tsUnitBtn"><?php echo $timeshift_unit ?></button></span>
                                            </div>
                                        </div>
                                        <div class="col-xs-12"><button type="button" class="btn btn-block btn-success btn-sm noProp tsSaveBtn"><i class="fa fa-save fa-lg"> </i> Save</button></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif;?>
                        </div>
                    </div><!--row-->
                    <div class="taskLinkages"><?php 
                        if (!empty($task['Parent']['id'])): ?>
                            <div class="row xs-bot-marg sm-top-marg">
                                <div class="col-md-3"><h5><?php echo ($task['Parent']['id'] && ($task['Task']['time_control']==1))? '<i class="fa fa-history"></i>&nbsp;<b>Synced To</b>':'<i class="fa fa-link"></i>&nbsp; <b>Linked To</b>';?></h5></div>
                                <div class="col-md-9"><?php echo $this->Ops->subtaskRowSingleWithOffset($task['Parent']);?></div>
                            </div>
                        <?php endif; 
                        if (!empty($task['Assist'])):
                            $ass = Hash::combine($task['Assist'], '{n}.id', '{n}','{n}.time_control');
            
                            if(isset($ass[1])){
                                // Re-sort Time Controlled tasks by offset to highlight timing (before, synced, after)
                                $arrAss = array();
                                
                                foreach($ass[1] as $k => $v){
                                    $weight = 0;
                                    // "most before" the task should be first in an ascending list - set weights then do sort
                                    switch ($v['time_offset_type']) {
                                        case -1:
                                            $weight = -1;
                                            break;
                                        case -2:
                                            $weight = 500;
                                            break;
                                        case 2:
                                            $weight = 5000;
                                            break;
                                        default:
                                            $weight = 1;                                                            
                                            break;
                                    }
                                    
                                   $v['time_sort'] = (int)$v['time_offset']*$weight;
                                    $arrAss[$k] =  $v;
                                }
                                $tc_tasks = Hash::sort($arrAss, '{n}.time_sort', 'asc');
                                unset($arrAss);
                        ?>
                                <div class="row xs-bot-marg sm-top-marg">
                                    <div class="col-md-3"><h5><i class="fa fa-clock-o"></i>&nbsp;<b>Synced Tasks</b></h5></div>
                                    <div class="col-md-9">
                                        <?php
                                            foreach($tc_tasks as $tid => $tsk){
                                                echo $this->Ops->subtaskRowSingleWithOffset($tsk);
                                            }
                                        ?>
                                    </div>
                                </div>
                            <?php
                            }
                            if(isset($ass[0])):?>
                                <div class="row xs-bot-marg sm-top-marg">
                                    <div class="col-md-3"><h5><i class="fa fa-sitemap"></i>&nbsp; <b>Linked Tasks</b></h5></div>
                                    <div class="col-md-9">
                                        <?php
                                            foreach($ass[0] as $tid => $tsk){
                                                echo $this->Ops->subtaskRowSingleWithOffset($tsk);
                                            }
                                        ?>
                                    </div>
                                </div>
                            <?php
                            endif;
                        endif;?>
                    </div>
                </div>
                <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>" style="display:none;"></div>    
            </div>
            <!--END individual task-->
        </div>
    </div>
    <?php
        $last_t_day = $cur_t_day;
        $last_t_hr = $cur_t_hr;
        $last_mod_day = $cur_mod_day;
        $task_counter++;
        endforeach; 
    
        echo '<br/>';
    
    if(!$single_task && $this->Paginator->param('current')): ?>
        <p><small><?php  echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'))); ?></small></p>
        <ul class="pagination hidden-print">
            <?php
                echo $this->Paginator->prev('< ' . __($prev_lab), array('tag' => 'li'), null, array('class' => 'pagPrev disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'class'=>'pagNum', 'tag' => 'li', 'currentClass' => 'disabled'));
                echo $this->Paginator->next(__($next_lab) . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
            ?>
        </ul><!-- /.pagination -->
        <div class="pageNum" id="pageNum" style="visibility:hidden;"><?php echo $this->Paginator->param('page');?></div>
    <?php endif;
    }
else{// No tasks
        if($single_task > 0){?>
            <div class="alert alert-danger" role="alert" style="margin-top:20px;margin-bottom:250px;">
                <div class="row">
                    <div class="col-md-8"><i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>Task Not Found! </b> The task you requested was not found. It may have been deleted. Please verify the task ID.</div>
                    <div class="col-md-4"><a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'compile'))?>" class="btn btn-default ai_hidden pull-right"><i class="fa fa-gears"></i> Back to Compiled Tasks</a></div>
                </div>
            </div>
        <?php 
        }
        else{
            ?>
            <div style = "margin-top: 20px; margin-bottom: 80px;" class="alert alert-danger" role="alert">
                <p><i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>No Tasks Found! </b> No tasks matched your search parameters.  Please try modifying your Compile Options or search term.</p>
            </div>
        <?php 
        } 
    }
}

?>

    <div class="singleTask" id="singleTask" style="visibility:hidden;"><?php echo ($single_task)?:0;?></div>
</div><!-- /.tasks-index -->
 
<div id="taskLegend">
    <?php if($view_type <>2) { echo $this->element('task/task_legend');} ?>
</div>
 
<?php echo $this->Js->writeBuffer(); ?>


