<?php
    if (AuthComponent::user('id')){
        $userTeams = AuthComponent::user('Teams');
    }
    
    //$user_controls = $this->Session->read('Auth.User.Teams');
    $single_task = (isset($single_task))? $single_task:0;
    //debug($single_task);
    $search_term = (isset($search_term))? $search_term:null;
    $start_date = $this->Session->read('Auth.User.Compile.start_date');
    $end_date = $this->Session->read('Auth.User.Compile.end_date');
    $comp_teams = $this->Session->read('Auth.User.Compile.Teams');
    $sort = $this->Session->read('Auth.User.Compile.sort');
    $view_type = $this->Session->read('Auth.User.Compile.view_type');
    $view_links = ($this->Session->read('Auth.User.Compile.view_links'))? 1:0;
    $view_details = ($this->Session->read('Auth.User.Compile.view_details'))? 1:0;
    $view_threaded = ($this->Session->read('Auth.User.Compile.view_threaded'))? 1:0;
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
        'before' => $this->Js->get('#global-busy-indicator')->effect(
            'fadeIn',
            array('buffer' => false)
        ),
        //'before' => $this->Js->get('.csSpinner')->effect('fadeIn', array('buffer' => false)),
        //'before' => $this->Js->get('#global-busy-indicator')->effect('fadeIn', array('buffer' => false)),
        //'before' => $this->Js->get('a.navbar-brand')->getprepend('<span class="tr_spin"><i class="fa fa-cog fa-spin fa-lg"></i> </span>'),
        //'complete' => $this->Js->get('.csSpinner')->effect('fadeOut', array('buffer' => false)),
        'complete' => $this->Js->get('#global-busy-indicator')->effect(
            'fadeOut',
            array('buffer' => false)
        ),
        'url' => array('controller' => 'tasks', 'action' => 'compile','?'=>array('src'=>'compile'))
    ));

    //$cURL = $this->params->here;

    if($single_task == 0){
        $this->Js->buffer("
            var ses_details = ".$view_details.";
            var ses_links = ".$view_links.";

            if(ses_details == 0){
                $('div.divTaskDetails').hide();  
            }
            if(ses_links == 0){
                $('div.taskLinkages').hide();   
            }
            
            $('#coViewLinksBut').prop('disabled', false);
            $('#coViewDetailsBut').prop('disabled', false);
        ");
        
    }
    if($single_task != 0){
        $view_type = 1;
        $this->Js->buffer("
            // Show details right away if viewing single task
            $('#taskListWrap').find('.task-panel-heading').trigger('click');
            
            var tph = $('#taskListWrap').find('.task-panel-heading');
            $('div.isChild').show();
            $('div.divTaskDetails').show();  
            $('div.taskLinkages').show();   
            $('#coViewThreadedBut').prop('disabled', true);
            $('#coViewLinksBut').prop('disabled', true);
            $('#coViewDetailsBut').prop('disabled',true);
        ");
    }
    elseif($view_type == 1){
        $this->Js->buffer("
            var ses_threaded = ".$view_threaded.";
            if(ses_threaded == 1){
                //$('div.isChild').hide();
            }
            $('#coViewThreadedBut').prop('disabled', false);
            $('#coDateRange').attr('disabled', false);
            $('#coSort').attr('readonly', false);
        ");
    }
    elseif($view_type == 10){
        $this->Js->buffer("
            $('#coViewThreadedBut').prop('disabled', true);
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
            
            $('#coViewThreadedBut').prop('disabled', true);
            $('#coDateRange').attr('disabled', true);
            $('#coSort').attr('readonly', true);
        ");
    }    
    else{
        $this->Js->buffer("
            $('#coViewThreadedBut').prop('disabled', true);
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
    $('input.eaStartTime, input.eaEndTime').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD HH:mm:ss', 
    });
    
    
    
    $('.noProp, .csTaskDetails a').on('click', function(e){
        e.stopPropagation();
    });

    $('button.tsUnitBtn').on('click', function(){
        curVal = $(this).text();
        newVal = '';
        
        if(curVal == 'Min'){
            $('button.tsUnitBtn').text('Hr')
            newVal = 'Hr';            
        }
        else if(curVal == 'Hr'){
            $('button.tsUnitBtn').text('Sec');
            newVal = 'Sec';
        }
        else if(curVal == 'Sec'){
            $('button.tsUnitBtn').text('Min');
            newVal = 'Min';
        }
        
        $.ajax({
            url: '/users/setTimeshiftUnit/'+newVal,
            type: 'post',
        });
        
    });
    
    $('button.tsSaveBtn').on('click', function(){
        thisBtn = $(this);
        tsMagIn = $(this).closest('.task-timeShift').find('input');
        tsUnitBtn = $(this).closest('.task-timeShift').find('button.tsUnitBtn');
        tsUnitBtnVal = tsUnitBtn.text();
        
        taskid = $(this).closest('.task-panel').data('taskid');
        
        var shift_secs = 0;
        
        if(tsUnitBtnVal == 'Sec'){
            shift_secs = tsMagIn.val();    
        }
        else if(tsUnitBtnVal == 'Min'){
            shift_secs = 60*tsMagIn.val();
        }
        else if(tsUnitBtnVal == 'Hr'){
            shift_secs = 60*60*tsMagIn.val();
        }
        
        if(taskid > 0 && shift_secs != 0){
            $.ajax({
                url: '/tasks/timeshiftTask',
                data: {'task_id':taskid, 'secs':shift_secs},
                type: 'post',
                beforeSend:function () {
                    thisBtn.prop('disabled', true);
                    thisBtn.html('<i class=\"fa fa-cog fa-spin fa-lg\"></i> Saving...');
                },
                success: function(){
                    $('#taskListWrap').load('/tasks/compile?src=ajax', function(response, status, xhr){
                        if(status == 'success'){
                            $('#taskListWrap').html(response);
                        }
                    });
                }
            });
        }
    });    

    $('.alert-success').delay(3000).fadeOut('fast');
    
    ");

    // Variables for what's currently being shown
    $viewTeams = array();
    $viewMessage = $viewRange = $viewTeamsStr = '';
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
        $viewMessage = 'Viewing Event Day timeline for <b>'.$viewTeamStr.'</b> ordered by <b>'.$viewSort.'.</b>';    
    }
    // Lead Only
    elseif($view_type == 10){
        $viewMessage = 'Viewing tasks lead by <b>'.$viewTeamStr.'</b> from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by <b>'.$viewSort.'.</b>';
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
    }
    // Recently Modified
    elseif($view_type ==  100){
        $viewMessage = 'Viewing <b>recently modified</b> tasks involving <b>'.$viewTeamStr.'</b> from <b>ANY</b> date, ordered by <b>descending modified date.</b> (i.e most recent first)';
    }
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
        echo '<i class="fa fa-gears"></i> &nbsp;Compiled Tasks (Rundown)'; 
    }
    elseif($view == 2){
        echo '<i class="fa fa-tasks"></i> &nbsp;Event Day Timeline'; 
    //debug('hi');
    }
    

    
    elseif($view == 10){
        echo '<i class="fa fa-bookmark-o"></i> &nbsp;Compiled Tasks (Lead Only)'; 
    }
    elseif($view == 30){
        echo '<i class="fa fa-life-saver"></i> &nbsp;Open Requests (Owing)'; 
    }
    elseif($view == 31){
        echo '<i class="fa fa-hourglass-half"></i> &nbsp;Open Requests (Waiting)'; 
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
    <div class="row">
        <div class="col-md-12">
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
        </div>
    </div>

<?php
//View 2 ==> Hourly 
if ($view == 2 && empty($single_task)){
    //echo "got 2";
    //debug($tasks);

    $userTLhr = AuthComponent::user('Timeline.hour');
    
    $hour = ($userTLhr>=0 && $userTLhr <=30)? $userTLhr: 6;
    
    
    
    //debug($timeline_hr);
    //echo $this->element('/task/compile_by_role', array(
    echo $this->element('/task/compile_hourly', array(
    'hour'=>$hour,
    'tasks' => $tasks,
    //'timeline_hr'=>$timeline_hr,
    'timeline_hr'=>$hour,
));    
} 
else{

    if (!empty($tasks)){ ?>
<div class="tasks index">
        
        
    
        
<?php if(!$single_task && $this->Paginator->param('current')):?>
    <div class="row hidden-print">
        <div class=" col-xs-12" style="margin-bottom: -10px;">
            <div style="text-align:right; margin-bottom:-40px;">
                <div>
                    <ul class="pagination" style="margin-top:0px; margin-left: auto; margin-right:auto">
                    <?php
                        if($view_type == 100){
                            $prev_lab = 'Newer';
                            $next_lab = 'Older';
                        }
                        else{
                            $prev_lab = 'Earlier';
                            $next_lab = 'Later';    
                        }
                        //echo '<span class="csSpinner" style="display: none; margin-left: 5px;">'.$this->Html->image('ajax-loader_old.gif').'</span>'; 
                        //echo '<span class="csSpinner" style="display: none; margin-left: 5px;"><i class="fa fa-cog fa-spin fa-2x"></i></span>';
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

    /******************************
     *  START of FOREACH $tasks   *          
     ******************************/
    foreach ($tasks as $k => $task):
        $inUsrShift = $userControls = $uControlsInvolved = $hasComment = $commentCount = $hasDueDate = $hasDueSoon = $hasActionable = $hasChange = $hasNewChange = $hasAssignment = $actor = false;
        $hoursAreSame = $daysAreSame = $modDaysAreSame = $onEday = $isPastDue = $isTimeshiftable = $isTimeControlled = false;
        $teamsInvolved = array();
        
        $tid = $task['Task']['id']; 
        $taskTO = $taskTO_type = 0;
        //if(isset($task['TasksTeam'])){
            $teamsInvolved = Hash::extract($task['TasksTeam'],'{n}.team_id');    
        //} 
        
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
     <div <?php
            if(!empty($task['Assist']) || !empty($task['Task']['parent_id']) || ($task['Task']['actionable_type_id'] > 300)){
                echo 'class = "';
                if (!empty($task['Task']['parent_id'])){
                    echo "isChild ";    
                }
                if($task['Task']['actionable_type_id'] > 300){
                    echo "aiCompleted";
                }
            echo '"';}?>>    
    
    <div class="row <?php echo ($isPastDue)? 'past_due':'';?>">
        <div class="col-xs-12">
        <?php
            // For recently modified, use "modified date" as basis for grouping tasks for display
            if($view_type == 100 && !$single_task && !$modDaysAreSame){
                echo '<h4 class="great">';
                if($cur_mod_day != $today){
                    echo $this->Time->timeAgoInWords($cur_mod_day, array('accuracy' => array('day' => 'day'), 'end' => '1 week', 'format' => 'M d'));    
                }
                else{ echo 'Today'; }
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

    <div class="row">
        <div class="col-md-12">
            <div data-taskid="<?php echo ($task['Task']['id']); ?>" id="tid<?php echo ($task['Task']['id']); ?>" class="panel panel-default task-panel" style="border-left: 7px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>">
                <div class="panel-heading task-panel-heading" data-team_id = "<?php echo $task['Task']['team_id'];?>" data-stime="<?php echo $task['Task']['start_time'];?>" data-uconinv="<?php echo ($uControlsInvolved)?'true':'false';?>" data-tid="<?php echo ($task['Task']['id']); ?>">
                    <span class="collapse jsonCIN" style="visibility: hidden;"><?php echo $jsonCIN; ?></span>
                    <div class="row">
                        <div class="col-xs-3 col-sm-2 col-md-2">
                            <div class="taskTs checkbox facheckbox xs-bot-marg facheckbox-circle facheckbox-success">
                                <input type="checkbox" class="tsCheck <?php if($inUsrShift){echo 'checked';} ?>" id="hide<?php echo $tid;?>" <?php if(!$userControls || $isTimeControlled){echo 'disabled="disabled"';} ?> <?php if($inUsrShift){echo 'checked="checked"';} ?> 
                                data-tid="<?php echo $task['Task']['id']?>"/>
                                <label class="taskTimeshift" for="hide<?php echo $tid;?>"><?php 
                                        if($view_type != 100){
                                            echo $this->Ops->startTimeFriendly($task['Task']['start_time'], $task['Task']['end_time'], array('duration'=>true));
                                        
                                        }
                                        else { // When showing by created date, must show task date since overall tasks are sorted by created date (not task date)
                                            echo date('M j g:i A', strtotime($task['Task']['start_time']));
                                        }
                                    ?>
                                    </label>
                                    
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 sm-bot-marg">
                            <?php echo '<b>'.$task['Task']['task_type'].'</b><br/>'.$this->Ops->makeTeamsSig($task['TasksTeam'], $zoneTeamCodeList, $userControls);?> 
                        </div>
                        <div class="col-xs-6 col-sm-7 col-md-5">
                            
                            <div class="csTaskDetails"><?php
                                    echo $task['Task']['short_description'].'<br/>';
                                    if (!empty($task['Task']['details'])){
                                        echo '<div class="divTaskDetails">';
                                        echo '<hr align="left" style="width: 98%; margin-bottom:0.5em; margin-top:0.5em; border-top: 1px solid #aaa;"/>';
                                        echo nl2br($task['Task']['details']);
                                        echo '</div>';
                                    }else{echo '';} 
                                    
                                    if($hasAssignment){
                                        foreach($task['Assignment'] as $n =>$ass){
                                            echo '<button type="button" class="btn btn-yh btn-xs xs-bot-marg xs-right-marg noProp">';
                                            echo '<i class="fa fa-at"></i>&nbsp;'.$ass['role_handle'].'</button>';    
                                            
                                        }
                                    }
                                    
                                    ?>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-2">
                            <div class="row">
                                <div class="col-md-1 pull-right">
                                    <div class="pull-right task-buttons <?php if($isTimeshiftable){echo 'canCollapse';};?>" style="text-align: right; margin-left: 5px;">
                                    <?php 
                                        if($hasDueDate){
                                            //echo ($hasDueSoon)? '<button type="button" class="btn btn-danger btn-xs xs-bot-marg xs-right-marg noProp">':'<button type="button" class="btn btn-warning btn-xs xs-bot-marg">';
                                            echo '<button type="button" class="btn btn-danger btn-xs xs-bot-marg xs-right-marg noProp">';
                                            echo '<i class="fa fa-bell-o"></i>&nbsp;'.$this->Time->format('M d', $task['Task']['due_date']).'</button>';
                                        }
                                        if($hasActionable){
                                            echo '<button type="button" class="btn btn-danger btn-xs xs-bot-marg xs-right-marg noProp">';
                                            echo '<i class="fa fa-flag fa-lg"></i>&nbsp;'.$task['Task']['actionable_type'].'</button>';
                                        }
                                        if($hasChange && $hasNewChange){
                                            echo '<button type="button" class="btn btn-success btn-xs xs-bot-marg xs-right-marg noProp">';
                                            echo '<i class="fa fa-exchange"></i>&nbsp;'.$numChange.' New</button>';
                                            //echo '<i class="fa fa-exchange"></i>&nbsp;'.$numChange.'</button>';    
                                        }
                                        if($hasComment){
                                            echo '<button type="button" class="btn btn-primary btn-xs xs-bot-marg xs-right-marg noProp">';
                                            echo '<i class="fa fa-comment-o"></i>&nbsp;'.$commentCount.' New</button>';    
                                            //echo '<i class="fa fa-comment-o"></i>&nbsp;'.$commentCount.'</button>';
                                        }
                                        if(count($task['Assist']) > 0 || $task['Task']['parent_id'] > 0){
                                            $lnkCnt = count($task['Assist']);
                                            if(!empty($task['Task']['parent_id'])){ $lnkCnt++; }
                                            $lstr = ($lnkCnt > 1 )? 'Links': 'Link';
                                            //$lstr ='';
                                            echo '<button type="button" class="btn btn-default btn-xs xs-bot-marg xs-right-marg noProp">';
                                            echo '<i class="fa fa-link"></i>&nbsp;'.$lnkCnt.' '.$lstr.'</button>';    
                                            //echo '<i class="fa fa-link"></i>&nbsp;'.$lnkCnt.'</button>';
                                        }
                                        if(($isTimeControlled) && ($taskTO != 0)){
                                            if($taskTO_type == -1 || $taskTO_type == 1){
                                                echo '<button type="button" class="btn btn-orange btn-xs xs-bot-marg xs-right-marg noProp">';
                                                echo $this->Ops->offsetToFriendly($taskTO, $taskTO_type).' <i class="fa fa-clock-o"></i>&nbsp;</button>';    
                                            }
                                            elseif ($taskTO_type == -2 || $taskTO_type == 2) {
                                                echo '<button type="button" class="btn btn-orange btn-xs xs-bot-marg xs-right-marg noProp">';
                                                echo '<i class="fa fa-clock-o"></i>&nbsp;'.$this->Ops->offsetToFriendly($taskTO, $taskTO_type).'</button>';    
                                            }
                                        }
                                        else if(($isTimeControlled) && ($taskTO == 0)){
//                                            echo '<button type="button" class="btn btn-primary btn-xs xs-bot-marg xs-right-marg noProp">';
                                            echo '<button type="button" class="btn btn-orange btn-xs xs-bot-marg xs-right-marg noProp">';
                                            echo '<i class="fa fa-clock-o"></i>&nbsp;'.$this->Ops->offsetToFriendly($taskTO, $taskTO_type).'</button>';    
                                        }
                                    
                                    ?>
                                    </div> 
                                </div>
                            </div>
                            
                            <?php if($isTimeshiftable):?>
                            <div class="pull-right task-timeShift <?php echo (!$timeshift_mode)?'hide':'';?>">
                                <div class="tsButtonWrap">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <span><i class="fa fa-clock-o"></i> <b>Shift Task</b></span>
                                        </div>
                                    </div>
                                    <div class="row xs-bot-marg">
                                        <div class="col-xs-6 col-md-12">
                                            <div class="input-group xs-bot-marg">
                                                <input type="number" min="-120" max="120" class="form-control input-sm tsInputMag noProp" value="0">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-primary btn-sm noProp tsUnitBtn"><?php echo $timeshift_unit ?></button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-md-12">
                                            <button type="button" class="btn btn-block btn-success btn-sm noProp tsSaveBtn"><i class="fa fa-save fa-lg"> </i> Save</button>   
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif;?>
                        </div>
                    </div>
                    <div class="taskLinkages"><?php 
                        if (!empty($task['Parent']['id'])): ?>
                            <div class="row xs-bot-marg sm-top-marg">
                                <div class="col-md-2">
                                    <h5 class="pull-right">
                                    <?php 
                                        $relType = ($task['Parent']['id'] && ($task['Task']['time_control']==1))? '<i class="fa fa-clock-o"></i>&nbsp;<b>Synced To</b>':'<i class="fa fa-external-link-square"></i>&nbsp; <b>Linked To</b>';
                                        echo $relType;
                                    ?>
                                    </h5>  
                                </div>
                                <div class="col-md-10"><?php echo $this->Ops->subtaskRowSingle($task['Parent']); ?></div>
                            </div>
                        <?php endif; 
                        //debug($task);
                        if (!empty($task['Assist'])):
                            
                            $ass = Hash::combine($task['Assist'], '{n}.id', '{n}','{n}.time_control');
    
    
                            if(isset($ass[1])){
                                // Re-sort Time Controlled tasks by offset to highlight timing (before, synced, after)
                                
                                $arrAss = array();
                                
                                foreach($ass[1] as $k => $v){
                                    $sign = 0;
                                    // "most before" the task should be first in an ascending list
                                    if($v['time_offset_type'] == -1){
                                        $sign = -1;
                                    }
                                    //forcing these lower as they're linked to END of the task
                                    elseif($v['time_offset_type'] == -2){
                                        $sign = 500;
                                    }
                                    elseif($v['time_offset_type'] == 2){
                                        $sign = 5000;
                                    }
                                    else{
                                        $sign = 1;
                                    }
                                    $v['time_sort'] = (int)$v['time_offset']*$sign;
                                    $arrAss[$k] =  $v;
                                }
                                $tc_tasks = Hash::sort($arrAss, '{n}.time_sort', 'asc');
                                unset($arrAss);
                                //$tc_tasks = Hash::sort($ass[1], '{n}.time_offset', 'asc');
                                //debug($tc_tasks);
                        ?>
                                <div class="row xs-bot-marg sm-top-marg">
                                    <div class="col-md-2">
                                        <h5 class="pull-right"><i class="fa fa-clock-o"></i>&nbsp;<b>Synced Tasks</b></h5>
                                    </div>
                                    <div class="col-md-10">
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
                                    <div class="col-md-2">
                                        <h5 class="pull-right"><i class="fa fa-sitemap"></i>&nbsp; <b>Linked Tasks</b></h5>
                                    </div>
                                    <div class="col-md-10">
                                        <?php
                                            foreach($ass[0] as $tid => $tsk){
                                                echo $this->Ops->subtaskRowSingle($tsk);
                                            }
                                        ?>
                                    </div>
                                </div>
                            <?php
                            endif;
                        endif;?></div>
                        
                
                </div>
                <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>" style="display:none;"></div>    
                </div>
            </div>
        </div>
    </div>
    <?php
        $last_t_day = $cur_t_day;
        $last_t_hr = $cur_t_hr;
        $last_mod_day = $cur_mod_day;
        endforeach; 
    
    echo '<br/>';
        
    
    if(!$single_task && $this->Paginator->param('current')): 
    ?>
        <p><small><?php  echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'))); ?></small></p>
        <ul class="pagination hidden-print">
            <?php
                echo $this->Paginator->prev('< ' . __($prev_lab), array('tag' => 'li'), null, array('class' => 'pagPrev disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'class'=>'pagNum', 'tag' => 'li', 'currentClass' => 'disabled'));
                echo $this->Paginator->next(__($next_lab) . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                //echo '<span class="csSpinner" style="display: none; margin-left: 5px;"><i class="fa fa-cog fa-spin fa-2x"></i></span>';
                 
            ?>
        </ul><!-- /.pagination -->
        <div class="pageNum" id="pageNum" style="visibility:hidden;"><?php echo $this->Paginator->param('page');?></div>
    <?php
    endif;
    }
else{// No tasks
    
    if($single_task > 0){?>
        <div class="alert alert-danger" role="alert" style="margin-top:20px;margin-bottom:250px;">
            <div class="row">
                <div class="col-md-8">
                    <i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>Task Not Found! </b> The task you requested was not found. It may have been deleted. Please verify the task ID.        
                </div>
                <div class="col-md-4">
                    <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'compile'))?>" class="btn btn-default ai_hidden pull-right">
                        <i class="fa fa-gears"></i> Back to Compiled Tasks                                   
                    </a>        
                </div>
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
<span style="visibility: hidden" id ="coViewThreaded"><?php echo $view_threaded;?></span>
<span style="visibility: hidden" id ="coViewLinks"><?php echo $view_links;?></span>
<span style="visibility: hidden" id ="coViewDetails"><?php echo $view_details;?></span>
<div class="singleTask" id="singleTask" style="visibility:hidden;"><?php echo ($single_task)?:0;?></div>

</div><!-- /.index -->
 
 
       <div id="taskLegend">
            <?php if($view_type <>2) {
                echo $this->element('task/task_legend');
            } ?>
        </div>
 
<?php echo $this->Js->writeBuffer(); ?>


